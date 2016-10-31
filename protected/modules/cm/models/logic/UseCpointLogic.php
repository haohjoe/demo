<?php

/**
 * 消费c币
 * @author yun-yb
 * @date 20141204
 */
class UseCpointLogic
{
    public $solution;
    public $appName;

    public $objDataOrder;
    public $objDataBase;
    public $objDataAccountChangeLog;
    public $objDataUserCpointV2;

    // @todo -> mq
    public $objDaoUserCpoint;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;

        $this->objDataOrder = Factory::create('DataOrder', array($this->solution, $this->appName));
        $this->objDataBase = Factory::create('DataBase', array($this->solution, $this->appName));
        $this->objDataAccountChangeLog = Factory::create('DataAccountChangeLog', array($this->solution));
        $this->objDataUserCpointV2 = Factory::create('DataUserCpointV2', array($this->solution));
        // @todo -> mq
        $this->objDaoUserCpoint = Factory::create('DaoUserCpoint', array($this->solution));
    }

    /**
     * 减少(消费)c币
     *
     * @param $data array
     * @throw Exception
     * @return array
     */
    public function consume(array $data)
    {
        $uid = $data['uid']; // string
        $cpoint = $data['cpoint'];
        $orderId = $data['orderId'];
        $aoId = DataBase::makeAoid($this->appName, $orderId);

        // 查看用户c币信息
        $userCpointInfo = $this->objDataUserCpointV2->getByUid($uid);
        if ($userCpointInfo === false) { // 查询mysql出现异常
            throw new Exception('Get user cpoint info fail', Errno::INTERNAL_SERVER_ERROR);
        }
        $oldCpoint = isset($userCpointInfo['cpoint']) ? $userCpointInfo['cpoint'] : 0;

        // 检查cpoint余额
        if ($oldCpoint < $cpoint) {
            LogHelper::error('Cpoint not enough, data:' . json_encode($data));
            throw new Exception('Cpoint not enough', Errno::CPOINT_NOT_ENOUGH);
        }

        // 查看是否有已有订单信息
        $orderInfo = $this->objDataOrder->getByAoId($uid, $aoId);
        // 检查是否重复为一个订单消费cpoint
        if ($orderInfo) { // consume 的订单不用考虑status、只要订单存在就可认为是重复消费.
            // LogHelper::error('Duplicate consume, data:' . json_encode($data));
            // throw new Exception('Duplicate consume', Errno::DUPLICATE_CONSUME);
            return array( // 重复订单情况不进行抛异常处理.
                'id' => $aoId,
                'cpoint' => $oldCpoint
            );
        }

        // 减少c币
        $rtn = $this->objDataBase->consumeCpoint($uid, $cpoint, $aoId, $orderId, json_encode($data));
        if ($rtn === false) {
            LogHelper::error('cut cpoint fail, data:' . json_encode($data));
            throw new Exception('cut cpoint fail', Errno::INTERNAL_SERVER_ERROR);
        }
        // @todo -> mq
        $this->objDaoUserCpoint->reduceCpoint($uid, $cpoint);

        //添加账号变更记录 @todo -> mq
        $this->addAccountChangeLog($uid, -$cpoint, $aoId, 'user consume cpoint');

        return array(
            'id' => $aoId,
            'cpoint' => $oldCpoint - $cpoint
        );
    }

    /**
     * 撤回
     * @param $data array
     * @throw Exception
     * @return string
     */
    public function revoke($data)
    {
        $uid = $data['uid']; // string
        $cpoint = $data['cpoint'];
        $orderId = $data['orderId'];
        $aoId = DataBase::makeAoid($this->appName, $orderId);

        // 根据orderId获取记录
        $orderInfo = $this->objDataOrder->getByAoId($uid, $aoId);
        if (! $orderInfo) {
            LogHelper::error('Order not found, data:' . json_encode($data));
            throw new Exception('Order not found', Errno::ORDER_NOT_FOUND);
        }

        // 判断找到的用户id是否相等
        if ($orderInfo['uid'] != $uid) {
            LogHelper::error('Uid not equal, data:' . json_encode($data));
            throw new Exception('Uid not equal', Errno::PARAMETER_VALIDATION_FAILED);
        }

        // 检查消费状态(必须订单状态是已支付/已冻结的才能撤回)
        if ($orderInfo['status'] != DaoOrder::STATUS_PAID && $orderInfo['status'] != DaoOrder::STATUS_FROZEN) {
            LogHelper::error('Cannot revoke, status:' . $orderInfo['status'] . ', data:' . json_encode($data));
            throw new Exception('Cannot revoke', Errno::CANNOT_REVOKE);
        }

        // 检查撤销cpoint
        if ($cpoint > $orderInfo['cpoint']) {
            LogHelper::error('cpoint err, order cpoint:' . $orderInfo['cpoint'] . ', data:' . json_encode($data));
            throw new Exception('cpoint err', Errno::PARAMETER_VALIDATION_FAILED);
        }

        // 撤销
        if (! $this->objDataBase->revokeCpoint($uid, $cpoint, $aoId, json_encode($data))) {
            LogHelper::error('Revoke cpoint fail, data:' . json_encode($data));
            throw new Exception('Revoke cpoint fail');
        }

        // @todo -> mq
        $this->objDaoUserCpoint->addCpoint($uid, $cpoint);

        //添加账号变更记录 @todo -> mq
        $this->addAccountChangeLog($uid, $cpoint, $aoId, 'user revoke cpoint');

        return $aoId;
    }

    /**
     * 冻结c币(用于混合支付)
     */
    public function freeze(array $data)
    {
        return false;
    }

    /**
     * 通知冻结的已支付
     */
    public function notifyPaid(array $data)
    {
        return false;
    }

    /**
     * 添加到账号变化记录日志
     * @param $uid
     * @param $cpoint
     * @param $orderId
     * @return null
     */
    public function addAccountChangeLog($uid, $cpoint, $aoId, $msg)
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
            'ao_id' => $aoId
        );
        if (false === $this->objDataAccountChangeLog->insert($record)) {
            // @todo 需要手动修复数据
            LogHelper::error('Add account change log fail with record:' . json_encode($record));
        }
    }
}
