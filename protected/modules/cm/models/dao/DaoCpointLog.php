<?php

/**
 * mysql存储 c币日志（流水）表
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/07
 */
class DaoCpointLog extends DaoRelationalBase
{
    public $db = null;
    public $dbName = null;

    const TABLE_NAME_PREFIX = 'cpoint_log_';

    const ID = 'id';                    // int | autoincrement index
    const REC_ID = 'rec_id';            // string | index | 关联id 如：ao_id/task_id
    const UID = 'uid';                  // string | index
    const CPOINT = 'cpoint';            // float
    const ADD_MONTH = 'add_mon';        // Int
    const ADD_TIME = 'add_time';        // float
    const APPNAME = 'appname';          // string
    const TYPE = 'type';                // int

    const TYPE_DONE_TASK = 1;           // 完成任务获取积分
    const TYPE_CONSUME = 2;             // 消费扣除积分
    const TYPE_REVOKE = 3;              // 撤销订单获得积分
    const TYPE_ADMIN_ADD_CPOINT = 4;    // 后台管理员添加积分
    const TYPE_ADMIN_REDUCE_CPOINT = 5; // 后台管理员减少积分

    public function __construct($solution, $isMaster = true)
    {
        $wr = $isMaster ? 'write' : 'read';
        parent::__construct('db.relational.cm.' . $solution . '.' . $wr);
        $this->dbName = 'mb_cm_' . $solution;
    }

    /**
     * 获取cpoint_log_x表
     * @param $uid string 用户的mongoId字符串化
     * return string
     */
    public static function getTable($uid)
    {
        return self::TABLE_NAME_PREFIX . substr($uid, -2);
    }

    /**
     * 获取用户c币流水列表
     */
    public function getListByUid($uid, $type = null, $start = 0, $limit = 20)
    {
        $table = self::getTable($uid);
        $sea = '';
        $params[':uid'] = $uid;
        if ($type !== null) {
            $sea .= ' AND `type`=:type';
            $params[':type'] = $type;
        }

        $sql = 'SELECT * FROM ' . $table . ' WHERE `uid`=:uid ' . $sea . ' ORDER BY `id` DESC LIMIT ' . $start . ',' . $limit;

        return $this->queryRow($table, $sql, $params);
    }
}
