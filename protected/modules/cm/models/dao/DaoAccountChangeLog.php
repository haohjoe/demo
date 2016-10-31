<?php

/**
 * @author xiaoshiyong@camera360.com
 * @date 2015/10/1027
 */
class DaoAccountChangeLog extends DbWrapper
{

    const COLLECTION_NAME = 'accountChangeLog';

    const ID = '_id'; // MongoId
    const UID = 'uid'; // MongoId
    const TYPE = 'type'; // int (0: cpoint;1:score)
    const APP_NAME = 'appname'; // string
    const AMOUNT = 'amount'; // float (+:入账；-:出账)
    const REMARK = 'remark'; // string 备注
    const CREATE_TIME = 'c_time'; // MongoDate
    const OPTION = 'op'; // string 做任务时的option(出账字段无)
    const TARGET = 'target'; // string (出账字段无 || target = '' 时也无)
    const AO_ID = 'ao_id'; // string (入账字段无)
    const ADMIN_ID = 'aid'; // 管理员+、-c币/经验值时传入的adminId

    // 建立一个特殊索引，便于查询某个用户在某个特定时间类操作了指定target的动作(只有入账时会发生)
    // @todo uid_op_target
    const INDEX = 'idx';

    const TYPE_CPOINT = 0;
    const TYPE_SCORE = 1;
    const REMARK_SEP = '|:|'; // remark信息分隔符 
    
    public function __construct($solution)
    {
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }

    public function getByIds(array $ids)
    {
        $query[self::ID] = array(
            '$in' => $ids
        );
        
        $docs = $this->conn->query($query);
        if (false === $docs) {
            return false;
        }
        self::transform($docs);
        $rtn = array();
        foreach ($docs as $key => $val) {
            $rtn[$key]['id'] = self::getPorp($val, self::ID);
            $rtn[$key]['uid'] = self::getPorp($val, self::UID);
            $rtn[$key]['appname'] = self::getPorp($val, self::APP_NAME);
            $rtn[$key]['type'] = self::getPorp($val, self::TYPE);
            $rtn[$key]['amount'] = self::getPorp($val, self::SCORE);
            $rtn[$key]['remark'] = self::getPorp($val, self::REMARK);
            $rtn[$key]['c_time'] = self::getPorp($val, self::CREATE_TIME);
            $rtn[$key]['op'] = self::getPorp($val, self::OPTION, '');
            $rtn[$key]['target'] = self::getPorp($val, self::TARGET, '');
            $rtn[$key]['ao_id'] = self::getPorp($val, self::AO_ID, '');
            $rtn[$key]['aid'] = self::getPorp($val, self::ADMIN_ID, '');
        }
        
        return $rtn;
    }

    /**
     * 根据各种条件获取列表，注意索引问题
     * @param array $args
     * @return array
     */
    public function getList(array $args)
    {
        $query = array();
        if ($args['st'] !== null) {
            $query[self::CREATE_TIME]['gte'] = UtilHelper::float2MongoDate($args['st']);
        }
        if ($args['et'] !== null) {
            $query[self::CREATE_TIME]['lt'] = UtilHelper::float2MongoDate($args['et']);
        }
        if ($args['type'] !== null) {
            $query[self::TYPE] = $args['type'] == self::TYPE_CPOINT ? self::TYPE_CPOINT : self::TYPE_SCORE;
        }
        if ($args['uid']) {
            $query[self::UID] = new MongoId($args['uid']);
        }
        if ($args['op'] !== '') {
            $query[self::OPTION] = $args['op'];
        }
        if ($args['appname'] !== '') {
            $query[self::APP_NAME] = $args['appname'];
        }
        if ($args['target'] !== '') {
            $query[self::TARGET] = $args['target'];
        }

        if ($args['ioType']) {
            if ($args['ioType'] == 'in') { // 入账
                $query[self::AMOUNT] = array('gt' => 0);
            } else { // 出账
                $query[self::AMOUNT] = array('lt' => 0);
            }
        }

        $sort = array(
            self::CREATE_TIME => -1
        );
        $docs = $this->conn->query($query, array(), $sort, $args['limit']);
        if (false === $docs) {
            return false;
        }
        self::transform($docs);
        $rtn = array();
        foreach ($docs as $key => $val) {
            $rtn[$key]['id'] = self::getPorp($val, self::ID);
            $rtn[$key]['uid'] = self::getPorp($val, self::UID);
            $rtn[$key]['appname'] = self::getPorp($val, self::APP_NAME);
            $rtn[$key]['type'] = self::getPorp($val, self::TYPE);
            $rtn[$key]['amount'] = self::getPorp($val, self::SCORE);
            $rtn[$key]['remark'] = self::getPorp($val, self::REMARK);
            $rtn[$key]['c_time'] = self::getPorp($val, self::CREATE_TIME);
            $rtn[$key]['op'] = self::getPorp($val, self::OPTION, '');
            $rtn[$key]['target'] = self::getPorp($val, self::TARGET, '');
            $rtn[$key]['ao_id'] = self::getPorp($val, self::AO_ID, '');
            $rtn[$key]['aid'] = self::getPorp($val, self::ADMIN_ID, '');
        }
        
        return $rtn;
    }
    
    public function insert(array $data)
    {
        $doc[self::ID] = $data['_id'];
        $doc[self::UID] = $data['uid'];
        $doc[self::APP_NAME] = $data['appname'];
        $doc[self::TYPE] = $data['type'];
        $doc[self::AMOUNT] = floatval($data['amount']);
        $doc[self::REMARK] = $data['remark'];
        $doc[self::CREATE_TIME] = $data['c_time'];
        if (isset($data['op'])) {
            $doc[self::OPTION] = $data['op'];
        }
        if (isset($data['target']) && $data['target'] !== '') {
            $doc[self::TARGET] = $data['target'];
        }
        if (isset($data['ao_id'])) {
            $doc[self::AO_ID] = $data['ao_id'];
        }
        if (isset($data['aid'])) {
            $doc[self::ADMIN_ID] = $data['aid'];
        }
        
        $rtn = $this->conn->add($doc);
        
        return $rtn;
    }

    public function find($query = array(), $fields = array())
    {
        $cursor = $this->conn->find($query, $fields);

        return $cursor;
    }
}
