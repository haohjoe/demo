<?php

/**
* StaticClassMap class file
* 
* @brief 维护lib类的所有静态函数信息列表，方便genStaticMock获取
* @author zhanglu@camera360.com
* @date 2014-04-01
 */
class StaticClassMap
{

    /**
     * @brief array 保存要mock的lib类的所有静态接口信息,手工维护
     */
    private static $arrMap = array();

    /**
     * @brief getMapInfo 根据；类名获取静态接口信息
     * 
     * @param $className 类名            
     * @return 返回类对应的所有静态函数名
     */
    public static function getMapInfo($className)
    {
        return self::$arrMap[$className];
    }
}
