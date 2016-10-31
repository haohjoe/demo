<?php

class TestController extends CmInnerController
{

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $uid = ControllerParameterValidator::validateString($_REQUEST, 'uid');
        try {
            // code here
        } catch (Exception $e) {
            LogHelper::error(json_encode(debug_backtrace));
            LogHelper::pushLog('errno', $e->getCode());
            ResponseHelper::outputJsonV2(array(), 'get data failed', Errno::INTERNAL_SERVER_ERROR);
        }
        
        ResponseHelper::outputJsonV2(array('uid' => $uid));
    }
}
