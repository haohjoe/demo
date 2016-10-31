<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/11/26
 */
class DaoUserCpoint extends DbWrapper
{

    const COLLECTION_NAME = 'userCpoint';
    
    const ID = '_id'; // uid
    const CPOINT = 'cpoint'; // float
    const UPDATE_TIME = 'u_time'; // Int

    public function __construct($solution)
    {
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }
    
    /**
     * 获取某个用户的c点
     * @param string $id
     * @return array
     */
    public function getById($id)
    {
        $query[self::ID] = $id;
        
        $doc = $this->conn->findOne($query);
        if (false === $doc) {
            return false;
        }
        if (empty($doc)) {
            return array();
        }
        self::transform($doc);
        $rst['id'] = self::getPorp($doc, self::ID);
        $rst['cpoint'] = self::getPorp($doc, self::CPOINT, 0);
        $rst['u_time'] = self::getPorp($doc, self::UPDATE_TIME, 0);
        
        return $rst;
    }
    
    /**
     * 获取多个用户的c点
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
        foreach ($docs as $key => $val) {
            $rst[$key]['id'] = self::getPorp($val, self::ID);
            $rst[$key]['cpoint'] = self::getPorp($val, self::CPOINT, 0);
            $rst[$key]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
        
        return $rst;
    }
    
    /**
     * 排序获取用户c点信息
     * @param $skip int 跳过的数量 (此值不要过大)
     * @param $limit int 返回数
     * @param $sort int 排序方式，1：正序；-1：倒序
     * @return mixed
     */
    public function getSortList($skip, $limit, $sort)
    {
        $rst = $docs = array();
        
        $cursor = $this->conn->find()->sort(array(self::CPOINT => $sort));
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
            $rst[$id]['cpoint'] = self::getPorp($val, self::CPOINT, 0);
            $rst[$id]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
        }
        
        return $rst;
    }
    
    public function addCpoint($strUid, $cpoint)
    {
        $query[self::ID] = $strUid;
        
        $doc['$inc'] = array(
            self::CPOINT => $cpoint,
        );
        $doc['$set'] = array(
            self::UPDATE_TIME => UtilHelper::now()
        );
        
        $rst = $this->conn->updateDoc($query, $doc, false, true);
        
        return $rst;
    }
    
    public function reduceCpoint($strUid, $cpoint)
    {
        $query[self::ID] = $strUid;
        $query[self::CPOINT] = array(
            '$gte' => $cpoint,
        );
        
        $doc['$inc'] = array(
            self::CPOINT => -$cpoint,
        );
        $doc['$set'] = array(
            self::UPDATE_TIME => UtilHelper::now()
        );
        
        $rst = $this->conn->updateDocV2($query, $doc, false, true);
        
        return $rst;
    }

    public function find()
    {
        $cursor = $this->conn->find();

        return $cursor;
    }
}
