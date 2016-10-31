<?php

/**
 * 使用c点
 */
class UseCpointController extends CmInnerController
{
    /**
     * 消费c币
     */
    public function actionConsume()
    {
        // 判断是否关闭consume
        if (isset(Yii::app()->params['closeApi']['consume']) && Yii::app()->params['closeApi']['consume'] === 1) {
            throw new Exception('Consume api has closed.', Errno::API_HAS_CLOSED);
            // ResponseHelper::outputJsonV2(array());
        }

        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // c币
        $cpoint = ControllerParameterValidator::validateFloat($_POST, 'cpoint');
        // 订单id
        $orderId = ControllerParameterValidator::validateString($_POST, 'orderId');

        $data = array(
            'uid' => $uid,
            'cpoint' => $cpoint,
            'orderId' => strval($orderId)
        );
        $logic = new UseCpointLogic($this->solution, $this->appName);
        $rst = $logic->consume($data);

        // 成功打日志
        LogHelper::pushLog('ConsumeCpoint', 'success');

        ResponseHelper::outputJsonV2($rst);
    }

    /**
     * 撤回
     */
    public function actionRevoke()
    {
        // 判断是否关闭revoke
        if (isset(Yii::app()->params['closeApi']['revoke']) && Yii::app()->params['closeApi']['revoke'] === 1) {
            throw new Exception('Revoke api has closed.', Errno::API_HAS_CLOSED);
            // ResponseHelper::outputJsonV2(array());
        }

        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // c币
        $cpoint = ControllerParameterValidator::validateFloat($_POST, 'cpoint');
        // 订单id
        $orderId = ControllerParameterValidator::validateString($_POST, 'orderId');
        
        $args = array(
            'uid' => $uid,
            'cpoint' => $cpoint,
            'orderId' => strval($orderId)
        );
        $logic = new UseCpointLogic($this->solution, $this->appName);
        $rst = $logic->revoke($args);
        
        if ($rst) {
            $data = array(
                'id' => $rst
            );
            ResponseHelper::outputJsonV2($data);
            // 成功打日志
            LogHelper::pushLog('RevokeCpoint', 'success');
        } else {
            ResponseHelper::outputJsonV2($rst, 'fail', Errno::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 冻结c币
     */
    public function actionFreeze()
    {
        // 判断是否关闭freeze
        if (isset(Yii::app()->params['closeApi']['freeze']) && Yii::app()->params['closeApi']['freeze'] === 1) {
            throw new Exception('Freeze api has closed.', Errno::API_HAS_CLOSED);
            // ResponseHelper::outputJsonV2(array());
        }

        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // c币
        $cpoint = ControllerParameterValidator::validateFloat($_POST, 'cpoint');
        // 订单id
        $orderId = ControllerParameterValidator::validateString($_POST, 'orderId');

        $data = array(
            'uid' => $uid,
            'cpoint' => $cpoint,
            'orderId' => strval($orderId)
        );
        $logic = new UseCpointLogic($this->solution, $this->appName);
        $rst = $logic->freeze($data);
        // 成功打日志
        LogHelper::pushLog('FreezeCpoint', 'success');

        ResponseHelper::outputJsonV2($rst);
    }

    /**
     * 支付已冻结的c币(通知冻结（未支付）的订单状态更改为已支付)
     */
    public function actionNotifyPaid()
    {
        // 判断是否关闭notifyPaid
        if (isset(Yii::app()->params['closeApi']['notifyPaid']) && Yii::app()->params['closeApi']['revoke'] === 1) {
            throw new Exception('NotifyPaid api has closed.', Errno::API_HAS_CLOSED);
            // ResponseHelper::outputJsonV2(array());
        }

        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // 订单id
        $orderId = ControllerParameterValidator::validateString($_POST, 'orderId');

        $data = array(
            'uid' => $uid,
            'orderId' => strval($orderId)
        );
        $logic = new UseCpointLogic($this->solution, $this->appName);
        $rst = $logic->notifyPaid($data);
        // 成功打日志
        LogHelper::pushLog('FreezeCpoint', 'success');

        ResponseHelper::outputJsonV2($rst);
    }
}
