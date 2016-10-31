<?php

/**
 * DataUserCpoint class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class DataUserCpoint
{
    public $solution;
    public $objDaoUserCpoint = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoUserCpoint = Factory::create('DaoUserCpoint', array($this->solution));
    }
    
    /**
     * 获取单个用户的经验值
     * @param $uid string 用户uid
     */
    public function getByUid($uid)
    {
        $arrRet = $this->objDaoUserCpoint->getById($uid);
        if (false === $arrRet) {
            $arrLog = array(
                'msg' => 'objDaoUserCpoint->getById fail'
            );
            LogWrapper::warning($arrLog);
            
            return false;
        }
        
        return $arrRet;
    }
    
    /**
     * 获取多个用户的c币
     * @param $uids array 用户uid
     * @return mixed
     */
    public function getByUids(Array $uids)
    {
        if (empty($uids)) {
            return array();
        }
        $arrRst = $this->objDaoUserCpoint->getByIds($uids);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserCpoint->getByIds fail'
            );
            LogWrapper::warning($arrLog);
    
            return false;
        }
    
        return $arrRst;
    }
    
    /**
     * 排序获取用户c币信息
     * @return mixed
     */
    public function getSortList($page, $limit, $sort)
    {
        $skip = max(0, ($page - 1) * $limit);
        $arrRst = $this->objDaoUserCpoint->getSortList($skip, $limit, $sort);
        if (false === $arrRst) {
            $arrLog = array(
                'msg' => 'objDaoUserCpoint->getSortList fail'
            );
            LogWrapper::warning($arrLog);

            return false;
        }

        return $arrRst;
    }
    
    /**
     * 增加c币
     * @param string $strUid
     * @param float $addCpoint
     * @return boolean
     */
    public function addCpoint($strUid, $addCpoint)
    {
        $arrRet = $this->objDaoUserCpoint->addCpoint($strUid, $addCpoint);
        if (false === $arrRet) {
            $arrLog = array(
                'msg' => 'objDaoUserCpoint->addCpoint fail',
                'uid' => $strUid,
                'cpoint' => $addCpoint
            );
            LogWrapper::warning($arrLog);

            return false;
        }

        return true;
    }
    
    /**
     * 减少c币
     * @param string $strUid
     * @param float $cpoint
     * @return boolean
     */
    public function reduceCpoint($strUid, $cpoint)
    {
        return $this->objDaoUserCpoint->reduceCpoint($strUid, $cpoint);
    }
}
