<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

    protected $appName = null;
    protected $appVersion = null;
    protected $systemVersion = null;
    protected $platform = null;
    protected $device = null;
    protected $locale = null;
    protected $deviceId = null;
    protected $channel = null;
    protected $cid = null;
    protected $longtitude = null;
    protected $latitude = null;
    protected $mnc = null;
    protected $mcc = null;
    protected $icc = null;

    public function init()
    {
        parent::init();
        // 增加Ajax标识，用于异常处理
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) == false) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }
        if (! defined('MODULE_NAME')) {
            define('MODULE_NAME', 'member'); 
        }
        LogHelper::init();
        // 打印请求参数
        $arrLogParams = $_REQUEST;
        if (isset($arrLogParams['token'])) {
            $arrLogParams['token'] = '***<' . strlen($arrLogParams['token']) . 'chars>'; 
        }
        if (isset($arrLogParams['signpass'])) {
            $arrLogParams['signpass'] = '***<' . strlen($arrLogParams['signpass']) . 'chars>'; 
        }
        LogHelper::pushLog('params', $arrLogParams);
    }

    public function filters()
    {
        return array(
            
            // 'checkCommonParameters',
            'accessControl'
        );
    }

    /**
     * 检验公共参数.
     *
     * @param mixed $filterChain
     * @access public
     * @return void
     */
    public function filterCheckCommonParameters($filterChain)
    {
        $aryCommParams = ControllerParameterValidator::validateCommonParamters($_REQUEST);
        
        $this->appName = $aryCommParams['appName'];
        $this->appVersion = $aryCommParams['appVersion'];
        $this->systemVersion = $aryCommParams['systemVersion'];
        $this->platform = $aryCommParams['platform'];
        $this->device = $aryCommParams['device'];
        $this->deviceId = $aryCommParams['deviceId'];
        $this->locale = $aryCommParams['locale'];
        $this->channel = $aryCommParams['channel'];
        $this->cid = $aryCommParams['cid'];
        $this->longtitude = $aryCommParams['longtitude'];
        $this->latitude = $aryCommParams['latitude'];
        $this->mnc = $aryCommParams['mnc'];
        $this->mcc = $aryCommParams['mcc'];
        $this->icc = $aryCommParams['icc'];
        Yii::app()->language = UtilHelper::getLanguage($this->locale);
        $filterChain->run();
    }

    public function filterAccessControl($filterChain)
    {
        LogHelper::profile_start(__CLASS__ . ':' . __FUNCTION__);
        $filter = new AccessCheckFilter();
        $filter->setRules($this->accessRules());
        $filter->filter($filterChain);
        LogHelper::profile_end(__CLASS__ . ':' . __FUNCTION__);
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'users' => array(
                    '@'
                )
            ),
            array(
                'deny'
            )
        );
    }

    public function run($actionID)
    {
        try {
            parent::run($actionID);
        } catch (ParameterValidationException $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), Errno::PARAMETER_VALIDATION_FAILED);
        } catch (PrivilegeException $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), Errno::PRIVILEGE_NOT_PASS);
        } catch (Exception $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            if ($e->getCode() == 500) { // 只在500错误是才记录debug_backtrace
                LogHelper::error(@json_encode(debug_backtrace()));
            }
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), Errno::INTERNAL_SERVER_ERROR);
        }
    }

    protected function beforeAction($action)
    {
        $nickName = Yii::app()->user->getName(); // 用户昵称
        LogHelper::pushLog('nickname', $nickName);
        return true;
    }
}
