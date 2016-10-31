<?php
ini_set('memory_limit', '1024M');

/**
 * 用户c币mongodb迁移到mysql
 */
class InitDbCommand extends CConsoleCommand
{
    private $dbs = array();
    private $solutions = array();

    public function __construct()
    {
        // 'Create Database If Not Exists `mb_cm_c360` Character Set UTF8 collate utf8_general_ci';
        // 'Create Database If Not Exists `mb_cm_cc` Character Set UTF8 collate utf8_general_ci';

        $this->solutions = Yii::app()->params['solutions'];
        foreach ($this->solutions as $solution) {
            $this->dbs[$solution] = Yii::app()->getComponent('db.relational.cm.' . $solution . '.write');
        }
    }

    /**
     * ./yiic --module=cm InitDb order
     */
    public function actionOrder()
    {
        for ($i = 0; $i <= 255; $i ++) {
            $idx = strval(dechex($i)); // 转成16进制
            $idx = str_pad($idx, 2, '0', STR_PAD_LEFT);
            $table = 'order_' . $idx;
            $sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
                `ao_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'appName_orderId ',
                `uid` char(24) NOT NULL DEFAULT '' COMMENT '用户uid',
                `cpoint` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'c币',
                `add_mon` int(10) NOT NULL COMMENT '创建月份',
                `add_time` int(10) NOT NULL COMMENT '创建时间',
                `u_time` int(10)  NOT NULL COMMENT '更新时间',
                `appname` varchar(30)  NOT NULL COMMENT 'appname',
                `order_id` varchar(50)  NOT NULL COMMENT '订单id',
                `remark` varchar(255)  NOT NULL COMMENT '备注',
                `status` tinyint(1) unsigned NOT NULL COMMENT '订单状态(1:已撤销；2：已支付；3：已冻结)',
                PRIMARY KEY (`id`),
                KEY `in_uid` (`uid`),
                UNIQUE `in_ao_id` (`ao_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单表';";
            // 执行sql语句.
            foreach ($this->dbs as $solution => $db) {
                $rst = $db->createCommand($sql)->execute();
                echo ('在数据库mb_cm_' . $solution . '中创建表' . $table . PHP_EOL);
            }
        }
        Yii::app()->end();
    }

    /**
     * ./yiic --module=cm InitDb userCpoint
     */
    public function actionUserCpoint()
    {
        for ($i = 0; $i <= 255; $i ++) {
            $idx = strval(dechex($i)); // 转成16进制
            $idx = str_pad($idx, 2, '0', STR_PAD_LEFT);
            $table = 'user_cpoint_' . $idx;
            $sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
                `uid` char(24) NOT NULL DEFAULT '' COMMENT '用户uid',
                `cpoint` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'c币',
                `u_time` int(10)  NOT NULL COMMENT '更新时间',
                UNIQUE `in_uid` (`uid`)
            ) ENGINE=InnoDB CHARSET=utf8 COMMENT='用户积分表';";
            // 执行sql语句.
            foreach ($this->dbs as $solution => $db) {
                $rst = $db->createCommand($sql)->execute();
                echo ('在数据库mb_cm_' . $solution . '中创建表' . $table . PHP_EOL);
            }
        }
        Yii::app()->end();
    }

    /**
     * ./yiic --module=cm InitDb cpointLog
     */
    public function actionCpointLog()
    {
        for ($i = 0; $i <= 255; $i ++) {
            $idx = strval(dechex($i)); // 转成16进制
            $idx = str_pad($idx, 2, '0', STR_PAD_LEFT);
            $table = 'cpoint_log_' . $idx;
            $sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
                `rec_id` varchar(255) NOT NULL DEFAULT ''  COMMENT '关联id ',
                `uid` char(24) NOT NULL DEFAULT '' COMMENT '用户uid',
                `cpoint` DECIMAL(10,2) NOT NULL DEFAULT 0  COMMENT 'c币',
                `add_mon` int(10) NOT NULL COMMENT '创建月份',
                `add_time` int(10) NOT NULL COMMENT '创建时间',
                `appname` varchar(30)  NOT NULL COMMENT 'appname',
                `remark` varchar(255)  NOT NULL COMMENT '备注',
                `type` tinyint(1) unsigned NOT NULL COMMENT '类型(1:完成任务获取积分；2：消费扣除积分；3：撤销订单获得积分)',
                PRIMARY KEY (`id`),
                KEY `in_rec_id` (`rec_id`),
                KEY `in_uid` (`uid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='积分流水日志表';";
            // 执行sql语句.
            foreach ($this->dbs as $solution => $db) {
                $rst = $db->createCommand($sql)->execute();
                echo ('在数据库mb_cm_' . $solution . '中创建表' . $table . PHP_EOL);
            }
        }
        Yii::app()->end();
    }

    /**
     * ./yiic --module=cm InitDb reCpointLog
     */
    public function actionReCpointLog()
    {
        $months = array();
        $min = 201506;
        for ($y = 2015; $y <= 2017; $y ++) {
            for ($m = 1; $m <= 12; $m ++) {
                $ym = $y . str_pad($m, 2, '0', STR_PAD_LEFT);
                if ($ym < $min) {
                    continue;
                }
                $months[] = $ym;
            }
        }
        foreach ($months as $month) {
            $idx = $month;
            $table = 're_cpoint_log_' . $idx;
            $sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
                `rec_id` varchar(255) NOT NULL DEFAULT ''  COMMENT '关联id ',
                `uid` char(24) NOT NULL DEFAULT '' COMMENT '用户uid',
                `cpoint` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'c币',
                `add_mon` int(10) NOT NULL COMMENT '创建月份',
                `add_time` int(10) NOT NULL COMMENT '创建时间',
                `appname` varchar(30)  NOT NULL COMMENT 'appname',
                `remark` varchar(255)  NOT NULL COMMENT '备注',
                `bin_log` varchar(10000)  NOT NULL COMMENT 'bin log',
                `type` tinyint(1) unsigned NOT NULL COMMENT '类型(1:完成任务获取积分；2：消费扣除积分；3：撤销订单获得积分)',
                PRIMARY KEY (`id`),
                KEY `in_rec_id` (`rec_id`),
                KEY `in_uid` (`uid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='积分流水日志表';";
            // 执行sql语句.
            foreach ($this->dbs as $solution => $db) {
                $rst = $db->createCommand($sql)->execute();
                echo ('在数据库mb_cm_' . $solution . '中创建表' . $table . PHP_EOL);
            }
        }
        Yii::app()->end();
    }

    /**
     * alter table
     * ./yiic --module=cm InitDb alterTableV1
     */
    public function actionAlterTableV1()
    {
        for ($i = 0; $i <= 255; $i ++) {
            $idx = strval(dechex($i)); // 转成16进制
            $idx = str_pad($idx, 2, '0', STR_PAD_LEFT);
            $table = 'order_' . $idx;
            $sql = "ALTER TABLE " . $table . " MODIFY `order_id` varchar(50)  NOT NULL COMMENT '订单id'";
            // 执行sql语句.
            foreach ($this->dbs as $solution => $db) {
                $rst = $db->createCommand($sql)->execute();
                echo ('在数据库mb_cm_' . $solution . '中修改表' . $table . PHP_EOL);
            }
        }
        Yii::app()->end();
    }
}
