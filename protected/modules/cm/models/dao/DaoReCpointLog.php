<?php

/**
 * cpoint_log_x冗余表
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/07
 */
class DaoReCpointLog extends DaoRelationalBase
{
    public $db = null;
    public $dbName = null;

    const TABLE_NAME_PREFIX = 're_cpoint_log_';

    const ID = 'id';                    // int | autoincrement index
    const REC_ID = 'rec_id';            // string | index
    const UID = 'uid';                  // string | index
    const CPOINT = 'cpoint';            // float
    const ADD_MONTH = 'add_mon';        // Int
    const ADD_TIME = 'add_time';        // float
    const UPDATE_TIME = 'u_time';       // float
    const APPNAME = 'appname';          // string
    const BIN_LOG = 'bin_log';          // string
    const TYPE = 'type';                // int

    public function __construct($solution, $isMaster = true)
    {
        $wr = $isMaster ? 'write' : 'read';
        parent::__construct('db.relational.cm.' . $solution . '.' . $wr);
        $this->dbName = 'mb_cm_' . $solution;
    }

    /**
     * 获取re_order_log_x表
     * @param $month int eg: 201512
     * return string
     */
    public static function getTable($month)
    {
        return self::TABLE_NAME_PREFIX . $month;
    }
}
