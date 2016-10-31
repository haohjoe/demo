<?php
$argments = array_splice($argv, 1);
$module = null;
$index  = false;
foreach ($argments as $key => $argment) {
    if (strpos($argment, '--module=') === 0) {
        $module = substr($argment, strlen('--module='));
        $index = $key + 1;
        break;
    }
}

require_once dirname(__FILE__) . '/config/mode.php';
$yiiPath = dirname(__FILE__) . '/../../lib/yii-1.1.13/yii.php';
require_once($yiiPath);

// change the following paths if necessary
$yiic = dirname(__FILE__) . '/../../lib/yii-1.1.13/yiic.php';
$globalConfig = require dirname(__FILE__) . '/config/' . constant('APPLICATION_ENV') . '/console.php';

if ($module != null) {
    $moduleConfig = require dirname(__FILE__) . "/modules/$module/config/" . constant('APPLICATION_ENV') . '/console.php';
    $config = CMap::mergeArray($globalConfig, $moduleConfig);
    $env = dirname(__FILE__) . "/modules/$module/commands/";
    unset($_SERVER['argv'][$index]);
    @putenv('YII_CONSOLE_COMMANDS=' . $env);
} else {
    $config = $globalConfig;
}

require_once($yiic);
