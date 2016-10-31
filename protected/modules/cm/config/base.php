<?php
return array(
    'import' => array(
        'application.modules.cm.components.*',
        'application.modules.cm.models.dao.*',
        'application.modules.cm.models.data.*',
        'application.modules.cm.models.logic.*',
        'application.modules.cm.models.rule.*'
    ),

    'components' => array(
        'db.cm.c360' => array( // mongodb
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'connectTimeoutMS' => 50,
                // 'replicaSet' => 'member_rs1'
            )
        ),
        'db.relational.cm.c360.write' => array( // mysql 写
            'class' => 'CDbConnection',
            'connectionString' => $GLOBALS['mysql_servers']['write']['host'] . ';dbname=mb_cm_c360',
            'emulatePrepare' => true,
            'username' => $GLOBALS['mysql_servers']['write']['username'],
            'password' => $GLOBALS['mysql_servers']['write']['password'],
            'charset' => 'utf8',
            'autoConnect' => false,
        ),
        'db.relational.cm.c360.read' => array( // mysql读
            'class' => 'CDbConnection',
            'connectionString' => $GLOBALS['mysql_servers']['read']['host'] . ';dbname=mb_cm_c360',
            'emulatePrepare' => true,
            'username' => $GLOBALS['mysql_servers']['read']['username'],
            'password' => $GLOBALS['mysql_servers']['read']['password'],
            'charset' => 'utf8',
            'autoConnect' => false,
        ),
        'db.cm.cc' => array(
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'connectTimeoutMS' => 50,
                // 'replicaSet' => 'member_rs1'
            )
        ),
        'db.relational.cm.cc.write' => array( // mysql 写
            'class' => 'CDbConnection',
            'connectionString' => $GLOBALS['mysql_servers']['write']['host'] . ';dbname=mb_cm_cc',
            'emulatePrepare' => true,
            'username' => $GLOBALS['mysql_servers']['write']['username'],
            'password' => $GLOBALS['mysql_servers']['write']['password'],
            'charset' => 'utf8',
            'autoConnect' => false,
        ),
        'db.relational.cm.cc.read' => array( // mysql读
            'class' => 'CDbConnection',
            'connectionString' => $GLOBALS['mysql_servers']['read']['host'] . ';dbname=mb_cm_cc',
            'emulatePrepare' => true,
            'username' => $GLOBALS['mysql_servers']['read']['username'],
            'password' => $GLOBALS['mysql_servers']['read']['password'],
            'charset' => 'utf8',
            'autoConnect' => false,
        ),
        'cache.cm.c360' => array(
            'servers' => $GLOBALS['cache_servers'],
            'cacheInUse' => true,
            'hashKey' => false, // 对key做hash转换之后再缓存
        ),
        'cache.cm.cc' => array(
            'servers' => $GLOBALS['cache_servers'],
            'cacheInUse' => true,
            'hashKey' => false, // 对key做hash转换之后再缓存
        ),
        'cache.cm.c360.lock' => array(
            'servers' => $GLOBALS['cache_servers'],
            'cacheInUse' => true,
            'hashKey' => false, // 对key做hash转换之后再缓存
        ),
        'cache.cm.cc.lock' => array(
            'servers' => $GLOBALS['cache_servers'],
            'cacheInUse' => true,
            'hashKey' => false, // 对key做hash转换之后再缓存
        )
    ),

    'params' => require_once(dirname(__FILE__) . '/params.php'),    //引入参数文件
);
