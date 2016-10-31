<?php

/**
 * data基类
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/07
 */
class DataBase
{
    public $solution;
    public $appName;

    public $objDaoRelationalBase = null;
    public $objDataOrder = null;
    public $objDataUserCpointV2 = null;

    public static function makeAoid($appname, $orderId)
    {
        return $appname . '_' . $orderId;
    }

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;

        $this->objDaoRelationalBase = Factory::create('DaoRelationalBase', array('db.relational.cm.' . $solution . '.write'));
        $this->objDataOrder = Factory::create('DataOrder', array($solution, $appName));
        $this->objDataUserCpointV2 = Factory::create('DataUserCpointV2', array($solution));
    }

    /**
     * 添加c币
     */
    public function addCpoint($uid, $cpoint, $action, $isNew = false, $binLog = '', $type = DaoCpointLog::TYPE_DONE_TASK)
    {
        $arrRet = $this->objDaoRelationalBase->addCpoint($this->appName, $uid, $cpoint, $action, $isNew, $binLog, $type);
        // 删除用户c币信息缓存
        $this->objDataUserCpointV2->delCacheByUid($uid);
        if (false === $arrRet) {
            return false;
        }

        return true;
    }

    /**
     * 减少c币
     */
    public function reduceCpoint($uid, $cpoint, $action, $binLog = '')
    {
        $arrRet = $this->objDaoRelationalBase->reduceCpoint($this->appName, $uid, $cpoint, $action, $binLog);
        // 删除用户c币信息缓存
        $this->objDataUserCpointV2->delCacheByUid($uid);
        if (false === $arrRet) {
            return false;
        }

        return true;
    }

    /**
     * 消费c币时（此过程不同于先冻结再支付）
     */
    public function consumeCpoint($uid, $cpoint, $aoId, $orderId, $binLog = '')
    {
        $arrRet = $this->objDaoRelationalBase->consumeCpoint($this->appName, $uid, $cpoint, $aoId, $orderId, $binLog);
        // 不管return false 都删缓存是基于有可能某次删除缓存失败，导致消费会一直失败，而用户却拿到错误的缓存信息
        // 删除用户c币信息缓存
        $this->objDataUserCpointV2->delCacheByUid($uid);
        // 删除用户订单信息缓存
        $this->objDataOrder->delCacheByAoId($aoId);
        if (false === $arrRet) {
            return false;
        }

        return true;
    }

    /**
     * 撤回c币时
     */
    public function revokeCpoint($uid, $cpoint, $aoId, $binLog = '')
    {
        $arrRet = $this->objDaoRelationalBase->revokeCpoint($this->appName, $uid, $cpoint, $aoId, $binLog);
        // 删除用户c币信息缓存
        $this->objDataUserCpointV2->delCacheByUid($uid);
        // 删除用户订单信息缓存
        $this->objDataOrder->delCacheByAoId($aoId);
        if (false === $arrRet) {
            return false;
        }

        return true;
    }

    /**
     * 冻结c币时(冻结c币场所一般用于c币和第三方混合支付用到)
     */
    public function freezeCpoint()
    {

    }

    /**
     * 解冻c币时(其实也就是用户创建了订单冻结完c币后，又删除订单，则需要解冻)
     * 解冻的订单必须是状态为已冻结的订单
     */
    public function thawCpoint()
    {

    }

    /**
     * 通知已付款，更新状态事物
     * 此时订单状态必须为冻结中（==未支付）状态
     */
    public function notifyPaid()
    {

    }
}
