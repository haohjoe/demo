<?php

define('SYSTEM_NAME', 'demo');

! defined('WWW_DIR') && define('WWW_DIR', realpath(dirname(__FILE__) . '/../../..'));
! defined('LIB_DIR') && define('LIB_DIR', WWW_DIR . '/lib');
! defined('RUNTIME_DIR') && define('RUNTIME_DIR', WWW_DIR . '/runtime/' . SYSTEM_NAME);
set_include_path(get_include_path() . ':' . LIB_DIR);
YiiBase::setPathOfAlias('yii-ext', LIB_DIR . '/yii-ext');

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'demo',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.components.*',
        'application.components.filters.*',
        'application.lib.*',
        'application.lib.helpers.*',
        'yii-ext.vendors.*',
        'yii-ext.components.*',
        'yii-ext.helpers.*',
        'yii-ext.models.*',
        'yii-ext.models.data.*',
        'yii-ext.models.logic.*',
    ),
    'runtimePath' => constant('RUNTIME_DIR'),

    'modules' => array(
        'cm' => array(), // module setting
        'bestie' => array() // module setting
    ),

    // application components
    'components' => array(
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                '/inner/<module:\w+>/<action:.+>' => '/<module>/inner/<action>',
            ),
        ),
        'cache.demo' => array(
            'class' => 'PGMemCache',
            'servers' => $GLOBALS['cache_servers'], // 具体配置在不同环境中设置
            'cacheInUse' => true,
            'keyPrefix' => 'mb_login_',     // key自动追加前缀
            'hashKey' => false,             // 对key做hash转换之后再缓存
            'expireTime' => 86400,          // seconds
            'useMemcached' => true,         // memcache特有参数
            'mgetReturnUnhit' => false,     // 批量查询时是否返回未命中key
            'serializer' => array(          // value内容的序列号、反序列化函数, serializer为false则不序列化
                'igbinary_serialize',
                'igbinary_unserialize'
            ),
            'options' => array(              // in use when useMemcached is true
                'OPT_CONNECT_TIMEOUT' => 10, // milliseconds
                'OPT_POLL_TIMEOUT' => 50,    // milliseconds
            ),
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                'file' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'maxFileSize' => 2 * 1024 * 1024,
                    'maxLogFiles' => 100,
                ),
                'notice' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'notice,trace',
                    'logFile' => 'notice.log',
                    'maxFileSize' => 2 * 1024 * 1024,
                    'maxLogFiles' => 100,
                ),
                'profile' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'profile',
                    'maxFileSize' => 100 * 1024,
                    'maxLogFiles' => 100,
                    'logFile' => 'profile.log',
                ),
            ),
        ),
    ),

    'params' => array(
        'adminEmail' => 'webmaster@example.com',
        'awsS3Data' => array(
            'bucket' => 'pg-app-data',
            'region' => 'cn-north-1',
            'accessKeyId' => 'AKIAOZZBKNACO2UBQBDQ',
            'secretAccessKey' => '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'
        )
    )
);
