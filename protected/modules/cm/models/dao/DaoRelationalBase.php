<?php

/**
 * dao关系型数据库基类
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/07
 */
class DaoRelationalBase
{
    private $profilename = '';
    protected $db = null;

    public function __construct($componentId)
    {
        $this->db = Yii::app()->getComponent($componentId);
        $this->profilename = $componentId . '.';
    }

    ////////////-=-sql语句封装-=-//////////////

    /**
     * 查询一条
     * @param $table string 表名
     * @param $sql string sql语句
     * @param $params array 绑定参数
     * @return false | array
     */
    public function queryRow($table, $sql, array $params = array(), $throwException = false)
    {
        LogHelper::profileStart($this->profilename . $table . '.' . __FUNCTION__);
        try {
            // queryRow未找到记录、表不存在等返回false
            $row = $this->db->createCommand($sql)->bindValues($params)->queryRow();
            if ($row === false) { // 未找到记录
                return array();
            }
        } catch (Exception $e) { // 表不存在时会抛异常.
            LogHelper::error('Mysql queryRow failed with sql=' . $sql . ',params=' . json_encode($params) . ',errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            if ($throwException) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            return false;
        }
        LogHelper::profileEnd($this->profilename . $table . '.' . __FUNCTION__);

        return $row;
    }

    /**
     * 查询多条
     * @param $table string 表名
     * @param $sql string sql语句
     * @param $params array 绑定参数
     * @return array | false
     */
    public function queryAll($table, $sql, array $params = array(), $throwException = false)
    {
        LogHelper::profileStart($this->profilename . $table . '.' . __FUNCTION__);
        try {
            // 未找到记录、表不存在等返回array()
            $rows = $this->db->createCommand($sql)->bindValues($params)->queryAll();
        } catch (Exception $e) {
            LogHelper::error('Mysql queryAll failed with sql=' . $sql . ',params=' . json_encode($params) . ',errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            if ($throwException) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            return false;
        }
        LogHelper::profileEnd($this->profilename . $table . '.' . __FUNCTION__);

        return $rows;
    }

    /**
     * 执行
     * @param $table string 表名
     * @param $sql string sql语句
     * @param $params array 绑定参数
     * @return int(受影响行) | false
     */
    public function execute($table, $sql, array $params = array(), $throwException = false)
    {
        LogHelper::profileStart($this->profilename . $table . '.' . __FUNCTION__);
        try {
            $eRows = $this->db->createCommand($sql)->execute($params);
        } catch (Exception $e) {
            LogHelper::error('Mysql execute failed with sql=' . $sql . ',params=' . json_encode($params) . ',errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            if ($throwException) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            return false;
        }
        LogHelper::profileEnd($this->profilename . $table . '.' . __FUNCTION__);

        return $eRows;
    }

    /**
     * 更新
     * @param $table string 表名
     * @param $fields array 要更新的字段
     * @param $condition string 条件
     * @param $params array 绑定参数
     * @return int(受影响行) | false
     */
    public function update($table, $fields, $condition, array $params = array(), $throwException = false)
    {
        LogHelper::profileStart($this->profilename . $table . '.' . __FUNCTION__);
        try {
            $eRows = $this->db->createCommand()->update($table, $fields, $condition, $params);
        } catch (Exception $e) {
            LogHelper::error('Mysql update failed with table=' . $table . ',fields=' . json_encode($fields) . ',condition=' . $condition . ',params=' . json_encode($params) . ',errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            if ($throwException) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            return false;
        }
        LogHelper::profileEnd($this->profilename . $table . '.' . __FUNCTION__);

        return $eRows;
    }

    /**
     * 插入
     * @param $table string 表名
     * @param $iArr array 插入的字段及值
     * @param $getLastId 是否获取最后的id（自增id）
     * @return int(受影响行) | 自增id
     */
    public function insert($table, array $iArr, $getLastId = false, $throwException = false)
    {
        LogHelper::profileStart($this->profilename . $table . '.' . __FUNCTION__);
        try {
            $eRow = $this->db->createCommand()->insert($table, $iArr);
        } catch (Exception $e) {
            LogHelper::error('Mysql insert failed with table=' . $table . ',iArr=' . json_encode($iArr) . ',errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            if ($throwException) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            return false;
        }
        LogHelper::profileEnd($this->profilename . $table . '.' . __FUNCTION__);

        if ($eRow > 0 && $getLastId) {
            return $this->db->getLastInsertID();
        }

        return $eRow;
    }

    ////////////-=-涉及多个表的事务-=-//////////////

    /**
     * 完成任务、后台管理员，添加c币事物
     * @param $appname string app name
     * @param $uid string 用户uid
     * @param $cpoint float c币
     * @param $action string 任务option、其他定义的action
     * @param $isNew bool 用户c币记录是否要新加
     * @param $binLog string bin log
     * @return boolean
     */
    public function addCpoint($appname, $uid, $cpoint, $action, $isNew = false, $binLog = '', $type = DaoCpointLog::TYPE_DONE_TASK)
    {
        LogHelper::profileStart('addCpoint_Transaction');
        $transaction = $this->db->beginTransaction();
        try {
            $now = UtilHelper::now();
            $month = intval(date('Ym', $now));

            $table1 = DaoUserCpointV2::getTable($uid);
            // if ($isNew) { // 为第一次写入user_cpoint_x时，因为缓存锁的细粒度是任务id，不能保证并发写入导致抛异常.
            //    $sql0 = "SELECT `uid` FROM `{$table1}` WHERE `uid`='{$uid}' LIMIT 1";
            //    $eRow0 = $this->execute($table1, $sql0, array(), true);
            // }
            // if ($isNew && empty($eRow0)) {
            if ($isNew) { // 插入用户积分记录
                // $sql1 = "INSERT INTO `{$table1}` (`uid`,`cpoint`,`u_time`) VALUES ('{$uid}',{$cpoint},{$now})";
                // uid 为unique key时可用此种写法，此时update不能加 LIMIT 1.
                $sql1 = "INSERT INTO `{$table1}` (`uid`,`cpoint`,`u_time`) VALUES ('{$uid}',{$cpoint},{$now}) ON DUPLICATE KEY UPDATE `cpoint`=`cpoint`+{$cpoint}, `u_time`={$now}";
            } else { // 更新用户积分
                $sql1 = "UPDATE `{$table1}` SET `cpoint`=`cpoint`+{$cpoint}, `u_time`={$now} WHERE `uid`='{$uid}' LIMIT 1";
            }
            $eRow1 = $this->execute($table1, $sql1, array(), true);
            if ($eRow1 == 0) { // 无受影响行
                $transaction->rollBack();
                return false;
            }

            // 插入cpoint_log_x
            $table2 = DaoCpointLog::getTable($uid);
            $sql2 = "INSERT INTO `{$table2}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`) VALUES ('{$action}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type})";
            $this->execute($table2, $sql2, array(), true);

            // 插入re_cpoint_log_x
            $table3 = DaoReCpointLog::getTable($month);
            $sql3 = "INSERT INTO `{$table3}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`,`bin_log`) VALUES ('{$action}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type},'{$binLog}')";
            $this->execute($table3, $sql3, array(), true);

            // 提交事务
            $transaction->commit();
            LogHelper::profileEnd('addCpoint_Transaction');
        } catch (Exception $e) { // 如果有一条查询失败，则会抛出异常
            // LogHelper::error('addCpoin_Transaction failed errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            $transaction->rollBack();
            LogHelper::profileEnd('addCpoint_Transaction');
            return false;
        }

        return true;
    }

    /**
     * 后台管理员较少c币
     * @param $appname string app name
     * @param $uid string 用户uid
     * @param $cpoint float c币
     * @param $action string 其他定义的action
     * @param $binLog string bin log
     * @return boolean
     */
    public function reduceCpoint($appname, $uid, $cpoint, $action, $binLog = '')
    {
        LogHelper::profileStart('reduceCpoint_Transaction');
        $transaction = $this->db->beginTransaction();
        try {
            $now = UtilHelper::now();
            $month = intval(date('Ym', $now));
            $type = DaoCpointLog::TYPE_ADMIN_REDUCE_CPOINT;

            $table1 = DaoUserCpointV2::getTable($uid);
            // 更新用户积分
            $sql1 = "UPDATE `{$table1}` SET `cpoint`=`cpoint`-{$cpoint}, `u_time`={$now} WHERE `uid`='{$uid}' AND `cpoint`>={$cpoint} LIMIT 1";
            $eRow1 = $this->execute($table1, $sql1, array(), true);
            if ($eRow1 == 0) { // 无受影响行
                $transaction->rollBack();
                return false;
            }

            // 插入cpoint_log_x
            $table2 = DaoCpointLog::getTable($uid);
            $sql2 = "INSERT INTO `{$table2}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`) VALUES ('{$action}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type})";
            $this->execute($table2, $sql2, array(), true);

            // 插入re_cpoint_log_x
            $table3 = DaoReCpointLog::getTable($month);
            $sql3 = "INSERT INTO `{$table3}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`,`bin_log`) VALUES ('{$action}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type},'{$binLog}')";
            $this->execute($table3, $sql3, array(), true);

            // 提交事务
            $transaction->commit();
            LogHelper::profileEnd('reduceCpoint_Transaction');
        } catch (Exception $e) { // 如果有一条查询失败，则会抛出异常
            // LogHelper::error('addCpoin_Transaction failed errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            $transaction->rollBack();
            LogHelper::profileEnd('reduceCpoint_Transaction');
            return false;
        }

        return true;
    }

    /**
     * 消费c币时（此过程不同于先冻结再支付）事物
     * @param $appname string app name
     * @param $uid string 用户uid
     * @param $cpoint float c币
     * @param $aoId string appname_orderId
     * @param $orderId string 订单id
     * @param $binLog string bin log
     * @return boolean
     */
    public function consumeCpoint($appname, $uid, $cpoint, $aoId, $orderId, $binLog = '')
    {
        LogHelper::profileStart('consumeCpoint_Transaction');
        $transaction = $this->db->beginTransaction();
        try {
            $now = UtilHelper::now();
            $month = intval(date('Ym', $now));
            $status = DaoOrder::STATUS_PAID;    // 已支付
            $type = DaoCpointLog::TYPE_CONSUME; // 消费类型

            // 更新用户积分
            $table1 = DaoUserCpointV2::getTable($uid);
            $sql1 = "UPDATE `{$table1}` SET `cpoint`=`cpoint`-{$cpoint}, `u_time`={$now} WHERE `uid`='{$uid}' AND `cpoint`>={$cpoint} LIMIT 1";
            $eRow1 = $this->execute($table1, $sql1, array(), true);
            if ($eRow1 == 0) { // 无受影响行
                $transaction->rollBack();
                return false;
            }

            // 插入order_x
            $table2 = DaoOrder::getTable($uid);
            $sql2 = "INSERT INTO `{$table2}` (`ao_id`,`uid`,`order_id`,`cpoint`,`add_mon`,`add_time`,`u_time`,`appname`,`status`) VALUES ('{$aoId}','{$uid}','{$orderId}',{$cpoint},{$month},{$now},{$now},'{$appname}',{$status})";
            $this->execute($table2, $sql2, array(), true);

            // 插入cpoint_log_x
            $table3 = DaoCpointLog::getTable($uid);
            $sql3 = "INSERT INTO `{$table3}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`) VALUES ('{$aoId}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type})";
            $this->execute($table3, $sql3, array(), true);

            // 插入re_cpoint_log_x
            $table4 = DaoReCpointLog::getTable($month);
            $sql4 = "INSERT INTO `{$table4}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`,`bin_log`) VALUES ('{$aoId}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type},'{$binLog}')";
            $this->execute($table4, $sql4, array(), true);

            // 提交事务
            $transaction->commit();
            LogHelper::profileEnd('consumeCpoint_Transaction');
        } catch (Exception $e) { // 如果有一条查询失败，则会抛出异常
            // LogHelper::error('Consume cpoint Transaction commit failed with errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            $transaction->rollBack();
            LogHelper::profileEnd('consumeCpoint_Transaction');
            return false;
        }

        return true;
    }

    /**
     * 撤回c币时事物
     * @param $appname string app name
     * @param $uid string 用户uid
     * @param $cpoint float c币
     * @param $aoId string appname_orderId
     * @param $binLog string bin log
     * @return boolean
     */
    public function revokeCpoint($appname, $uid, $cpoint, $aoId, $binLog = '')
    {
        LogHelper::profileStart('revokeCpoint_Transaction');
        $transaction = $this->db->beginTransaction();
        try {
            $now = UtilHelper::now();
            $month = intval(date('Ym', $now));
            $revokeStatus = DaoOrder::STATUS_REVOKE;    // 已撤销
            $paidStatus = DaoOrder::STATUS_PAID;        // 已支付
            $frozenStatus = DaoOrder::STATUS_FROZEN;    // 已支付
            $type = DaoCpointLog::TYPE_REVOKE;          // 消费类型

            // 更新order_x状态(被更改的状态必须为已支付状态)
            $table1 = DaoOrder::getTable($uid);
            $sql1 = "UPDATE `{$table1}` SET `status`={$revokeStatus}, `u_time`={$now} WHERE `ao_id`='{$aoId}' AND (`status`={$paidStatus} OR `status`={$frozenStatus}) LIMIT 1";
            $eRow1 = $this->execute($table1, $sql1, array(), true);
            if ($eRow1 == 0) { // 无受影响行
                $transaction->rollBack();
                return false;
            }

            // 更新用户积分
            $table2 = DaoUserCpointV2::getTable($uid);
            $sql2 = "UPDATE `{$table2}` SET `cpoint`=`cpoint`+{$cpoint}, `u_time`={$now} WHERE `uid`='{$uid}' LIMIT 1";
            $eRow2 = $this->execute($table2, $sql2, array(), true);
            if ($eRow2 == 0) { // 无受影响行
                $transaction->rollBack();
                return false;
            }

            // 插入cpoint_log_x
            $table3 = DaoCpointLog::getTable($uid);
            $sql3 = "INSERT INTO `{$table3}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`) VALUES ('{$aoId}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type})";
            $this->execute($table3, $sql3, array(), true);

            // 插入re_cpoint_log_x
            $table4 = DaoReCpointLog::getTable($month);
            $sql4 = "INSERT INTO `{$table4}` (`rec_id`,`uid`,`cpoint`,`add_mon`,`add_time`,`appname`,`type`,`bin_log`) VALUES ('{$aoId}','{$uid}',{$cpoint},{$month},{$now},'{$appname}',{$type},'{$binLog}')";
            $this->execute($table4, $sql4, array(), true);

            // 提交事务
            $transaction->commit();
            LogHelper::profileEnd('revokeCpoint_Transaction');
        } catch (Exception $e) { // 如果有一条查询失败，则会抛出异常
            // LogHelper::error('revokeCpoin_Transaction failed errCode=' . $e->getCode() . ',errMsg=' . $e->getMessage());
            $transaction->rollBack();
            LogHelper::profileEnd('revokeCpoint_Transaction');
            return false;
        }

        return true;
    }

    /**
     * 冻结c币时事物(冻结c币场所一般用于c币和第三方混合支付用到)
     */
    public function freezeCpoint()
    {
        // 更新用户积分
        // 插入order_x
        // 插入cpoint_log_x
        // 插入re_cpoint_log_x

        return true;
    }

    /**
     * 解冻c币时事物(其实也就是用户创建了订单冻结完c币后，又删除订单，则需要解冻)
     * 解冻的订单必须是状态为已冻结的订单
     */
    public function thawCpoint()
    {
        // 更新用户积分
        // 删除order_x
        // 插入cpoint_log_x
        // 插入re_cpoint_log_x

        return true;
    }

    /**
     * 通知已付款，更新状态事物
     * 此时订单状态必须为冻结中（==未支付）状态
     */
    public function notifyPaid()
    {
        // 更新order_x
        // 插入cpoint_log_x
        // 插入re_cpoint_log_x

        return true;
    }
}
