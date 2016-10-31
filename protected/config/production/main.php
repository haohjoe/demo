<?php
require_once(dirname(__FILE__) . '/../../../../config/configparser.php');
//$mongoConfig = get_config('cn_bj', 'mongodb', 'member');
$mongoConfig = array(
    'host' => 'mongodb://cn-bj-cc-mongodb2:30311,cn-bj-cc-mongodb3:30312,en-ap-member-mongodb1:58111,en-ap-member-mongodb2:58112,en-ap-member-mongodb3:58113'
);
$cacheConfig = get_config('cn_bj', 'memcached', 'member');
$mysqlConfig = get_config('cn_bj', 'mysql', 'member');

// 全局配置
$GLOBALS['mongo_servers'] = $mongoConfig['host'];
$GLOBALS['cache_servers'] = $cacheConfig;
$GLOBALS['mysql_servers'] = array(
    'write' => array(
        'host' => 'mysql:host=' . $mysqlConfig['write'][0]['host'] . ';port=' . $mysqlConfig['write'][0]['port'],
        'username' => 'mallMaster',
        'password' => 'QEQ5NCSuRUPiaiG+nY'
    ),
    'read' => array(
        'host' => 'mysql:host=' . $mysqlConfig['reader'][0]['host'] . ';port=' . $mysqlConfig['reader'][0]['port'],
        'username' => 'mallMaster',
        'password' => 'QEQ5NCSuRUPiaiG+nY'
    )
);
$GLOBALS['mail_servers'] = array(
    'host' => 'cloud-stat1',
    'port' => '25'
);
$GLOBALS['mq_service'] = 'http://cn-mq-inner.camera360.com';

return CMap::mergeArray(require(dirname(__FILE__) . '/../base.php'), array(
    'components' => array(
        'log' => array(
            'routes' => array(
                'notice' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'notice', // production去除掉trace.
                    'logFile' => 'notice.log',
                    'maxFileSize' => 2 * 1024 * 1024,
                    'maxLogFiles' => 100,
                )
            )
        )
    ),
    'params' => array(
        'ssoUser' => array(
            'host' => 'http://i.camera360.com',
            'appkey' => '04b4w5r3j3ndzu99',
            'appsecret' => 'cr3IwI7ABPPYAR23hMEzWsR6fZwIJWvi'
        ),
        'innerProject' => array(
            'photoTask' => array( // 拍照挑战
                'appkey' => 'b97ef9489ae4479d',
                'appsecret' => '8cd892b7b97ef9489ae4479d3f4ef0fc',
            ),
            'mall' => array( // 电商
                'appkey' => 'e42e07f2c539b597',
                'appsecret' => '3df86fcb9e69ee6a32d4df235d5e87ab',
            ),
            'activity' => array( // 活动
                'appkey' => '6a32d4dfik5d5fui',
                'appsecret' => '5e8uyrdf863df235dfcb9e69cccc32d4',
            ),
            'CameraCircle' => array( // 照片圈
                'appkey' => 'ai6r7z9e87okpf1v',
                'appsecret' => '7b2f1bc290164a5dac0840b7ef2482b2'
            )
        ),
        'awsS3Data' => array(
            'keys' =>  array(
                'cpointDistribute' => 'member/online/cpointDistribute/#YM#/#D#/cpd.#YMD#.csv',
            )
        )
    )
));
