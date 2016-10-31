<?php

class FirstController extends CController
{
    public function __contruct()
    {
        // 生产环境屏蔽.
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'production') {
            Yii::app()->end();
        }
    }

    public function actionHelloWord()
    {
        var_dump($_GET['test']);
        echo "test for git push!";
    }
}
