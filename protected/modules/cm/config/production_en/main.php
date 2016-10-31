<?php
return CMap::mergeArray(require(__DIR__ . '/../base.php'), array(
    'components' => array(
        'cache.cm.c360' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'db' => 2,
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmc360_'
            )
        ),
        'cache.cm.cc' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'db' => 2,
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmcc_'
            )
        ),
        'cache.cm.c360.lock' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'db' => 2,
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmc360_'
            )
        ),
        'cache.cm.cc.lock' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'db' => 2,
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmcc_'
            )
        ),
        'db.cm.c360' => array( // mongodb
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'readPreference' => MongoClient::RP_SECONDARY_PREFERRED,//,RP_NEAREST,MongoClient::RP_PRIMARY
                'connectTimeoutMS' => 50,
                'replicaSet' => 'member_rs1'
            )
        ),
        'db.cm.cc' => array(
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'readPreference' => MongoClient::RP_SECONDARY_PREFERRED,//,RP_NEAREST,MongoClient::RP_PRIMARY
                'connectTimeoutMS' => 50,
                'replicaSet' => 'member_rs1'
            )
        ),
    ),
    'params' => array(
        // 功能关闭配置 （0：开启；1：关闭）
        'closeApi' => array(
            'submit' => 0,     // 提交任务
            'consume' => 0,     // 消费积分
            'revoke' => 0,     // 撤回积分
            'freeze' => 0,     // 冻结积分
            'notifyPaid' => 0,     // 积分回调支付
        )
    )
));
