<?php

class DemoController extends CController
{
    public function __contruct()
    {
        // 生产环境屏蔽.
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'production') {
            Yii::app()->end();
        }
    }

    public function actionIndex()
    {
        try {
            $uid = Yii::app()->user->getId();
        } catch (Exception $e) {
            LogHelper::error(json_encode(debug_backtrace));
            LogHelper::pushLog('errno', $e->getCode());
            ResponseHelper::outputJsonV2(array(), 'get data failed', Errno::INTERNAL_SERVER_ERROR);
        }

        ResponseHelper::outputJsonV2(array("uid" => $uid));
    }

    public function actionTestUserGetCpoint()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'photoTask',
            'uid' => $uid
        );
        $sign = SecurityHelper::sign($postArr, '8cd892b7b97ef9489ae4479d3f4efc');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/user/getCpoint';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestUserMultiGetCpoint()
    {
        $uids = '06938c547d2ace475852a5b9,00b40c563f0b1aad57460003';
        $postArr = array(
            'appName' => 'photoTask',
            'uids' => $uids
        );
        $sign = SecurityHelper::sign($postArr, '8cd892b7b97ef9489ae4479d3f4efc');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/user/multiGetCpoint';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestConsume()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'mall',
            'uid' => $uid,
            'cpoint' => 1.0,
            'orderId' => 'xxxx'
        );
        $sign = SecurityHelper::sign($postArr, '2ee385d4e42e07f2c539b597559e70ee');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/useCpoint/consume';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestRevoke()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'mall',
            'uid' => $uid,
            'cpoint' => 1.0,
            'orderId' => 'xxxx'
        );
        $sign = SecurityHelper::sign($postArr, '2ee385d4e42e07f2c539b597559e70ee');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/useCpoint/revoke';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestSubmit()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'photoTask',
            'uid' => $uid,
            'op' => 'vote',
            'target' => '',
            'times' => 1
        );
        $sign = SecurityHelper::sign($postArr, '8cd892b7b97ef9489ae4479d3f4ef0fc');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/task/submit';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestUserAdminAddCpoint()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'mall',
            'uid' => $uid,
            'cpoint' => 100.0,
            'adminId' => 'test'
        );
        $sign = SecurityHelper::sign($postArr, '2ee385d4e42e07f2c539b597559e70ee');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/user/adminAddCpoint';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestUserAdminReduceCpoint()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'mall',
            'uid' => $uid,
            'cpoint' => 100.0,
            'adminId' => 'test'
        );
        $sign = SecurityHelper::sign($postArr, '2ee385d4e42e07f2c539b597559e70ee');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/user/adminReduceCpoint';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }

    public function actionTestOrderCheckConsumeIsSucc()
    {
        $uid = '06938c547d2ace475852a5b9';
        $postArr = array(
            'appName' => 'mall',
            'uid' => $uid,
            'orderId' => 'xxxx'
        );
        $sign = SecurityHelper::sign($postArr, '2ee385d4e42e07f2c539b597559e70ee');
        $postArr['sig'] = $sign;
        var_dump($sign);

        $url = 'http://127.0.0.1:8070/inner/cm/order/checkConsumeIsSucc';
        $data = HttpHelper::post($url, $postArr);

        var_dump($data);
    }
}
