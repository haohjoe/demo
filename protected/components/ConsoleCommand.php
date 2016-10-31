<?php

abstract class ConsoleCommand extends CConsoleCommand
{

    public function init()
    {
        ini_set("display_errors", 1);
        ini_set('memory_limit', '2048M');
        LogHelper::init();
        Yii::getLogger()->autoFlush = true;
        Yii::getLogger()->autoDump = true;
    }

    protected function _setLogFile($filename)
    {
        $routes = Yii::app()->log->getRoutes();
        $routes['file']->setLogFile($filename);
        Yii::app()->log->setRoutes($routes);
    }
}
