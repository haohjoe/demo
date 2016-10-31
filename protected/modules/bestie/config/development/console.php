<?php
$config = CMap::mergeArray(
    require(__DIR__ . '/main.php'),
    array(
        'name' => 'My Console Application',
        'components' => array(
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    'file' => array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'info,warning,error',
                        'maxFileSize' => 10 * 1024,
                        'maxLogFiles' => 1024,
                    )
                )
            )
        )
    )
);
return $config;
