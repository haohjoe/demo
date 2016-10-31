<?php

/**
 * 用户账户变动记录.
 * @author xiaoshiyong@camera360.com
 * @date 20151011
 */
class AccountRecordLogic
{
    public $solution;
    
    public $objDataAccountChangeLog;

    public function __construct($solution)
    {
        $this->solution = $solution;
        
        $this->objDataAccountChangeLog = Factory::create('DataAccountChangeLog', array($this->solution));
    }
    
    /**
     * 根据id获取流水
     */
    public function listByIds(array $ids)
    {
        // 获取用户c点
        $records = $this->objDataAccountChangeLog->getByIds($ids);
        if (empty($records)) {
            return array();
        }
        
        return array_values($records);
    }
    
    /**
     * 根据各种条件获取流水
     */
    public function getList($args)
    {
        // 获取用户c点
        $records = $this->objDataAccountChangeLog->getList($args);
        if (empty($records)) {
            return array();
        }
        
        return array_values($records);
    }
}
