<?php

/**
 * 统计数据
 * @author xiaoshiyong@camera360.com
 * @date 2016/6/6
 */
class DaoStat extends DbWrapper
{
    const COLLECTION_NAME = 'stat';

    const ID = '_id'; // uid string
    const KEY = 'key'; // string index unique
    const VALUE = 'val'; // string
    const DAY = 'day'; // 添加时间
    const CREATE_TIME = 'c_time';   // Int

    public function __construct($solution)
    {
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }

    /**
     * @param MongoId $id
     * @return array
     */
    public function getById(MongoId $id)
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
        $rst['key'] = self::getPorp($doc, self::KEY, '');
        $rst['val'] = self::getPorp($doc, self::VALUE, '');
        $rst['day'] = self::getPorp($doc, self::DAY, '');
        $rst['c_time'] = self::getPorp($doc, self::CREATE_TIME, 0);

        return $rst;
    }

    /**
     * @param String $key
     * @return array
     */
    public function getByKey($key)
    {
        $query[self::KEY] = $key;

        $doc = $this->conn->findOne($query);
        if (false === $doc) {
            return false;
        }
        if (empty($doc)) {
            return array();
        }
        self::transform($doc);
        $rst['id'] = self::getPorp($doc, self::ID);
        $rst['key'] = self::getPorp($doc, self::KEY, '');
        $rst['val'] = self::getPorp($doc, self::VALUE, '');
        $rst['day'] = self::getPorp($doc, self::DAY, '');
        $rst['c_time'] = self::getPorp($doc, self::CREATE_TIME, 0);

        return $rst;
    }

    /**
     * @param String $key
     * @return array
     */
    public function getByKeys(array $keys)
    {
        $query[self::KEY] = array(
            '$in' => $keys
        );

        $doc = $this->conn->query($query);
        if (false === $doc) {
            return false;
        }
        if (empty($doc)) {
            return array();
        }
        self::transform($doc);
        $rst['id'] = self::getPorp($doc, self::ID);
        $rst['key'] = self::getPorp($doc, self::KEY, '');
        $rst['val'] = self::getPorp($doc, self::VALUE, '');
        $rst['day'] = self::getPorp($doc, self::DAY, '');
        $rst['c_time'] = self::getPorp($doc, self::CREATE_TIME, 0);

        return $rst;
    }

    /**
     * 获取多个用户的locale
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
            $rst[$key]['key'] = self::getPorp($val, self::KEY, '');
            $rst[$key]['val'] = self::getPorp($val, self::VALUE, '');
            $rst[$key]['day'] = self::getPorp($val, self::DAY, '');
            $rst[$key]['c_time'] = self::getPorp($val, self::CREATE_TIME, 0);
        }

        return $rst;
    }

    public function addOne($key, $value, $day)
    {
        $doc = array(
            'key' => $key,
            'val' => $value,
            'day' => $day,
            'c_time' => UtilHelper::now()
        );

        $rst = $this->conn->add($doc);

        return $rst;
    }

    public function setDataByKey($key, $value, $day)
    {
        $query = array(
            self::KEY => $key
        );
        $doc = array(
            'key' => $key,
            'val' => $value,
            'day' => $day,
            'c_time' => UtilHelper::now()
        );

        $rst = $this->conn->modify($query, $doc, false, true);

        return $rst;
    }

    public function find()
    {
        $cursor = $this->conn->find();

        return $cursor;
    }

    public function getByDay($day)
    {
        $query[self::DAY] = strval($day);

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
            $rst[$key]['key'] = self::getPorp($val, self::KEY, '');
            $rst[$key]['val'] = self::getPorp($val, self::VALUE, '');
            $rst[$key]['day'] = self::getPorp($val, self::DAY, '');
            $rst[$key]['c_time'] = self::getPorp($val, self::CREATE_TIME, 0);
        }

        return $rst;
    }
}
