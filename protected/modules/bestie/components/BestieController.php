<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BestieController extends CController
{

    protected $userId = null;

    protected $appName = null;

    protected $appVersion = null;

    protected $sdkVersion = null;

    protected $systemVersion = null;

    protected $platform = null;

    protected $deviceId = null;

    protected $device = null;

    protected $locale = null;

    protected $channel = null;

    private static $uinfo = false;

    private $debug = false;

    protected $message = '';

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

        if (isset($_REQUEST['__debug']) == true) {
            $this->debug = true;
        }

        // 打印请求参数
        $arrLogParams = $_REQUEST;
        if (! empty($arrLogParams['userToken'])) {
            $arrLogParams['userToken'] = '***<' . strlen($arrLogParams['userToken']) . 'chars>';
        }
        LogHelper::pushLog('params', $arrLogParams);
    }

    public function filters()
    {
        return array(
            'checkCommonParameters',
            'checkSign'
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
        if ($this->debug == true) {
            $arrDefault = array(
                'appName' => 'system',
                'appVersion' => '1.0.0',
                'systemVersion' => '1.0.0',
                'platform' => 'windows',
                'deviceId' => 'system',
                'device' => 'system',
                'locale' => 'zh_CN',
                'channel' => 'systemChannel'
            );
            foreach ($arrDefault as $strKey => $strValue) {
                if (isset($_REQUEST["$strKey"]) == false) {
                    $_REQUEST["$strKey"] = $strValue;
                }
            }
        }

        $this->appName = ControllerParameterValidator::validateString($_REQUEST, 'appName', 1, 50);
        $this->appVersion = ControllerParameterValidator::validateString($_REQUEST, 'appVersion', 1);
        $this->platform = ControllerParameterValidator::validateEnumString($_REQUEST, 'platform', array(
            'ios',
            'android',
            'windows',
            'winphone'
        ));
        $this->systemVersion = ControllerParameterValidator::validateString($_REQUEST, 'systemVersion');
        $this->deviceId = ControllerParameterValidator::validateString($_REQUEST, 'deviceId', 1);
        $this->device = ControllerParameterValidator::validateString($_REQUEST, 'device', 1);
        $this->locale = ControllerParameterValidator::validateString($_REQUEST, 'locale', 1);
        $this->channel = ControllerParameterValidator::validateString($_REQUEST, 'channel', 1);

        $filterChain->run();
    }

    /**
     * 检验签名.
     *
     * @param mixed $filterChain
     * @access public
     * @return void
     */
    public function filterCheckSign($filterChain)
    {
        if ($this->debug == true) {
            $filterChain->run();
        }

        $blPassed = $this->checkSignature();

        if ($blPassed == false) {
            ResponseHelper::outputJsonV2(array(), ($this->message ?: 'invalid signature'), Errno::PRIVILEGE_NOT_PASS);
        }

        $filterChain->run();
    }

    /**
     * 检查签名.
     *
     * @param boolean $blDebug
     * @static
     *
     * @access public
     * @return boolean
     */
    public function checkSignature()
    {
        $arrAllParams = array_merge($_GET, $_POST);

        $strCredential = ControllerParameterValidator::validateString($arrAllParams, 'X-PG-Credential', 1);
        $intExpires = ControllerParameterValidator::validateInteger($arrAllParams, 'X-PG-Expires', 1);
        $intTime = ControllerParameterValidator::validateInteger($arrAllParams, 'X-PG-Time', 1);
        $strSignature = ControllerParameterValidator::validateString($arrAllParams, 'Signature', 1);
        $strDeviceId = ControllerParameterValidator::validateString($arrAllParams, 'deviceId', 1);

        $strMethod = $_SERVER['REQUEST_METHOD'];
        $arrUri = parse_url($_SERVER['REQUEST_URI']);
        $strUri = $arrUri['path'];

        unset($arrAllParams['X-PG-Credential']);
        unset($arrAllParams['X-PG-Expires']);
        unset($arrAllParams['X-PG-Time']);
        unset($arrAllParams['Signature']);
        $strSecretKey = $strDeviceId;
        if (($intTime + $intExpires) < time()) {
            return false;
        }
        $strFactor = $strCredential . $intTime . $intExpires;

        $arrParams = array();
        foreach ($arrAllParams as $strKey => $value) {
            $arrParams[$strKey] = $strKey . '=' . rawurlencode($value);
        }
        ksort($arrParams);
        $strToSign = strtoupper($strMethod) . "\n" . $strUri . "\n" . implode('&', $arrParams);
        $strSignKey = hash_hmac("sha256", $strFactor, $strSecretKey);
        $strCalcSignature = hash_hmac("sha256", $strToSign, $strSignKey);
        if (false) {
            $strDebug = "\n=== factor ===\n";
            $strDebug .= $strFactor;
            $strDebug .= "\n=== secrect key ===\n";
            $strDebug .= $strSecretKey;
            $strDebug .= "\n=== signature ===\n";
            $strDebug .= $strToSign;
            $strDebug .= "\n============\n";
            $strDebug .= $strSignKey;
            $strDebug .= "\n============\n";
            $strDebug .= $strCalcSignature;
            $strDebug .= "\n============\n";
            LogHelper::trace($strDebug);
            $this->message = $strDebug;
        }

        return strcmp($strCalcSignature, $strSignature) === 0;
    }

    /**
     * Runs the named action.
     * Filters specified via {@link filters()} will be applied.
     *
     * @param string $actionID
     *            action ID
     * @throws CHttpException if the action does not exist or the action name is not proper.
     * @see filters
     * @see createAction
     * @see runAction
     */
    public function run($actionID)
    {
        try {
            parent::run($actionID);
        } catch (ParameterValidationException $e) {
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $arrTraces = explode("\n", $e->getTraceAsString());
            LogHelper::error('exception: ' . $e->getMessage() . ' cause by ' . $arrTraces[0]);
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), Errno::INTERNAL_SERVER_ERROR);
        }
    }

    protected function beforeAction($action)
    {
        $strUid = Yii::app()->user->getId();
        $nickName = Yii::app()->user->getName(); // 用户昵称
        LogHelper::pushLog('userId', $strUid);
        LogHelper::pushLog('nickname', $nickName);
        return true;
    }
}
