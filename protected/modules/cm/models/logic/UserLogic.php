<?php

/**
 * 用户
 * @author xiaoshiyong@camera360.com
 * @date 20150611
 */
class UserLogic
{
    const ADMIN_ADD_CPOINT = 'admin_add_cpoint';
    const ADMIN_REDUCE_CPOINT = 'admin_reduce_cpoint';

    public $solution;
    public $appName;

    public $objDataCpoint;
    public $objDataCpointV2;
    public $objDataScore;
    public $objDataBase;
    public $objDataAccountChangeLog;
    // @todo 去除
    public $objDaoUserCpoint;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;

        $this->objDataCpoint = Factory::create('DataUserCpoint', array($this->solution));
        $this->objDataBase = Factory::create('DataBase', array($this->solution, $this->appName));
        $this->objDataCpointV2 = Factory::create('DataUserCpointV2', array($this->solution));
        $this->objDataScore = Factory::create('DataUserScore', array($this->solution));
        $this->objDataAccountChangeLog = Factory::create('DataAccountChangeLog', array($this->solution));
        // @todo 去除
        $this->objDaoUserCpoint = Factory::create('DaoUserCpoint', array($this->solution));
    }

    /**
     * 添加积分【后台操作】
     */
    public function addCpointByUid($uid, $cpoint, $adminId)
    {
        $userCpointInfo = $this->objDataCpointV2->getByUid($uid);
        if ($userCpointInfo === false) {
            throw new Exception('Add cpoint fail', Errno::INTERNAL_SERVER_ERROR);
        }
        $isNew = empty($userCpointInfo);
        $binLog = array(
            'uid' => $uid,
            'cpoint' => $cpoint
        );
        $action = self::ADMIN_ADD_CPOINT . '_' . $adminId;
        $rst = $this->objDataBase->addCpoint($uid, $cpoint, $action, $isNew, json_encode($binLog), DaoCpointLog::TYPE_ADMIN_ADD_CPOINT);
        $crtUserCpointInfo = array();
        if ($rst) { // 添加成功,直接读取最新的c币信息.
            $crtUserCpointInfo = $this->objDataCpointV2->getByUid($uid);
            // 同步增加mongodb中c币数据 @todo -> mq
            $this->objDaoUserCpoint->addCpoint($uid, $cpoint);
            // 添加账号变更记录 @todo -> mq
            $this->addAccountChangeLog($uid, $cpoint, $adminId, 'admin add user cpoint');
        }

        return array(
            'cpoint' => empty($crtUserCpointInfo['cpoint']) ? 0 : floatval($crtUserCpointInfo['cpoint']),
            'result' => $rst ? 1 : 0
        );
    }

    /**
     * 较少积分【后台操作】
     */
    public function reduceCpointByUid($uid, $cpoint, $adminId)
    {
        $userCpointInfo = $this->objDataCpointV2->getByUid($uid);
        if (empty($userCpointInfo)) {
            throw new Exception('User cpoint info not found', Errno::INTERNAL_SERVER_ERROR);
        }
        $binLog = array(
            'uid' => $uid,
            'cpoint' => $cpoint
        );
        $action = self::ADMIN_REDUCE_CPOINT . '_' . $adminId;
        $rst = $this->objDataBase->reduceCpoint($uid, $cpoint, $action, json_encode($binLog));
        if ($rst) { // 添加成功,直接读取最新的c币信息.
            $userCpointInfo = $this->objDataCpointV2->getByUid($uid);
            // 同步减少mongodb中c币数据 @todo -> mq
            $this->objDaoUserCpoint->reduceCpoint($uid, $cpoint);
            // 添加账号变更记录 @todo -> mq
            $this->addAccountChangeLog($uid, - $cpoint, $adminId, 'admin reduce user cpoint');
        }

        return array(
            'cpoint' => empty($userCpointInfo['cpoint']) ? 0 : floatval($userCpointInfo['cpoint']),
            'result' => $rst ? 1 : 0
        );
    }

    /**
     * 获取c币
     *
     * @param uid $uid
     * @return float
     */
    public function getCpointByUid($uid)
    {
        $userCpointInfo = $this->objDataCpointV2->getByUid($uid);
        if ($userCpointInfo === false) {
            throw new Exception('Get cpoint fail', Errno::INTERNAL_SERVER_ERROR);
        }
        $data['cpoint'] = isset($userCpointInfo['cpoint']) ? $userCpointInfo['cpoint'] : 0;

        return $data;
    }

    /**
     * 获取多个用户c币
     *
     * @param uid $uid
     * @return float
     */
    public function getCpointByUids(array $uids)
    {
        $out = array();

        $multi = $this->objDataCpointV2->getByUids($uids);
        foreach ($multi as $uid => $val) {
            $out[$uid] = empty($val['cpoint']) ? 0 : $val['cpoint'];
        }

        return $out;
    }

    /**
     * 获取用户信息，如 c点，财富
     */
    public function listInfo(array $uids)
    {
        $out = array();

        // 获取用户c点
        $arrCpoints = $this->objDataCpointV2->getByUids($uids);
        // 获取用户经验值
        $arrScores = $this->objDataScore->getByUids($uids);

        foreach ($uids as $uid) {
            $cpoint = empty($arrCpoints[$uid]['cpoint']) ? 0 : $arrCpoints[$uid]['cpoint'];
            $score = empty($arrScores[$uid]['score']) ? 0 : $arrScores[$uid]['score'];
            $grade = $this->objDataScore->getGradeByScore($score);
            $out[$uid] = array(
                'cpoints' => $cpoint,
                'scores' => $score,
                'grade' => $grade
            );
        }

        return $out;
    }

    /**
     * 根据cpoint、score进行排序返回用户数据
     */
    public function listSortInfo($args)
    {
        $out = array();

        $condition = $args['condition'];
        $page = $args['page'];
        $limit = $args['limit'];
        $sort = $args['sort'];

        $arrCpoints = $arrScores = $uids = array();
        if ($condition === 'cpoint') { // 按经c点排序获取
            $arrCpoints = $this->objDataCpoint->getSortList($page, $limit, $sort);
            $uids = array_keys($arrCpoints);
            $arrScores = $this->objDataScore->getByUids($uids);
        } elseif ($condition === 'score') { // 按经验值排序获取
            $arrScores = $this->objDataScore->getSortList($page, $limit, $sort);
            $uids = array_keys($arrScores);
            $arrCpoints = $this->objDataCpoint->getByUids($uids);
        } else { // 按等级数获取
            list(, $intGrade) = explode('=', $condition);
            // 验证grade合法性
            if (! in_array($intGrade, array_keys(Yii::app()->params['grade']))) {
                throw new ParameterValidationException('Grade param is error');
            }
            $arrScores = $this->objDataScore->getSortListByGrade($intGrade, $page, $limit, $sort);
            $uids = array_keys($arrScores);
            $arrCpoints = $this->objDataCpoint->getByUids($uids);
        }

        // 组装的数据
        foreach ($uids as $uid) {
            $cpoint = empty($arrCpoints[$uid]['cpoint']) ? 0 : $arrCpoints[$uid]['cpoint'];
            $score = empty($arrScores[$uid]['score']) ? 0 : $arrScores[$uid]['score'];
            $grade = $this->objDataScore->getGradeByScore($score);
            $out[$uid] = array(
                'cpoints' => $cpoint,
                'scores' => $score,
                'grade' => $grade
            );
        }

        return $out;
    }

    /**
     * 添加到账号变化记录日志
     * @param $uid
     * @param $cpoint
     * @param $orderId
     * @return null
     */
    public function addAccountChangeLog($uid, $cpoint, $aid, $msg)
    {
        // 记录到流水记录中
        $record = array(
            '_id' => new MongoId(),
            'type' => DaoAccountChangeLog::TYPE_CPOINT,
            'amount' => $cpoint,
            'c_time' => UtilHelper::float2MongoDate(UtilHelper::microtime()),
            'uid' => new MongoId($uid),
            'appname' => $this->appName,
            'remark' => $this->objDataAccountChangeLog->makeRemark(array(
                'appname' => $this->appName,
                'msg' => $msg
            )),
            'aid' => $aid
        );
        if (false === $this->objDataAccountChangeLog->insert($record)) {
            // @todo 需要手动修复数据
            LogHelper::error('Add account change log fail with record:' . json_encode($record));
        }
    }
}
