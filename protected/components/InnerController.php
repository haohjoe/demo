<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class InnerController extends CController
{
    // 处理的消息代码
    protected $arrCode = array(
        'module.operation'
    );
    protected $_opcode = '';
    protected $_opuid = '';
    protected $_optime = 0;
    protected $_opinfo = array();
    protected $_msgid = 0;

    public function init()
    {
        parent::init();
        
        // 增加Ajax标识，用于异常处理
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) == false) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }
        
        if (! defined('MODULE_NAME')) {
            define('MODULE_NAME', 'cm'); 
        }
        LogHelper::init();
        
        if (isset($_REQUEST['opcode']) && ! isset($_REQUEST['code'])) {
            $_REQUEST['code'] = $_REQUEST['opcode'];
            $_POST['code'] = $_POST['opcode'];
        }
        if (isset($_REQUEST['opuid']) && ! isset($_REQUEST['uid'])) {
            $_REQUEST['uid'] = $_REQUEST['opuid'];
            $_POST['uid'] = $_POST['opuid'];
        }
        if (isset($_REQUEST['optime']) && ! isset($_REQUEST['time'])) {
            $_REQUEST['time'] = $_REQUEST['optime'];
            $_POST['time'] = $_POST['optime'];
        }
        if (isset($_REQUEST['opinfo']) && ! isset($_REQUEST['info'])) {
            $_REQUEST['info'] = $_REQUEST['opinfo'];
            $_POST['info'] = $_POST['opinfo'];
        }
        if (isset($_REQUEST['f']) && $_REQUEST['f'] == 'mq') {
            MqHelper::parse();
            LogHelper::pushLog('msgid', isset($_REQUEST['msgid']) ? $_REQUEST['msgid'] : 0);
            LogHelper::pushLog('code', isset($_REQUEST['code']) ? $_REQUEST['code'] : '');
            LogHelper::pushLog('uid', isset($_REQUEST['uid']) ? $_REQUEST['uid'] : '');
            LogHelper::pushLog('time', isset($_REQUEST['time']) ? $_REQUEST['time'] : 0);
            LogHelper::pushLog('info', isset($_REQUEST['info']) ? $_REQUEST['info'] : '');
        } else {
            LogHelper::pushLog('params', $_REQUEST);
        }
    }
    
    public function actionIndex()
    {
        try {
            $this->_opcode = ControllerParameterValidator::validateString($_POST, 'code');
            $this->_opuid = ControllerParameterValidator::validateString($_POST, 'uid');
            $this->_optime = ControllerParameterValidator::validateFloat($_POST, 'time');
            $this->_msgid = ControllerParameterValidator::validateInteger($_POST, 'msgid');
            $arrInfo = $_POST['info'];
            
            if (! is_array($arrInfo)) {
                throw new ParameterValidationException('param info must be array and cannot be empty!');
            }
            $this->_opinfo = $arrInfo;
            
            // check
            if (! in_array($this->_opcode, $this->arrCode)) {
                throw new ParameterValidationException('param code not valid!');
            }
            
            $func = '';
            foreach (explode('.', $this->_opcode) as $v) {
                $func .= empty($func) ? $v : ucfirst($v);
            }
            // $funcRet = call_user_func(array($this, $func));
            try {
                $funcRet = $this->$func();
            } catch (Exception $ex) {
                LogHelper::warning('msg handle exception, ' . $ex->getMessage());
                $funcRet = false;
            }
        } catch (ParameterValidationException $e) {
            LogHelper::warning('msg igore, ' . $e->getMessage());
            $funcRet = true;
        } catch (Exception $e) {
            LogHelper::warning($e->getMessage());
            $funcRet = false;
        }
        if ($funcRet === true) {
            ResponseHelper::outputJsonV2(array());
        } else {
            ResponseHelper::outputJsonV2(array(), 'retry', Errno::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * module.operation 事件的处理函数
     * 
     * @return bool 若为false,会导致服务端重试
     */
    protected function moduleOperation()
    {
        return true;
    }

    public function run($actionID)
    {
        try {
            parent::run($actionID);
        } catch (ParameterValidationException $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            LogHelper::error(@json_encode(debug_backtrace()));
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            LogHelper::error($e->getMessage() . ' with code ' . $e->getCode());
            LogHelper::error(@json_encode(debug_backtrace()));
            ResponseHelper::outputJsonV2(array(), $e->getMessage(), $e->getCode());
        }
    }
}
