<?php

/**
 * 配置
 * @author xiaoshiyong@camera360.com
 * @date 20150611
 */
class CnfLogic
{
    public $solution;
    
    public $objDataCnf;
    
    public function __construct($solution)
    {
        $this->solution = $solution;
        
        $this->objDataCnf = Factory::create('DataCnf', array($this->solution));
    }
    
    /**
     * 更新任务配置
     * @param array $args
     */
    public function taskCnfUpdate(array $args)
    {
        // 根据id判断配置是否存在
        if (empty(Yii::app()->params['task'])) {
            throw new Exception('Task config is empty', Errno::INTERNAL_SERVER_ERROR);
            LogHelper::error('Get task config from file but is empty with solution '. $this->solution);
        }
        $ids = array();
        foreach (Yii::app()->params['task'] as $val) {
            $ids[] = $val['id'];
        }
        if (!in_array($args['id'], $ids)) {
            LogHelper::warning('Task id is not exists,id=' . $args['id']);
            throw new ParameterValidationException('Id is not exists');
        }
        
        // 对阶梯数据进行转换
        $args['stepScore'] = $this->fStep($args['stepScore']);
        $args['stepCpoint'] = $this->fStep($args['stepCpoint']);
        
        // 进行更新操作
        $rst = $this->objDataCnf->taskCnfUpdate($args);
        
        return $rst;
    }
    
    /**
     * 获取任务配置列表
     */
    public function taskCnfList()
    {
        if (empty(Yii::app()->params['task'])) {
            throw new Exception('Task config is empty', Errno::INTERNAL_SERVER_ERROR);
            LogHelper::error('Get task config from file but is empty with solution '. $this->solution);
        }
        
        $rst = $this->objDataCnf->taskCnfList(); 
        
        return $rst;
    }
    
    /**
     * 获取等级配置列表
     */
    public function gradeCnfList()
    {
        if (empty(Yii::app()->params['grade'])) {
            throw new Exception('Grade config is empty', Errno::INTERNAL_SERVER_ERROR);
            LogHelper::error('Get grade config from file but is empty with solution '. $this->solution);
        }
        
        $rst = Yii::app()->params['grade'];
        
        return $rst;
    }
    
    /**
     * 格式化阶梯数据
     */
    private function fStep($data)
    {
        $fData = array();
        if (empty($data)) {
            return $fData;
        }
        
        $data = explode(',', $data);
        $n = 0;
        foreach ($data as $v) {
            $arr = explode(':', $v);
            if (count($arr) != 2) {
                throw new ParameterValidationException('step data valider failed');
            }
            if (0 == $n ++ && $arr[0] != 1) {
                throw new ParameterValidationException('step data of first setp must 1');
            }
            
            $fData[intval($arr[0])] = floatval($arr[1]);
        }
        
        return $fData;
    }
}
