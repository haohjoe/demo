<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/04/18
 */
class Factory
{

    private static $fakeObj = array();

    public static function create($strClassName, $arrClsArgs = array())
    {
        if (defined('TEST') && ! empty(self::$fakeObj[$strClassName])) {
            return array_shift(self::$fakeObj[$strClassName]);
        }
        // 根据$className创建不同类型的对象
        if (! class_exists($strClassName)) {
            LogHelper::warning('msg[class not exist] class[' . $strClassName . ']');
            return null;
        }
        $objRfc = new ReflectionClass($strClassName);
        
        if (empty($arrClsArgs)) {
            $obj = $objRfc->newInstance();
        } else {
            $obj = $objRfc->newInstanceArgs($arrClsArgs);
        }
        
        if (false === $obj) { // create class err
            LogHelper::warning('msg[create class error] class[' . $strClassName . ']');
            return null;
        }
        
        return $obj;
    }

    public static function set($strClassName, $fakeObj)
    {
        self::$fakeObj[$strClassName][] = $fakeObj;
    }

    public static function clear($strClassName)
    {
        self::$fakeObj[$strClassName] = null;
    }
}
