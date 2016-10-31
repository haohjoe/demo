<?php

// change the following paths if necessary
require dirname(__FILE__).'/../protected/config/mode.php';

$yii=dirname(__FILE__).'/../../lib/yii-1.1.13/yii.php';
$config=dirname(__FILE__).'/../protected/config/' . strtolower(APPLICATION_ENV) . '/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();
