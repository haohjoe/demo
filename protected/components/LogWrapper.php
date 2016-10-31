<?php

/**
 * @brief log工具类，封装固有的日志字段
 * @author zhanglu@camera360.com
 * @date 2014/03/13
 */
class LogWrapper extends LogHelper
{
    // 调用方式，可以传数组，也可以传字符串
    public static function info($arrArgs, $strMethod = 'default', $category = '')
    {
        $strLog = 'method[' . $strMethod . '] timestamp[' . time() . ']';
        
        if (is_array($arrArgs)) {
            foreach ($arrArgs as $strKey => $strValue) {
                $strLog .= " {$strKey}[{$strValue}]";
            }
        } else {
            $strLog .= " msg[{$arrArgs}]";
        }
        parent::info($strLog, $category);
    }

    public static function trace($arrArgs, $strMethod = 'default', $category = '')
    {
        $strLog = 'method[' . $strMethod . '] timestamp[' . time() . ']';
        
        if (is_array($arrArgs)) {
            foreach ($arrArgs as $strKey => $strValue) {
                $strLog .= " {$strKey}[{$strValue}]";
            }
        } else {
            $strLog .= " msg[{$arrArgs}]";
        }
        parent::info($strLog, $category);
    }

    public static function warning($arrArgs, $strMethod = 'default', $category = '')
    {
        $strLog = 'method[' . $strMethod . '] timestamp[' . time() . ']';
        
        if (is_array($arrArgs)) {
            foreach ($arrArgs as $strKey => $strValue) {
                $strLog .= " {$strKey}[{$strValue}]";
            }
        } else {
            $strLog .= " msg[{$arrArgs}]";
        }
        parent::warning($strLog, $category);
    }

    public static function error($arrArgs, $strMethod = 'default', $category = '')
    {
        $strLog = 'method[' . $strMethod . '] timestamp[' . time() . ']';
        
        if (is_array($arrArgs)) {
            foreach ($arrArgs as $strKey => $strValue) {
                $strLog .= " {$strKey}[{$strValue}]";
            }
        } else {
            $strLog .= " msg[{$arrArgs}]";
        }
        parent::error($strLog, $category);
    }
}
