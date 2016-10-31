<?php

/**
 * DataUserTask class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class DataUserTask extends DataBase
{
    public $solution;
    public $objDaoUserTask = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoUserTask = Factory::create('DaoUserTask', array($this->solution));
    }
    
    /**
     * 根据任务id获取某个用户的任务列表
     * @param string $uid
     * @param array $arrTaskIds
     * @return array
     */
    public function getUserTaskByTaskIds($uid, array $tids)
    {
        if (empty($tids)) {
            return array();
        }
        $ids = array();
        foreach ($tids as $v) {
            $ids[] = $uid . '_' . $v;
        }
        
        $data = $this->objDaoUserTask->getByIds($ids);
        if (false === $data) {
            $arrLog = array(
                'msg' => 'objDaoUserTask->getByIds fail'
            );
            LogWrapper::warning($arrLog);
            return false;
        }
        $arrRet = array();
        foreach ($data as $v) {
            $arrRet[$v['t_id']] = $v;
        }
        
        return $arrRet;
    }
    
    /**
     * 增加用户一条任务记录
     * @param array $arrNewUserTask 新任务数组
     * @return boolean
     */
    public function insert(array $arrNewUserTask)
    {
        if (false === $this->objDaoUserTask->insert($arrNewUserTask)) {
            $arrLog = array(
                'msg' => 'objDaoUserTask->insert'
            );
            LogWrapper::warning($arrLog);
            return false;
        }
        
        return true;
    }
    
    /**
     * 增加用户任务中的经验值和c点 
     * @param array $arrNewUserTask
     * @return boolean
     */
    public function incScoreAndCpoint(array $arrNewUserTask)
    {
        if (false === $this->objDaoUserTask->incScoreAndCpoint($arrNewUserTask)) {
            $arrLog = array(
                'msg' => 'objDaoUserTask->incScoreAndCpoint'
            );
            LogWrapper::warning($arrLog);
            return false;
        }
        
        return true;
    }

    /**
     * 更新数据
     */
    public function updateData(array $arrNewUserTask)
    {
        if (false === $this->objDaoUserTask->updateData($arrNewUserTask)) {
            $arrLog = array(
                'msg' => 'objDaoUserTask->updateData'
            );
            LogWrapper::warning($arrLog);
            return false;
        }

        return true;
    }
}
