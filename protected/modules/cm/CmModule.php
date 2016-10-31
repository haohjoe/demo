<?php

class CmModule extends CWebModule
{

    public function init()
    {
        $configPath = dirname(__FILE__) . '/config/' . strtolower(APPLICATION_ENV) . '/main.php';
        if (is_readable($configPath)) {
            $config = require($configPath);
            Yii::app()->configure($config);
        }
        $moduleName = $this->getId();
        define('MODULE_NAME', $moduleName);
        return parent::init();
    }
}
