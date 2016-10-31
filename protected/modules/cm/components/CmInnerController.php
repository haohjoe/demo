<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CmInnerController extends CController
{

    protected $appName;
    protected $sig;
    protected $solution; // 使用会员积分、经验方案
    
    public function init()
    {
        parent::init();
        
        // 增加Ajax标识，用于异常处理
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) == false) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }
        
        if (! defined('MODULE_NAME')) {
            define('MODULE_NAME', SYSTEM_NAME); 
        }
        
        LogHelper::init();
        $arrLogParams = $_REQUEST;
        if (isset($arrLogParams['sig'])) {
            $arrLogParams['sig'] = '***<'.strlen($arrLogParams['sig']).'chars>';
        }
        LogHelper::pushLog('params', $arrLogParams);
        
        // 检查必要参数
        $this->checkParams();
        // 验证签名
        $this->verifySign();
    }

    protected function checkParams()
    {
        $appNames = Yii::app()->params['appNames'];
        $this->appName = Yii::app()->request->getParam('appName');
        $this->sig = Yii::app()->request->getParam('sig');
        // appname 不合法
        if (!in_array($this->appName, array_keys($appNames))) {
            ResponseHelper::outputJsonV2(array(), 'appName error!', Errno::PARAMETER_VALIDATION_FAILED);
        }
        if (! $this->appName || ! $this->sig) {
            ResponseHelper::outputJsonV2(array(), 'Missing parameter!', Errno::PARAMETER_VALIDATION_FAILED);
        }
        // 设置当前选择的解决方案
        $this->solution = $appNames[$this->appName];
    }

    /**
     * 检查接口签名
     */
    protected function verifySign()
    {
        if (isset(Yii::app()->params['innerProject'][$this->appName]['appsecret']) == false) {
            throw new ParameterValidationException($this->appName . 'is illegal!');
            ResponseHelper::outputJsonV2(array(), $this->appName . 'is illegal!', Errno::PARAMETER_VALIDATION_FAILED);
        }
        $secretKey = Yii::app()->params['innerProject'][$this->appName]['appsecret'];
        $allParams = array();
        // $allParams = array_merge($allParams, $_GET);
        $allParams = array_merge($allParams, $_POST);
        if (isset($allParams['sig'])) {
            unset($allParams['sig']);
        }
        $sign = SecurityHelper::sign($allParams, $secretKey);
        if ($sign != $this->sig) {
            ResponseHelper::outputJsonV2(array(), 'Params is illegal!', Errno::PARAMETER_VALIDATION_FAILED);
        }
        
        return true;
    }

    public function run($actionID)
    {
        try {
            parent::run($actionID);
        } catch (ParameterValidationException $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            if ($e->getCode() == 500) { // 只在500错误是才记录debug_backtrace
                LogHelper::error(@json_encode(debug_backtrace()));
            }
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), $e->getCode());
        }
    }
}
