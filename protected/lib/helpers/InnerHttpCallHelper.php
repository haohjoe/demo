<?php
class InnerHttpCallHelper
{
    
    private static $jsonErrors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );
    
    public static function execHttpCall($api, array $params, $method, $timeout = 10, $options = array(), &$header = null)
    {
        if ($method=='POST') {
            $jsonRet = HttpHelper::post($api, $params, $timeout, $options);
        } elseif ($method=='GET') {
            $jsonRet = HttpHelper::get($api, $timeout, null, $options, $header);
        } else {
            throw new Exception('unsupported http request method:'.$method, Errno::FATAL);
        }
            
        if ($jsonRet === false) {
            throw new Exception('network failure, curl_exec returned false', Errno::FATAL);
        }
    
        $ret = json_decode($jsonRet, true);
        if (null===$ret) {
            $err = self::jsonLastErrorMsg();
            throw new Exception('json decode failure: '.$err.' caused by '.$jsonRet, Errno::FATAL);
        }
    
        if (!isset($ret['status'])) {
            throw new Exception('unexpected json structure, missing "status":'.$jsonRet, Errno::FATAL);
        }
    
        if (!isset($ret['message'])) {
            throw new Exception('unexpected json structure, missing "message":'.$jsonRet, Errno::FATAL);
        }
    
        if (!isset($ret['data'])) {
            throw new Exception('unexpected json structure, missing "data":'.$jsonRet, Errno::FATAL);
        }
    
        if ($ret['status'] != 200) {
            throw new Exception($ret['message'].' with status '.$ret['status'], $ret['status']);
        }
    
        return $ret['data'];
    }
    
    private static function jsonLastErrorMsg()
    {
        $error = json_last_error();
        return array_key_exists($error, self::$jsonErrors) ? self::$jsonErrors[$error] : "Unknown error ({$error})";
    }
}
