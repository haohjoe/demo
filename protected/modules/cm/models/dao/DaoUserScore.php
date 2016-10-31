<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/11/26
 */
class DaoUserScore extends DbWrapper
{
    public $solution;

    const COLLECTION_NAME = 'userScore';
    
    const ID = '_id'; // uid
    const SCORE = 'score'; // float
    const FLAG = 'flag'; // Int
    const UPDATE_TIME = 'u_time'; // Int

    const GRADES = 'grades'; // Array

    public function __construct($solution)
    {
        $this->solution = $solution;
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }
    
    /**
     * 获取某个用户的经验值
     * @param string $id
     * @return array
     */
    public function getById($id)
    {
        $query[self::ID] = $id;

        $pk = 'mongo.mb_cm_' . $this->solution . '.findOne';
        LogHelper::profileStart($pk);
        $doc = $this->conn->findOne($query);
        LogHelper::profileEnd($pk);

        if (false === $doc) {
            return false;
        }
        if (empty($doc)) {
            return array();
        }
        self::transform($doc);
        $rst['id'] = self::getPorp($doc, self::ID);
        $rst['score'] = self::getPorp($doc, self::SCORE, 0);
        $rst['grades'] = self::getPorp($doc, self::GRADES, array());
        $rst['flag'] = self::getPorp($doc, self::FLAG, 0);
        $rst['u_time'] = self::getPorp($doc, self::UPDATE_TIME, 0);
        
        return $rst;
    }
    
    /**
     * 获取多个用户的经验值
     * @param Array $ids
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
        if (empty($docs)) {
            return array();
        }
        self::transform($docs);
        foreach ($docs as $val) {
            $id = self::getPorp($val, self::ID);
            $rst[$id]['id'] = $id;
            $rst[$id]['score'] = self::getPorp($val, self::SCORE, 0);
            $rst[$id]['grades'] = self::getPorp($val, self::GRADES, 0);
            $rst[$id]['flag'] = self::getPorp($val, self::FLAG, 0);
            $rst[$id]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
    
        return $rst;
    }
    
    /**
     * 排序获取用户经验值
     * @param $skip int 跳过的数量 (此值不要过大)
     * @param $limit int 返回数
     * @param $sort int 排序方式，1：正序；-1：倒序
     * @return mixed
     */
    public function getSortList($skip, $limit, $sort)
    {
        $rst = $docs = array();
    
        $cursor = $this->conn->find()->sort(array(self::SCORE => $sort));
        while ($cursor->hasNext()) {
            $cursor->next();
            if (0 < $skip--) {
                continue;
            }
            if (--$limit < 0) {
                break;
            }
            $docs[] = $cursor->current();
        }
        self::transform($docs);
        foreach ($docs as $val) {
            $id = self::getPorp($val, self::ID);
            $rst[$id]['id'] = $id;
            $rst[$id]['score'] = self::getPorp($val, self::SCORE, 0);
            $rst[$id]['grades'] = self::getPorp($val, self::GRADES, 0);
            $rst[$id]['flag'] = self::getPorp($val, self::FLAG, 0);
            $rst[$id]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
    
        return $rst;
    }
    
    /**
     * 排序获取一个经验值区间段的用户经验值记录
     * @param $startScore float 起始经验值
     * @param $endScore float 结束经验值
     * @param $skip int 跳过的数量 (此值不要过大)
     * @param $limit int 返回数
     * @param $sort int 排序方式，1：正序；-1：倒序
     * @return mixed
     */
    public function getSortListByScoreSection($startScore, $endScore, $skip, $limit, $sort)
    {
        $rst = $docs = array();
        
        $query[self::SCORE] = array(
            '$gte' => $startScore,
        );
        if ($endScore != null) {
            $query[self::SCORE] = array(
                '$lt' => $endScore
            );
        }
        
        $cursor = $this->conn->find($query)->sort(array(self::SCORE => $sort));
        while ($cursor->hasNext()) {
            $cursor->next();
            if (0 < $skip--) {
                continue;
            }
            if (--$limit < 0) {
                break;
            }
            $docs[] = $cursor->current();
        }
        
        self::transform($docs);
        foreach ($docs as $val) {
            $id = self::getPorp($val, self::ID);
            $rst[$id]['id'] = $id;
            $rst[$id]['score'] = self::getPorp($val, self::SCORE, 0);
            $rst[$id]['grades'] = self::getPorp($val, self::GRADES, 0);
            $rst[$id]['flag'] = self::getPorp($val, self::FLAG, 0);
            $rst[$id]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
    
        return $rst;
    }
    
    /**
     * 添加经验值
     * @param string $strUid
     * @param int $score
     * @param array $arrGrades
     * @return boolean
     */
    public function addScore($strUid, $score, $arrGrades)
    {
        $query[self::ID] = $strUid;
        
        $doc['$inc'] = array(
            self::SCORE => $score
        );
        if (! empty($arrGrades)) {
            $doc['$pushAll'] = array(
                self::GRADES => $arrGrades
            );
            $doc['$set'][self::FLAG] = 1;
        }
        $doc['$set'][self::UPDATE_TIME] = UtilHelper::now();
        
        $rst = $this->conn->updateDoc($query, $doc, false, true);
        
        return $rst;
    }
}
