<?php
return CMap::mergeArray(require(__DIR__ . '/../base.php'), array(
    'components' => array(
        'cache.cm.c360' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmc360_'
            )
        ),
        'cache.cm.cc' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmcc_'
            )
        ),
        'cache.cm.c360.lock' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmc360_'
            )
        ),
        'cache.cm.cc.lock' => array(
            'class' => 'PGRedisCache',
            'keyPrefix' => '', // key自动追加前缀
            'balancePolicy' => 'hash',
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,
                Redis::OPT_PREFIX => 'mbcmcc_'
            )
        )
    )
));
