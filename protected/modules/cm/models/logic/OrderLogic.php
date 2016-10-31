<?php

/**
 * 订单logic
 * @author xiaoshiyong@camera360.com
 * @date 20141204
 */
class OrderLogic
{
    public $solution;
    public $appName;

    public $objDataOrder;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;
        
        $this->objDataOrder = Factory::create('DataOrder', array($this->solution, $this->appName));
    }

    public function checkStatus(array $args)
    {
        $uid = $args['uid'];
        $orderId = $args['orderId'];
        $status = $args['status'];

        $aoId = DataBase::makeAoid($this->appName, $orderId);
        $oInfo = $this->objDataOrder->getByAoId($uid, $aoId);
        if (empty($oInfo)) {
            throw new Exception('Order not found.', Errno::ORDER_NOT_FOUND);
        }

        return array(
            'result' => $oInfo['status'] == $status ? 1 : 0
        );
    }
}
