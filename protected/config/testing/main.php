<?php

// 全局配置
$GLOBALS['mongo_servers'] = 'mongodb://127.0.0.1:27017';
$GLOBALS['mysql_servers'] = array(
    'write' => array(
        'host' => 'mysql:host=127.0.0.1;port=3306',
        'username' => 'memberMaster',
        'password' => 'D#CB$C0D$F@!2'
    ),
    'read' => array(
        'host' => 'mysql:host=127.0.0.1;port=3306',
        'username' => 'memberMaster',
        'password' => 'D#CB$C0D$F@!2'
    ),
);
$GLOBALS['cache_servers'] = array(
    array(
        'host' => '127.0.0.1',
        'port' => 6380,
    )
);
$GLOBALS['mail_servers'] = array(
    'host' => 'cloud-stat1',
    'port' => '25',
);
$GLOBALS['mq_service'] = ''; // 测试环境未定义

return CMap::mergeArray(
    require(dirname(__FILE__) . '/../base.php'),
    array(
        'params' => array(
            'ssoUser' => array(
                'host' => 'http://itest.camera360.com',
                'appkey' => '04b4w5r3j3ndzu99',
                'appsecret' => 'cr3IwI7ABPPYAR23hMEzWsR6fZwIJWvi',
            ),
            'innerProject' => array(
                'photoTask' => array( // 拍照挑战
                    'appkey' => 'b97ef9489ae4479d',
                    'appsecret' => '8cd892b7b97ef9489ae4479d3f4ef0fc',
                ),
                'mall' => array( // 电商
                    'appkey' => 'e42e07f2c539b597',
                    'appsecret' => '2ee385d4e42e07f2c539b597559e70ee',
                ),
                'activity' => array( // 活动
                    'appkey' => 'e42e07f2c539b590',
                    'appsecret' => '2ee385d4e42e07f2c539b597559e70e3',
                ),
                'CameraCircle' => array( // 照片圈
                    'appkey' => 'd9vcipp52eb4a4k2',
                    'appsecret' => 'x147nwlahtnxrrxz2pco59pyigdkpa',
                )
            ),
            'awsS3Data' => array(
                'keys' =>  array(
                    'cpointDistribute' => 'member/testingdev/cpointDistribute/#YM#/#D#/cpd.#YMD#.csv',
                )
            )
        )
    )
);
