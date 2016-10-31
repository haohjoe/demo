<?php

/**
 * DataCnf class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class DataCnf extends DataBase
{
    public $solution;
    public $objDaoTaskCnf;
    
    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoTaskCnf = Factory::create('DaoTaskCnf', array($this->solution));
    }

    /**
     * 识别task，映射op到task
     * 
     * @param string $strOp
     * @return Array
     */
    public function mapOp2Tasks($strOp)
    {
        $arrTasks = array();
        
        // 获取配置优先来源
        $source = Yii::app()->params['solutionCnfSource'][$this->solution];
        $taskConfig = Yii::app()->params['task'];
        
        foreach ($taskConfig as $t) {
            if (in_array($strOp, $t['ops']) && ! isset($arrTasks[$t['id']])) {
                $arrTasks[$t['id']] = $t;
            }
        }
        if ($source == 'db' && !empty($arrTasks)) { 
            // 优先从数据库中获取详细配置信息
            $cnfs = $this->objDaoTaskCnf->getByIds(array_keys($arrTasks));
            // 重新组装数据
            foreach ($arrTasks as $tid => &$arrTask) {
                $arrTask['score'] = $cnfs[$tid]['score'];
                $arrTask['cpoint'] = $cnfs[$tid]['cpoint'];
                $arrTask['step_score'] = $cnfs[$tid]['step_score'];
                $arrTask['step_cpoint'] = $cnfs[$tid]['step_cpoint'];
                $arrTask['status'] = $cnfs[$tid]['status'];
                $arrTask['rule']['s_time'] = $cnfs[$tid]['s_time'];
                $arrTask['rule']['e_time'] = $cnfs[$tid]['e_time'];
                $arrTask['rule']['s_grade'] = $cnfs[$tid]['s_grade'];
                $arrTask['rule']['batch'] = $cnfs[$tid]['batch'];
                $arrTask['rule']['repeat'] = $cnfs[$tid]['repeat'];
                $arrTask['rule']['r_flag'] = $cnfs[$tid]['r_flag'];
                $arrTask['rule']['r_ratio'] = $cnfs[$tid]['r_ratio'];
                $arrTask['rule']['r_count'] = $cnfs[$tid]['r_count'];
                $arrTask['rule']['r_cpoint'] = $cnfs[$tid]['r_cpoint'];
                $arrTask['rule']['r_score'] = $cnfs[$tid]['r_score'];
                $arrTask['rule']['r_filter'] = $cnfs[$tid]['r_filter'];
                $arrTask['rule']['r_step'] = $cnfs[$tid]['r_step'];
                $arrTask['rule']['u_time'] = $cnfs[$tid]['u_time'];
            }
        }
        
        return $arrTasks;
    }
    
    /**
     * 获取配置列表信息
     */
    public function taskCnfList()
    {
        $out = array();
        
        // 获取配置优先来源
        $source = Yii::app()->params['solutionCnfSource'][$this->solution];
        $taskParams = Yii::app()->params['task'];
        if ($source === 'file') {
            foreach ($taskParams as $tp) {
                $out[] = array(
                    'id' => $tp['id'],
                    'task_type' => $tp['task_type'],
                    'name' => $tp['name'], 
                    'desc' => $tp['desc'], 
                    'score' => $tp['score'], 
                    'cpoint' => $tp['cpoint'], 
                    'step_score' => $tp['step_score'], 
                    'step_cpoint' => $tp['step_cpoint'], 
                    'status' => $tp['status'], 
                    's_time' => $tp['rule']['s_time'], 
                    'e_time' => $tp['rule']['e_time'], 
                    's_grade' => $tp['rule']['s_grade'], 
                    'e_grade' => $tp['rule']['e_grade'], 
                    'batch' => $tp['rule']['batch'], 
                    'repeat' => $tp['rule']['repeat'], 
                    'r_flag' => $tp['rule']['r_flag'], 
                    'r_ratio' => $tp['rule']['r_ratio'], 
                    'r_count' => $tp['rule']['r_count'], 
                    'r_cpoint' => $tp['rule']['r_cpoint'], 
                    'r_score' => $tp['rule']['r_score'], 
                    'r_filter' => $tp['rule']['r_filter'], 
                    'r_step' => $tp['rule']['r_step'], 
                );    
            }
        }
        
        if ($source === 'db') {
            $tasks = $this->objDaoTaskCnf->getAll();
            foreach ($taskParams as $tp) {
                $tid = $tp['id'];
                $out[] = array(
                    'id' => $tid,
                    'task_type' => $tp['task_type'],
                    'name' => $tp['name'],
                    'desc' => $tp['desc'],
                    'score' => isset($tasks[$tid]['score']) ? $tasks[$tid]['score'] : 0,
                    'cpoint' => isset($tasks[$tid]['cpoint']) ? $tasks[$tid]['cpoint'] : 0,
                    'step_score' => isset($tasks[$tid]['step_score']) ? $tasks[$tid]['step_score'] : 0,
                    'step_cpoint' => isset($tasks[$tid]['step_cpoint']) ? $tasks[$tid]['step_cpoint'] : 0,
                    'status' => isset($tasks[$tid]['status']) ? $tasks[$tid]['status'] : 0,
                    's_time' => isset($tasks[$tid]['s_time']) ? $tasks[$tid]['s_time'] : '',
                    'e_time' => isset($tasks[$tid]['e_time']) ? $tasks[$tid]['e_time'] : '',
                    's_grade' => isset($tasks[$tid]['s_grade']) ? $tasks[$tid]['s_grade'] : -1,
                    'e_grade' => isset($tasks[$tid]['e_grade']) ? $tasks[$tid]['e_grade'] : -1,
                    'batch' => isset($tasks[$tid]['batch']) ? $tasks[$tid]['batch'] : 0,
                    'repeat' => isset($tasks[$tid]['repeat']) ? $tasks[$tid]['repeat'] : 0,
                    'r_flag' => isset($tasks[$tid]['r_flag']) ? $tasks[$tid]['r_flag'] : 0,
                    'r_ratio' => isset($tasks[$tid]['r_ratio']) ? $tasks[$tid]['r_ratio'] : 0,
                    'r_count' => isset($tasks[$tid]['r_count']) ? $tasks[$tid]['r_count'] : 0,
                    'r_cpoint' => isset($tasks[$tid]['r_cpoint']) ? $tasks[$tid]['r_cpoint'] : -1,
                    'r_score' => isset($tasks[$tid]['r_score']) ? $tasks[$tid]['r_score'] : -1,
                    'r_filter' => isset($tasks[$tid]['r_filter']) ? $tasks[$tid]['r_filter'] : 0,
                    'r_step' => isset($tasks[$tid]['r_step']) ? $tasks[$tid]['r_step'] : 0,
                );
            }
        }
        
        return $out;
    }
    
    /**
     * 更新配置信息
     */
    public function taskCnfUpdate($args)
    {
        // 获取配置优先来源
        $source = Yii::app()->params['solutionCnfSource'][$this->solution];
        if ($source === 'file') {
            throw new ParameterValidationException("App config can't modify.");
        }
        
        $rst = $this->objDaoTaskCnf->setCnf($args);
        if (false === $rst) {
            $arrLog = array(
                'msg' => 'objDaoTaskCnf->update fail'
            );
            LogWrapper::warning($arrLog);
        
            return false;
        }
        
        return $rst;
    }
}
