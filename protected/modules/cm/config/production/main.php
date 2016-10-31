<?php
return CMap::mergeArray(require(__DIR__ . '/../base.php'), array(
    'components' => array(
        'db.cm.c360' => array( // mongodb
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'readPreference' => MongoClient::RP_SECONDARY_PREFERRED,//,RP_NEAREST,MongoClient::RP_PRIMARY
                'connectTimeoutMS' => 1000,
                'replicaSet' => 'member_rs1'
            )
        ),
        'db.cm.cc' => array(
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'readPreference' => MongoClient::RP_SECONDARY_PREFERRED,//,RP_NEAREST,MongoClient::RP_PRIMARY
                'connectTimeoutMS' => 1000,
                'replicaSet' => 'member_rs1'
            )
        ),
        'cache.cm.c360' => array(
            'class' => 'PGMemCache',
            'keyPrefix' => 'mbcmc360_', // key自动追加前缀
            'useMemcached' => true, // memcache特有参数
            'mgetReturnUnhit' => false, // 批量查询时是否返回未命中key
            'serializer' => array( // value内容的序列号、反序列化函数, serializer为false则不序列化
                'igbinary_serialize',
                'igbinary_unserialize'
            ),
            'options' => array( // in use when useMemcached is true
                'OPT_CONNECT_TIMEOUT' => 10, // milliseconds
                'OPT_POLL_TIMEOUT' => 50 // milliseconds
            )
        ),
        'cache.cm.cc' => array(
            'class' => 'PGMemCache',
            'keyPrefix' => 'mbcmcc_', // key自动追加前缀
            'useMemcached' => true, // memcache特有参数
            'mgetReturnUnhit' => false, // 批量查询时是否返回未命中key
            'serializer' => array( // value内容的序列号、反序列化函数, serializer为false则不序列化
                'igbinary_serialize',
                'igbinary_unserialize'
            ),
            'options' => array( // in use when useMemcached is true
                'OPT_CONNECT_TIMEOUT' => 10, // milliseconds
                'OPT_POLL_TIMEOUT' => 50 // milliseconds
            )
        ),
        'cache.cm.c360.lock' => array(
            'class' => 'PGMemCache',
            'keyPrefix' => 'mbcmc360_', // key自动追加前缀
            'useMemcached' => true, // memcache特有参数
            'mgetReturnUnhit' => false, // 批量查询时是否返回未命中key
            'serializer' => array( // value内容的序列号、反序列化函数, serializer为false则不序列化
                'igbinary_serialize',
                'igbinary_unserialize'
            ),
            'options' => array( // in use when useMemcached is true
                'OPT_CONNECT_TIMEOUT' => 50, // milliseconds
                'OPT_POLL_TIMEOUT' => 300 // milliseconds
            )
        ),
        'cache.cm.cc.lock' => array(
            'class' => 'PGMemCache',
            'keyPrefix' => 'mbcmcc_', // key自动追加前缀
            'useMemcached' => true, // memcache特有参数
            'mgetReturnUnhit' => false, // 批量查询时是否返回未命中key
            'serializer' => array( // value内容的序列号、反序列化函数, serializer为false则不序列化
                'igbinary_serialize',
                'igbinary_unserialize'
            ),
            'options' => array( // in use when useMemcached is true
                'OPT_CONNECT_TIMEOUT' => 50, // milliseconds
                'OPT_POLL_TIMEOUT' => 300 // milliseconds
            )
        )
    ),
    'params' => array(
        // 功能关闭配置 （0：开启；1：关闭）
        'closeApi' => array(
            'submit'        => 0,     // 提交任务
            'consume'       => 0,     // 消费积分
            'revoke'        => 0,     // 撤回积分
            'freeze'        => 0,     // 冻结积分
            'notifyPaid'    => 0,     // 积分回调支付
        )
    )
));
