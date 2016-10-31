<?php


class SiteController extends Controller
{
    
    public function filters() 
    {
        return array(
//             'accessControl'
        );
    }
    
    public function actionIndex()
    {
        Yii::app()->end(0);
    }
}
