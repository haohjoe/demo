<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CmController extends Controller
{
    protected $solution; // 使用会员积分、经验方案
    
    public function init()
    {
        parent::init();
        // 检查必要参数及设定积分、经验解决方案
        $this->checkParams();
    }
    
    protected function checkParams()
    {
        $appNames = Yii::app()->params['appNames'];
        // appname 不合法
        if (!in_array($this->appName, array_keys($appNames))) {
            ResponseHelper::outputJsonV2(array(), 'appName error!', Errno::PARAMETER_VALIDATION_FAILED);
        }
        // 设置当前选择的解决方案
        $this->solution = $appNames[parent::appName];
    }
}
