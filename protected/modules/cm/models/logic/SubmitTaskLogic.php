<?php

/**
 * SubmitTaskLogic class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class SubmitTaskLogic
{
    public $solution;
    public $appName;

    public $objDataUserTask;
    public $objDataUserScore;
    public $objDataUserCpointV2;
    public $objDataUserCpoint;
    public $objDataBase;
    public $objDataCnf;
    public $objDataAccountChangeLog;
    public $objRuleEngine;
    public $objCacheLock;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;

        $this->objDataUserTask = Factory::create('DataUserTask', array($this->solution));
        $this->objDataUserScore = Factory::create('DataUserScore', array($this->solution));
        $this->objDataUserCpointV2 = Factory::create('DataUserCpointV2', array($this->solution));
        $this->objDataUserCpoint = Factory::create('DataUserCpoint', array($this->solution));
        $this->objDataBase = Factory::create('DataBase', array($this->solution, $this->appName));
        $this->objDataAccountChangeLog = Factory::create('DataAccountChangeLog', array($this->solution));
        $this->objRuleEngine = Factory::create('RuleEngine', array($this->solution));
        $this->objDataCnf = Factory::create('DataCnf', array($this->solution));
        $this->objCacheLock = Factory::create('CacheLock', array($this->solution));
    }

    public function execute(array $args)
    {
        $out = array(
            'cpoint' => 0,                      // 新加c点
            'score' => 0,                       // 新加积分
            'newScore' => 0,                    // 最新的积分
            'newCpoint' => 0,                   // 最新c点
            'code' => CmConst::CODE_WRONG_RULE, // 不符合规则，没有完成任务
            'grades' => Yii::app()->params['grade'] // 等级配置
        );

        // @todo 获取用户今日已获取到的c币、经验值. 【防被刷】
        // 识别需要去完成的任务 (识别task，映射op到task)
        $preTasks = $this->objDataCnf->mapOp2Tasks($args['op']);
        if (empty($preTasks)) {
            LogHelper::warning('mapOp2Tasks fail with option:' . $args['op']);
            $out['code'] = CmConst::CODE_MAP_TASK_FAIL;
            return $out;
        }
        $taskIds = array_keys($preTasks);

        // 锁处理.
        $lockeds = array(); // 已成功加锁
        foreach ($taskIds as $tid) { // 对涉及到的每个任务加锁
            $lKey = 'lock_' . $args['uid'] . '_' . $tid;
            $lRst = $this->objCacheLock->addLock($lKey); // 尝试添加锁.
            if ($lRst) { // 成功加锁
                $lockeds[] = $lKey;
            } else { // 某个任务加锁失败
                // 循环释放已锁上的锁.
                $this->releaseLocked($lockeds);
                // if ($tid != 1 && $tid != 17) { // vote的就不抛异常.
                // 加锁失败抛异常.
                // usleep(100000); // 休眠100ms（尽可能减少mq的无节制刷,但会影响同步请求的耗时问题...）
                throw new Exception('Not concurrent execution with key=' . $lKey, Errno::NOT_CONCURRENT_EXE); // 锁添加失败，说明已锁.
                //}
            }
        }

        try {
            // 执行任务.
            $data = $this->doTask($args, $preTasks, $taskIds, $out);
        } catch (Exception $e) { // 执行任务发生异常.
            $this->releaseLocked($lockeds);
            throw new Exception($e->getMessage(), $e->getCode());
        }

        // 循环释放已锁上的锁.
        $this->releaseLocked($lockeds);

        return $data;
    }

    // 释放已上锁的锁
    public function releaseLocked(array $lockeds)
    {
        // 循环释放已锁上的锁.
        foreach ($lockeds as $lKey) {
            $this->objCacheLock->releaseLock($lKey);
        }
    }

    /**
     * 任务执行
     */
    public function doTask(array $args, array $preTasks, array $taskIds, array $out)
    {
        $uid = $args['uid'];
        $option = $args['op'];
        $target = $args['target'];
        $times = $args['times'];

        // 读取用户已完成的任务记录
        $userTasks = $this->objDataUserTask->getUserTaskByTaskIds($uid, $taskIds);
        // 是否需要检查等级
        $needCheckGrade = $this->needCheckGrade($preTasks);

        $arrUserScore = null;
        $intCrtScore = 0;
        $intCrtGrade = 0;
        // 需要检查等级
        if ($needCheckGrade) {
            // 读取用户经验值
            $arrUserScore = $this->objDataUserScore->getByUid($uid);
            $intCrtScore = isset($arrUserScore['score']) ? $arrUserScore['score'] : 0;
            // 用户当前等级
            $intCrtGrade = $this->objDataUserScore->getGradeByScore($intCrtScore);
        }

        // 调用规则判定引擎，执行判定逻辑 => 获得可以进行完成的任务.
        $cTasks = $this->objRuleEngine->run($uid, $target, $times, $preTasks, $userTasks, $intCrtGrade);
        if (empty($cTasks)) { // 调用规则引擎得出没有要去完成的任务时
            return $out;
        }

        $cTids = array_keys($cTasks); // 已完成的tids
        LogHelper::pushLog('ruleRet', $cTids);
        $out['code'] = CmConst::CODE_RIGHT_RULE; // 符合规则，完成任务

        if ($arrUserScore === null) {
            // 读取用户经验值
            $arrUserScore = $this->objDataUserScore->getByUid($uid);
            $intCrtScore = isset($arrUserScore['score']) ? $arrUserScore['score'] : 0;
            // 用户当前等级
            $intCrtGrade = $this->objDataUserScore->getGradeByScore($intCrtScore);
        }

        // 计算新增的经验值、C点，是否升级
        $addScore = 0;
        $addCpoint = 0;
        $newGrades = array();
        $intGrade = $intCrtGrade; // 当前未加经验前等级.
        foreach ($cTasks as $tid => $v) {
            $addScore += $v['score'];
            $addCpoint += $v['cpoint'];
            // @todo 加上每日上限规则进行积分、经验再处理.
            // 新经验值
            $intNewScore = $intCrtScore + $addScore;
            // 新等级
            $intNewGrade = $this->objDataUserScore->getGradeByScore($intNewScore);
            for (; $intGrade < $intNewGrade; ++ $intGrade) {
                // 上升等级
                $arrGrade = array();
                $arrGrade['grade'] = $intGrade + 1;
                $arrGrade['t_id'] = $tid; // 任务id
                $arrGrade['c_time'] = UtilHelper::now(); // 当前时间戳
                $newGrades[] = $arrGrade;
            }
        }

        $out['score'] = $addScore;
        $out['cpoint'] = $addCpoint;
        $out['newScore'] = $intCrtScore + $addScore;
        // 读取加c币前的用户c币数
        $arrUserCpoint = $this->objDataUserCpoint->getByUid($uid);
        $intCrtCpoint = isset($arrUserCpoint['cpoint']) ? $arrUserCpoint['cpoint'] : 0;
        $out['newCpoint'] = $intCrtCpoint + $addCpoint;
        // 读取mysql中用户的c币数
        $arrUserCpointV2 = $this->objDataUserCpointV2->getByUid($uid);
        $isNew = empty($arrUserCpointV2);
        // 增加用户积分 & 经验
        $added = $this->addScoreAndCpoint($uid, $addCpoint, $addScore, $newGrades, $option, $cTids, $target, $isNew, $args);
        if (! $added) {
            return false;
        }

        // 用户做了的本任务入库操作
        $this->recordUserCompentedTask($cTasks);

        return $out;
    }

    /**
     * **本方法暂时不提供使用.
     * 执行不经过规则验证的操作, 一般用于第一次批量统计用户历史数据中非一次性(可重复)任务数据.
     */
    public function executeNoRule(array $args)
    {
        $uid = $args['uid'];
        $option = $args['op'];
        $times = $args['times'];

        $out = array();

        // 识别需要去完成的任务 (识别task，映射op到task)
        $preTasks = $this->objDataCnf->mapOp2Tasks($option);
        if (empty($preTasks)) {
            $log = array(
                'msg' => 'mapOp2Tasks fail',
                'op' => $option
            );
            LogWrapper::warning($log);
            return $out;
        }

        $cpoints = $scores = 0;

        foreach ($preTasks as $tid => $val) {
            $cpoints += ($val['cpoint'] * $times); // 总共要加的积分.
            $scores += ($val['score'] * $times); // 总共要加的经验值.
        }

        $basic = array(
            'uid' => new MongoId($uid),
            'op' => $option,
            'appname' => $this->appName,
            'remark' => $this->objDataAccountChangeLog->makeRemark(array(
                'tids' => implode(',', array_keys($preTasks)),
                'msg' => 'completed batch task'
            ))
        );
        $cTime = UtilHelper::microtime();

        // 增加积分
        if (false === $this->objDataUserCpoint->addCpoint($uid, $cpoints)) {
            $log = array(
                'msg' => 'addCpoint fail',
                'uid' => $uid,
                'cpoint' => $cpoints
            );
            LogWrapper::warning($log);
            return false;
        }
        // 记录到流水记录中
        $record = array(
            '_id' => new MongoId(),
            'type' => DaoAccountChangeLog::TYPE_CPOINT,
            'amount' => $cpoints,
            'c_time' => UtilHelper::float2MongoDate($cTime)
        );
        $record = array_merge($record, $basic);
        if (false === $this->objDataAccountChangeLog->insert($record)) {
            // @todo 需要手动修复数据
            LogHelper::error('Batch add account change log fail with record:' . json_encode($record));
        }

        // 增加经验值
        if (false === $this->objDataUserScore->addScore($uid, $scores, array())) {
            $log = array(
                'msg' => 'addScore fail',
                'uid' => $uid,
                'score' => $scores,
            );
            LogWrapper::warning($log);
            return false;
        }
        // 记录到流水记录中
        $record = array(
            '_id' => new MongoId(),
            'type' => DaoAccountChangeLog::TYPE_SCORE,
            'amount' => $scores,
            'c_time' => UtilHelper::float2MongoDate($cTime + 0.001)
        );
        $record = array_merge($record, $basic);
        if (false === $this->objDataAccountChangeLog->insert($record)) {
            // @todo 需要手动修复数据
            LogHelper::error('Batch add account change log fail with record:' . json_encode($record));
        }

        return $out;
    }

    /**
     * 增加用户积分 & 经验
     * @param string $uid
     * @param int $addCpoint
     * @param int $addScore
     * @param array $newGrades
     * @param string $option
     * @param array $option
     * @param bool $isNew cpoint记录是否为新的记录
     * @return boolean
     */
    public function addScoreAndCpoint($uid, $addCpoint, $addScore, array $newGrades, $option, array $tids, $target = '', $isNew = false, $args = array())
    {
        $basic = array(
            'uid' => new MongoId($uid),
            'appname' => $this->appName,
            'op' => $option,
            'target' => $target,
            'remark' => $this->objDataAccountChangeLog->makeRemark(array(
                'tids' => implode(',', $tids),
                'msg' => 'completed task'
            ))
        );
        $cTime = UtilHelper::microtime();

        // 增加了经验
        if ($addScore > 0) {
            if (false === $this->objDataUserScore->addScore($uid, $addScore, $newGrades)) {
                return false;
            }
            // 记录到流水记录中
            $record = array(
                '_id' => new MongoId(),
                'type' => DaoAccountChangeLog::TYPE_SCORE,
                'amount' => $addScore,
                'c_time' => UtilHelper::float2MongoDate($cTime)
            );
            $record = array_merge($record, $basic);
            if (false === $this->objDataAccountChangeLog->insert($record)) {
                // @todo 需要手动修复数据
                LogHelper::error('Add account change log fail with record:' . json_encode($record));
            }
        }
        LogHelper::pushLog('score', $addScore);

        // 增加了c点
        if ($addCpoint > 0) {
            if (false === $this->objDataUserCpoint->addCpoint($uid, $addCpoint)) {
                return false;
            }
            if (false === $this->objDataBase->addCpoint($uid, $addCpoint, $option, $isNew, json_encode($args))) {
                return false;
            }
            // 记录到流水记录中
            $record = array(
                '_id' => new MongoId(),
                'type' => DaoAccountChangeLog::TYPE_CPOINT,
                'amount' => $addCpoint,
                'c_time' => UtilHelper::float2MongoDate($cTime + 0.001)
            );
            $record = array_merge($record, $basic);
            if (false === $this->objDataAccountChangeLog->insert($record)) {
                // @todo 需要手动修复数据
                LogHelper::error('Add account change log fail with record:' . json_encode($record));
            }
        }
        LogHelper::pushLog('cpoint', $addCpoint);

        return true;
    }

    /**
     * 用户做了的本任务入库操作
     * @param array $cTasks
     * @return boolean
     */
    public function recordUserCompentedTask(array $cTasks)
    {
        foreach ($cTasks as $tid => $v) {
            if (isset($v['id'])) { // userTask表中有记录.
                // 更新用户任务表中经验值 + c点 && 同时会set actions
                if (false === $this->objDataUserTask->incScoreAndCpoint($v)) {
                    LogHelper::warning('IncScoreAndCpoint user task fail with data:' . json_encode($v));
                    return false;
                }
            } else {
                // 插入到用户任务表
                if (false === $this->objDataUserTask->updateData($v)) {
                    LogHelper::warning('UpdateData user task fail with data:' . json_encode($v));
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 检查是否需要升级
     * @param array $preTasks
     * @return boolean
     */
    public function needCheckGrade(array $preTasks)
    {
        // 是否需要检查等级
        $flag = false;
        foreach ($preTasks as $v) {
            if (isset($v['rule']['s_grade']) && $v['rule']['s_grade'] != - 1) {
                $flag = true;
                break;
            }
            if (isset($v['rule']['e_grade']) && $v['rule']['e_grade'] != - 1) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }
}
