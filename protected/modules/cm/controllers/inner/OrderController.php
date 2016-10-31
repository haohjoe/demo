<?php

/**
 * 订单控制器
 */
class OrderController extends CmInnerController
{

    /**
     * 获取用户订单列表信息
     */
    public function actionGetUserOrders()
    {
        // 用户uid
        $args['uid'] = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        $statuses = array(
            DaoOrder::STATUS_FROZEN,
            DaoOrder::STATUS_PAID,
            DaoOrder::STATUS_REVOKE
        );
        $args['status'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'status', $statuses, null);
        $args['page'] = ControllerParameterValidator::validateInteger($_REQUEST, 'page', 1, 500, 1);
        $args['limit'] = ControllerParameterValidator::validateInteger($_REQUEST, 'limit', 1, 100, 20);

        $logic = new OrderLogic($this->solution, $this->appName);
        $data = $logic->getUserOrders($args);

        ResponseHelper::outputJsonV2($data);
    }

    /**
     * 检查订单是否消费成功
     */
    public function actionCheckConsumeIsSucc()
    {
        // 用户uid
        $args['uid'] = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // 订单id
        $args['orderId'] = ControllerParameterValidator::validateString($_POST, 'orderId');
        $args['appName'] = $this->appName;
        $args['status'] = DaoOrder::STATUS_PAID;

        $logic = new OrderLogic($this->solution, $this->appName);
        $data = $logic->checkStatus($args);

        ResponseHelper::outputJsonV2($data);
    }
}
