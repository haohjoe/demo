<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/11/26
 */
class DaoUserTask extends DbWrapper
{

    const COLLECTION_NAME = 'userTask';
    
    const ID = '_id';               // uid_taskId
    const UID = 'uid';              // MongoId
    const TASK_ID = 't_id';         // Int
    const SCORE = 'score';          // float
    const CPOINT = 'cpoint';        // float
    const COUNTER = 'counter';      // int 总次数
    const CREATE_TIME = 'c_time';   // Int
    const UPDATE_TIME = 'u_time';   // Int
    /**
     * actions:
     *     array(
     *         't_id' => 1,
     *         'target' => '', // 操作对象：比如图片id、用户id、分享渠道id
     *         'c_time' => 0,
     *         'cpoint' => 0,
     *         'score' => 0
     *     )
     * )
     */
    const ACTIONS = 'actions'; // 记录用户已做过的周期内记录.

    public function __construct($solution)
    {
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }

    public function getByIds(array $ids)
    {
        $query[self::ID] = array(
            '$in' => $ids
        );
        
        $doc = $this->conn->query($query);
        if (false === $doc) {
            return false;
        }
        self::transform($doc);
        $ret = array();
        foreach ($doc as $key => $val) {
            $ret[$key]['id'] = self::getPorp($val, self::ID);
            $ret[$key]['uid'] = self::getPorp($val, self::UID, '');
            $ret[$key]['t_id'] = self::getPorp($val, self::TASK_ID, 0);
            $ret[$key]['score'] = self::getPorp($val, self::SCORE, 0);
            $ret[$key]['cpoint'] = self::getPorp($val, self::CPOINT, 0);
            $ret[$key]['counter'] = self::getPorp($val, self::COUNTER, 0);
            $ret[$key]['c_time'] = self::getPorp($val, self::CREATE_TIME, 0);
            $ret[$key]['u_time'] = self::getPorp($val, self::UPDATE_TIME, 0);
            $ret[$key]['actions'] = self::getPorp($val, self::ACTIONS, array());
        }
        
        return $ret;
    }

    public function insert(array $arrUserTask)
    {
        $doc[self::ID] = $arrUserTask['uid'] . '_' . $arrUserTask['t_id'];
        $doc[self::UID] = $arrUserTask['uid'];
        $doc[self::TASK_ID] = $arrUserTask['t_id'];
        $doc[self::SCORE] = $arrUserTask['score'];
        $doc[self::CPOINT] = $arrUserTask['cpoint'];
        $doc[self::COUNTER] = 1;
        $doc[self::CREATE_TIME] = $arrUserTask['c_time'];
        $doc[self::UPDATE_TIME] = $arrUserTask['u_time'];
        $doc[self::ACTIONS] = $arrUserTask['actions'];
        
        $ret = $this->conn->add($doc);
        
        return $ret;
    }

    public function incScoreAndCpoint(array $arrUserTask)
    {
        $query[self::ID] = $arrUserTask['id'];
        
        $doc['$inc'] = array(
            self::SCORE => $arrUserTask['score'],
            self::CPOINT => $arrUserTask['cpoint'],
            self::COUNTER => $arrUserTask['incCounter']
        );
        $doc['$set'] = array(
            self::UPDATE_TIME => $arrUserTask['u_time'],
            self::ACTIONS => $arrUserTask['actions']
        );
        
        $ret = $this->conn->updateDoc($query, $doc, false, false);
        
        return $ret;
    }

    public function updateData(array $arrUserTask)
    {
        $query[self::ID] = $arrUserTask['uid'] . '_' . $arrUserTask['t_id'];

        $doc['$inc'] = array(
            self::SCORE => $arrUserTask['score'],
            self::CPOINT => $arrUserTask['cpoint'],
            self::COUNTER => $arrUserTask['incCounter']
        );
        $doc['$set'] = array(
            self::UID => $arrUserTask['uid'],
            self::TASK_ID => $arrUserTask['t_id'],
            self::UPDATE_TIME => $arrUserTask['u_time'],
            self::CREATE_TIME => $arrUserTask['c_time'],
            self::ACTIONS => $arrUserTask['actions']
        );

        $ret = $this->conn->updateDoc($query, $doc, false, true);

        return $ret;
    }
}
