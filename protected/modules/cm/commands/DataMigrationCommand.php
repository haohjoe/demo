<?php
ini_set('memory_limit', '1024M');

/**
 * 用户c币mongodb迁移到mysql
 */
class DataMigrationCommand extends CConsoleCommand
{
    private $mongoDbs = array();
    private $mysqlDbs = array();
    private $solutions = array();

    public function __construct()
    {
        $this->solutions = Yii::app()->params['solutions'];
        foreach ($this->solutions as $solution) {
            $this->mysqlDbs[$solution] = Yii::app()->getComponent('db.relational.cm.' . $solution . '.write');
            $this->mongoDbs[$solution] = Yii::app()->getComponent('db.cm.' . $solution);
        }
    }

    /**
     * 数据覆盖式迁移 用户积分
     * ./yiic --module=cm DataMigration exeCoverUserCpoint
     */
    public function actionExeCoverUserCpoint()
    {
        // 循环处理每个解决方案
        foreach ($this->solutions as $solution) {
            $mongoDb = new DaoUserCpoint($solution);
            $mysqlDb = new DaoUserCpointV2($solution);

            $cursor = $mongoDb->find();
            $n = 0;
            foreach ($cursor as $k => $v) {
                $n ++;
                $cpoint = floatval($v['cpoint']);
                $time = intval($v['u_time']);

                $table = DaoUserCpointV2::getTable($k);
                $sql = 'SELECT `uid`,`cpoint` FROM `' . $table . '` WHERE `uid`=:uid LIMIT 1';
                $params[':uid'] = $k;
                // mysql中记录查询.
                $record = $mysqlDb->queryRow($table, $sql, $params);
                if ($record) { // 有记录更新
                    if ($record['cpoint'] == $cpoint) {
                        echo '用户 ' . $k . ' ' . $n . ' 无需更新！' . PHP_EOL;
                        continue;
                    }
                    $fields = array(
                        'cpoint' => $cpoint,
                        'u_time' => $time
                    );
                    $condition = '`uid`=:uid';
                    $uParams = array(
                        ':uid' => $k
                    );
                    if ($mysqlDb->update($table, $fields, $condition, $uParams)) {
                        echo '更新用户 ' . $k . ' ' . $n . ' 成功！' . PHP_EOL;
                    }
                } else { // 无记录插入
                    $iArr = array(
                        'uid' => $k,
                        'cpoint' => $cpoint,
                        'u_time' => $time
                    );
                    if ($mysqlDb->insert($table, $iArr)) {
                        echo '插入用户 ' . $k . ' ' . $n . ' 成功！' . PHP_EOL;
                    } else {
                        echo '插入用户 ' . $k . ' ' . $n . ' 失败！' . PHP_EOL;
                    }
                }
            }

            echo '已完成' . $solution . '的迁移，共迁移' . $n . '条数据' . PHP_EOL;
        }

        Yii::app()->end();
    }

    /**
     * 数据覆盖式迁移 用户积分流水
     * ./yiic --module=cm DataMigration exeCoverCpointLog --sTime=0 --eTime=1450512868
     */
    public function actionExeCoverCpointLog($sTime = null, $eTime = null)
    {
        $sort = array(
            '_id' => -1
        );
        $query = array();
        if ($sTime !== null) {
            $query['c_time']['$gt'] = UtilHelper::float2MongoDate(floatval($sTime));
        }
        if ($eTime !== null) {
            $query['c_time']['$lt'] = UtilHelper::float2MongoDate(floatval($eTime));
        }

        // 循环处理每个解决方案
        foreach ($this->solutions as $solution) {
            $mongoDb = new DaoAccountChangeLog($solution);
            $componentId = 'db.relational.cm.' . $solution . '.write';
            $mysqlDb = Yii::app()->getComponent($componentId);
            $drb = new DaoRelationalBase($componentId);

            $cursor = $mongoDb->find($query)->sort($sort);
            $n = 0;
            foreach ($cursor as $k => $v) {
                if ($v['type'] != DaoAccountChangeLog::TYPE_CPOINT) {
                    continue;
                }
                $uid = $v['uid']->__toString();
                $cpoint = floatval($v['amount']);
                $time = intval(UtilHelper::mongoDate2Float($v['c_time']));
                $month = intval(date('Ym', $time));
                $option = $v['op'];
                $type = DaoCpointLog::TYPE_DONE_TASK;
                $appname = ! empty($v['appname']) ? $v['appname'] : ($solution == 'c360') ? 'photoTask' : 'CameraCircle';
                $binLog = '';

                $transaction = $mysqlDb->beginTransaction();
                try {
                    // 插入cpoint_log_x
                    $table2 = DaoCpointLog::getTable($uid);
                    $sql2 = "INSERT INTO `{$table2}`
                    (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`)
                    VALUES ('{$option}','{$uid}',{$cpoint},{$month},{$time},'{$appname}',{$type})";
                    $drb->execute($table2, $sql2, array(), true);

                    // 插入re_cpoint_log_x
                    $table3 = DaoReCpointLog::getTable($month);
                    $sql3 = "INSERT INTO `{$table3}`
                    (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`,`bin_log`)
                    VALUES ('{$option}','{$uid}',{$cpoint},{$month},{$time},'{$appname}',{$type},'{$binLog}')";
                    $drb->execute($table3, $sql3, array(), true);

                    // 提交事务
                    $transaction->commit();
                    echo '事物执行成功 ' . $uid . ' ' . $k . ' ' . $n . PHP_EOL;
                } catch (Exception $e) {
                    echo '事物执行失败 ' . $uid . ' ' . $k . ' ' . $n . PHP_EOL;
                    $transaction->rollBack();
                }
                $n ++;
            }

            echo '已完成' . $solution . '的迁移，共迁移' . $n . '条数据' . PHP_EOL;
        }

        Yii::app()->end();
    }

