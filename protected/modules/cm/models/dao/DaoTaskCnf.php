<?php

/**
 * 任务配置表
 * @author xiaoshiyong@camera360.com
 * @date 2015/06/12
 */
class DaoTaskCnf extends DbWrapper
{

    const COLLECTION_NAME = 'taskCnf';
    
    const ID = '_id'; // task id 任务id
    const SCORE = 'score'; // float 经验值
    const CPOINT = 'cpoint'; // float c点
    const STEP_SCORE = 'step_score'; // array 阶梯经验值
    const STEP_CPOINT = 'step_cpoint'; // array 阶梯c点
    const STATUS = 'status'; // int 状态
    const START_TIME = 's_time'; // int 开始时间
    const END_TIME = 'e_time'; // int 结束时间
    const START_GRADE = 's_grade'; // int 开始等级
    const END_GRADE = 'e_grade'; // int 结束等级
    const BATCH = 'batch';  // int 是否可批量执行
    const REPEAT = 'repeat'; // int 是否可重复执行
    const R_FLAG = 'r_flag'; // int 周期模式
    const R_RATIO = 'r_ratio'; // int 周期系数
    const R_COUNT = 'r_count'; // int 周期内可执行次数
    const R_CPOINT = 'r_cpoint'; // float 周期内可获得最大c点数
    const R_SCORE = 'r_score'; // float 周期内可获得最大经验值
    const R_FILTER = 'r_filter'; // int 周期内是否过滤操作对象 
    const R_STEP = 'r_step'; // int 阶梯经验、积分是否要考虑是否为周期模式. 
    
    const UPDATE_TIME = 'u_time'; // Int 更新时间

    public function __construct($solution)
    {
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }

