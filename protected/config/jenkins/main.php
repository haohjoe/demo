<?php

// 全局配置
$GLOBALS['mongo_servers'] = 'mongodb://127.0.0.1:27517';
$GLOBALS['cache_servers'] = array(
    array(
        'host' => '127.0.0.1',
        'port' => 6379
    )
);
$GLOBALS['mail_servers'] = array(
    'host' => 'cloud-stat1',
    'port' => '25'
);
$GLOBALS['mq_service'] = ''; // 没有明确测试地址

return CMap::mergeArray(require (dirname(__FILE__) . '/../base.php'), array(
    'params' => array(
        'ssoUser' => array(
            'host' => 'http://itest.camera360.com',
            'appkey' => '04b4w5r3j3ndzu99',
            'appsecret' => 'cr3IwI7ABPPYAR23hMEzWsR6fZwIJWvi'
        ),
        'innerProject' => array(
            'store' => array( // 应用商店
                'appkey' => 'b97ef9489ae4479d',
                'appsecret' => '8cd892b7b97ef9489ae4479d3f4ef0fc',
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
));