    /**
     * 数据校验
     * ./yiic --module=cm DataMigration exeCheck
     * @todo 应该校验失败后记录并且在当前程序完成第一次循环后进行重试失败记录的校验.
     */
    public function actionExeCheck()
    {
        $filePrefix = '/home/worker/data/www/runtime/member/checkDataMigrationFail.';

        foreach ($this->solutions as $solution) {
            $mongoDb = new DaoUserCpoint($solution);
            $mysqlDb = new DaoUserCpointV2($solution);

            $n = 0;
            $file = $filePrefix . $solution;
            $fails = array();
            file_put_contents($file, '');
            $cursor = $mongoDb->find();
            foreach ($cursor as $k => $v) {
                $cpoint = floatval($v['cpoint']);

                $table = DaoUserCpointV2::getTable($k);
                $sql = 'SELECT * FROM `' . $table . '` WHERE `uid`=:uid LIMIT 1';
                $params[':uid'] = $k;
                $record = $mysqlDb->queryRow($table, $sql, $params);
                echo $k . ' ' . $record['cpoint'] . ' =? ' . $cpoint . ' ' . $n . PHP_EOL;
                if (empty($record) || $record['cpoint'] != $cpoint) {
                    $fails[] = $k; // check失败了的.
                    echo '数据检验失败' . $solution . ' uid=' . $k . PHP_EOL;
                    file_put_contents($filePrefix . $solution, $k . PHP_EOL, FILE_APPEND);
                }
                $n ++;
            }
            echo '已完成' . $solution . '的校验，共校验' . $n . '条数据' . PHP_EOL;
            if (! empty($fails)) {
                sleep(30); // 防止在检查时同时有数据更新导致影响检查结果
                foreach ($fails as $fail) {
                    $tb = DaoUserCpointV2::getTable($fail);
                    $sql1 = 'SELECT * FROM `' . $tb . '` WHERE `uid`=:uid LIMIT 1';
                    $ps[':uid'] = $fail;
                    $record = $mysqlDb->queryRow($tb, $sql1, $ps);
                    $src = $mongoDb->findOne(array('_id' => $fail));
                    if (empty($record) || empty($src) || $record['cpoint'] != $src['cpoint']) {
                        echo '重新检查' . $fail . '失败！' . PHP_EOL;
                    } else {
                        echo '重新检查' . $fail . '成功！' . PHP_EOL;
                    }
                }
            }
        }

        Yii::app()->end();
    }

    /**
     * @throws Exception
     * ./yiic --module=cm DataMigration RepairData
     */
    public function actionRepairData()
    {
        $o1 = new DaoReCpointLog('c360');
        $o2 = new DaoCpointLog('c360');
        $t1 = 're_cpoint_log_201512';

        // 修复201512月份得数据
        $sql0 = 'SELECT * FROM `re_cpoint_log_201512` WHERE `rec_id`="watchTask" AND `bin_log`="" AND `add_time`>1450512868';
        $records = $o1->queryAll($t1, $sql0);
        echo 'Total num=' . count($records) . PHP_EOL;
        sleep(10);

        foreach ($records as $record) {
            var_dump(json_encode($record));

            $id = intval($record['id']);
            $uid = $record['uid'];
            $aTime = intval($record['add_time']);

            $t2 = DaoCpointLog::getTable($uid);
            $sql1 = "DELETE FROM `{$t2}` WHERE `uid`='{$uid}' AND `rec_id`='watchTask' AND `add_time`={$aTime} AND `type`=1 LIMIT 1";
            $rst1 = $o2->execute($t2, $sql1);
            $sql2 = "DELETE FROM `{$t1}` WHERE `id`={$id} LIMIT 1";
            $rst0 = $o1->execute($t1, $sql2);
            echo '已修复数据 uid=' . $uid . ',add_time=' . date('Y/m/d H:i', $aTime) . PHP_EOL;
        }
    }
}