    /**
     * @param array $ids task id数组
     * @return array
     */
    public function getByIds(Array $ids)
    {
        $query[self::ID] = array(
            '$in' => $ids
        );
        
        $docs = $this->conn->query($query);
        if (false === $docs) {
            return false;
        }
        self::transform($docs);
        
        $rst = array();
        foreach ($docs as $key => $val) {
            $stepScore = (isset($val['step_score']) && is_array($val['step_score'])) ? $val['step_score'] : array();
            $stepCpoint = (isset($val['step_cpoint']) && is_array($val['step_cpoint'])) ? $val['step_cpoint'] : array();
            ksort($stepScore);
            ksort($stepCpoint);
            
            $rst[$key]['id'] = (int)self::getPorp($val, self::ID);
            $rst[$key]['score'] = self::getPorp($val, self::SCORE, 0);
            $rst[$key]['cpoint'] = self::getPorp($val, self::CPOINT, 0);
            $rst[$key]['step_score'] = $stepScore;
            $rst[$key]['step_cpoint'] = $stepCpoint;
            $rst[$key]['status'] = (int)self::getPorp($val, self::STATUS, 1);
            $rst[$key]['s_time'] = self::getPorp($val, self::START_TIME, '');
            $rst[$key]['e_time'] = self::getPorp($val, self::END_TIME, '');
            $rst[$key]['s_grade'] = (int)self::getPorp($val, self::START_GRADE, -1);
            $rst[$key]['e_grade'] = (int)self::getPorp($val, self::END_GRADE, -1);
            $rst[$key]['batch'] = (int)self::getPorp($val, self::BATCH, 1);
            $rst[$key]['repeat'] = (int)self::getPorp($val, self::REPEAT, 1);
            $rst[$key]['r_flag'] = (int)self::getPorp($val, self::R_FLAG, 0);
            $rst[$key]['r_ratio'] = (int)self::getPorp($val, self::R_RATIO, 0);
            $rst[$key]['r_count'] = (int)self::getPorp($val, self::R_COUNT, -1);
            $rst[$key]['r_cpoint'] = self::getPorp($val, self::R_CPOINT, -1);
            $rst[$key]['r_score'] = self::getPorp($val, self::R_SCORE, -1);
            $rst[$key]['r_filter'] = (int)self::getPorp($val, self::R_FILTER, 0);
            $rst[$key]['r_step'] = (int)self::getPorp($val, self::R_STEP, 0);
            $rst[$key]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
        
        return $rst;
    }
    
    public function getAll()
    {
        $docs = $this->conn->query();
        if (false === $docs) {
            return false;
        }
        self::transform($docs);
        
        $rst = array();
        foreach ($docs as $key => $val) {
            $stepScore = (isset($val['step_score']) && is_array($val['step_score'])) ? $val['step_score'] : array();
            $stepCpoint = (isset($val['step_cpoint']) && is_array($val['step_cpoint'])) ? $val['step_cpoint'] : array();
            ksort($stepScore);
            ksort($stepCpoint);
            
            $rst[$key]['id'] = (int)self::getPorp($val, self::ID);
            $rst[$key]['score'] = self::getPorp($val, self::SCORE, 0);
            $rst[$key]['cpoint'] = self::getPorp($val, self::CPOINT, 0);
            $rst[$key]['step_score'] = $stepScore;
            $rst[$key]['step_cpoint'] = $stepCpoint;
            $rst[$key]['status'] = (int)self::getPorp($val, self::STATUS, 1);
            $rst[$key]['s_time'] = self::getPorp($val, self::START_TIME, '');
            $rst[$key]['e_time'] = self::getPorp($val, self::END_TIME, '');
            $rst[$key]['s_grade'] = (int)self::getPorp($val, self::START_GRADE, -1);
            $rst[$key]['e_grade'] = (int)self::getPorp($val, self::END_GRADE, -1);
            $rst[$key]['batch'] = (int)self::getPorp($val, self::BATCH, 1);
            $rst[$key]['repeat'] = (int)self::getPorp($val, self::REPEAT, 1);
            $rst[$key]['r_flag'] = (int)self::getPorp($val, self::R_FLAG, 0);
            $rst[$key]['r_ratio'] = (int)self::getPorp($val, self::R_RATIO, 0);
            $rst[$key]['r_count'] = (int)self::getPorp($val, self::R_COUNT, -1);
            $rst[$key]['r_cpoint'] = self::getPorp($val, self::R_CPOINT, -1);
            $rst[$key]['r_score'] = self::getPorp($val, self::R_SCORE, -1);
            $rst[$key]['r_filter'] = (int)self::getPorp($val, self::R_FILTER, 0);
            $rst[$key]['r_step'] = (int)self::getPorp($val, self::R_STEP, 0);
            $rst[$key]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
        
        return $rst;
    }
    
    public function setCnf(Array $data)
    {
        ksort($data['stepScore']);
        ksort($data['stepCpoint']);
        
        $query[self::ID] = (int)$data['id'];
        
        $doc[self::ID] = (int)$data['id'];
        $doc[self::SCORE] = $data['score'];
        $doc[self::CPOINT] = $data['cpoint'];
        $doc[self::STEP_SCORE] = $data['stepScore'];
        $doc[self::STEP_CPOINT] = $data['stepCpoint'];
        $doc[self::STATUS] = (int)$data['status'];
        $doc[self::START_TIME] = $data['sTime'];
        $doc[self::END_TIME] = $data['eTime'];
        $doc[self::START_GRADE] = (int)$data['sGrade'];
        $doc[self::END_GRADE] = (int)$data['eGrade'];
        $doc[self::BATCH] = (int)$data['batch'];
        $doc[self::REPEAT] = (int)$data['repeat'];
        $doc[self::R_FLAG] = (int)$data['rFlag'];
        $doc[self::R_RATIO] = (int)$data['rRatio'];
        $doc[self::R_COUNT] = (int)$data['rCount'];
        $doc[self::R_CPOINT] = $data['rCpoint'];
        $doc[self::R_SCORE] = $data['rScore'];
        $doc[self::R_FILTER] = (int)$data['rFilter'];
        $doc[self::R_STEP] = (int)$data['rStep'];
        $doc[self::UPDATE_TIME] = UtilHelper::now();
        
        $ret = $this->conn->updateDoc($query, $doc, false, true);
        
        return $ret;
    }
}
