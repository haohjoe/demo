<?php

/**
 * mysql存储
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/07
 */
class DaoUserCpointV2 extends DaoRelationalBase
{
    public $db = null;
    public $dbName = null;

    const TABLE_NAME_PREFIX = 'user_cpoint_';
    
    const UID = 'uid';              // uid | int
    const CPOINT = 'cpoint';        // float
    const UPDATE_TIME = 'u_time';   // Int

    public function __construct($solution, $isMaster = true)
    {
        $wr = $isMaster ? 'write' : 'read';
        parent::__construct('db.relational.cm.' . $solution . '.' . $wr);
        $this->dbName = 'mb_cm_' . $solution;
    }

    /**
     * 获取user_cpoint_x表
     * @param $uid string 用户的mongoId字符串化
     * return string
     */
    public static function getTable($uid)
    {
        return self::TABLE_NAME_PREFIX . substr($uid, -2);
    }

    /**
     * 获取用户信息
     * @param $uid string
     * @return array
     */
    public function getInfoByUid($uid)
    {
        $table = self::getTable($uid);

        $sql = 'SELECT * FROM ' . self::getTable($uid) . ' WHERE `uid`=:uid LIMIT 1';
        $params[':uid'] = $uid;

        return $this->queryRow($table, $sql, $params);
    }

    /**
     * 获取多个用户信息
     * @param $uid string
     * @return array
     */
    public function getInfoByUids(array $uids)
    {
        if (empty($uids)) {
            return array();
        }

        $rows = array();
        $sqls = array();
        foreach ($uids as $uid) {
            $table = self::getTable($uid);
            $sqls[] = 'SELECT * FROM ' . $table . ' WHERE `uid`="' . $uid . '"';
            $rows[$uid] = array();
        }
        $sql = implode(' union ', $sqls);
        $res = $this->queryAll('user_cpoint_*', $sql);
        if (empty($res)) {
            return $rows;
        }
        foreach ($res as $val) {
            if (empty($val)) {
                continue;
            }
            $rows[$val['uid']] = $val;
        }

        return $rows;
    }

    /**
     * 插入一条信息
     */
    public function add($uid, $cpoint)
    {
        $iArr = array(
            self::UID => $uid,
            self::CPOINT => $cpoint
        );

        return $this->insert(self::getTable($uid), $iArr);
    }

    /**
     * 更新一条信息
     */
    public function modify($uid, $cpoint)
    {
        $fileds = array(
            self::CPOINT => $cpoint,
            self::UPDATE_TIME => UtilHelper::now()
        );
        $condition = '`uid`=:uid';
        $params = array(
            ':uid' => $uid
        );

        return $this->update(self::getTable($uid), $fileds, $condition, $params);
    }
}
