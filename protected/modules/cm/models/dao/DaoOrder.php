<?php

/**
 * mysql存储 订单表
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/07
 */
class DaoOrder extends DaoRelationalBase
{
    public $db = null;
    public $dbName = null;

    const TABLE_NAME_PREFIX = 'order_';

    const ID = 'id';                    // int | autoincrement index
    const AO_ID = 'ao_id';              // string | unique
    const UID = 'uid';                  // string | index
    const CPOINT = 'cpoint';            // float
    const ADD_MONTH = 'add_mon';        // Int
    const ADD_TIME = 'add_time';        // float
    const UPDATE_TIME = 'u_time';       // float
    const APPNAME = 'appname';          // string
    const ORDER_ID = 'order_id';        // string
    const STATUS = 'status';            // int

    const STATUS_REVOKE = 1;            // 已撤销 (状态可用作退货、删除订单场景；可撤销消费、冻结行为)
    const STATUS_PAID = 2;              // 已支付
    const STATUS_FROZEN = 3;            // 已冻结 （已冻结状态在通知已支付后，更改为已支付状态）

    public function __construct($solution, $isMaster = true)
    {
        $wr = $isMaster ? 'write' : 'read';
        parent::__construct('db.relational.cm.' . $solution . '.' . $wr);
        $this->dbName = 'mb_cm_' . $solution;
    }

    /**
     * 获取order_x表
     * @param $uid string 用户的mongoId字符串化
     * return string
     */
    public static function getTable($uid)
    {
        return self::TABLE_NAME_PREFIX . substr($uid, - 2);
    }

    /**
     * 根据aoid获取信息
     * @param $uid string
     * @param $aoId string
     * @return array
     */
    public function getInfoByAoId($uid, $aoId)
    {
        $table = self::getTable($uid);
        $sql = 'SELECT * FROM ' . $table . ' WHERE `ao_id`=:ao_id LIMIT 1';
        $params[':ao_id'] = $aoId;
        return $this->queryRow($table, $sql, $params);
    }

    /**
     * 获取用户订单列表
     */
    public function getListByUid($uid, $status = null, $start = 0, $limit = 20)
    {
        $table = self::getTable($uid);
        $sea = '';
        $params[':uid'] = $uid;
        if ($status !== null) {
            $sea .= ' AND `status`=:status';
            $params[':status'] = $status;
        }

        $sql = 'SELECT * FROM ' . $table . ' WHERE `uid`=:uid ' . $sea . ' ORDER BY `id` DESC LIMIT ' . $start . ',' . $limit;

        return $this->queryRow($table, $sql, $params);
    }
}
