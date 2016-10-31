<?php

/**
 * DataAccountChangeLog class file
 *
 * @author xiaoshiyong@camera360.com
 * @date 2014/10/27
 *
 */
class DataAccountChangeLog extends DataBase
{
    public $solution;
    public $dao = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->dao = Factory::create('DaoAccountChangeLog', array($this->solution));
    }
    
    /**
     * 获取多个id的信息
     * @param $uids array 用户uid
     * @return mixed
     */
    public function getByIds(Array $ids)
    {
        if (empty($ids)) {
            return array();
        }
        $arrRst = $this->dao->getByIds($ids);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'DaoAccountChangeLog->getByIds fail',
                'ids' => implode(',', $ids)
            );
            LogWrapper::warning($arrLog);
    
            return false;
        }
    
        return $arrRst;
    }
    
    /**
     * 排序获取列表
     * @param $cTime mixed 起始时间 null | mongodate
     * @return mixed
     */
    public function getList($args)
    {
        $arrRst = $this->dao->getList($args);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'DaoAccountChangeLog->getList fail',
                'args' => json_encode($args)
            );
            LogWrapper::warning($arrLog);
        
            return false;
        }
        
        return $arrRst;
    }
    
    /**
     * 增加记录
     * @param array $data
     * @return boolean
     */
    public function insert(array $data)
    {
        $arrRet = $this->dao->insert($data);
        if (false === $arrRet) {
            $arrLog = array(
                'msg' => 'DaoAccountChangeLog->insert fail',
                'data' => json_encode($data)
            );
            LogWrapper::warning($arrLog);
            
            return false;
        }
        
        return true;
    }

    /**
     * 生成remark信息
     * @param $data array('appname' => 'c360', 'msg' => 'xxx')
     * @return string array('appname::c360|:|msg::xxx')
     */
    public function makeRemark(array $data)
    {
        $tmp = array();
    
        foreach ($data as $k => $v) {
            $v = str_replace(array(DaoAccountChangeLog::REMARK_SEP, '::'), '', $v);
            $tmp[] = $k . '::' . $v;
        }
    
        return implode(DaoAccountChangeLog::REMARK_SEP, $tmp);
    }
}
