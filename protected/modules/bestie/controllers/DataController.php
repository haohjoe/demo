<?php
/**
 * 最美自拍存储数据
 * @author yangbing
 * @date 2016-02-25
 */
class DataController extends BestieController
{

    public function actionSet()
    {
        $strContent = ControllerParameterValidator::validateString($_REQUEST, 'content', 1, 5120);
        $dataContent = new DataContent();
        $blRet = $dataContent->setContent($this->deviceId, $strContent, new MongoDate());
        if ($blRet) {
            ResponseHelper::outputJsonV2(array());
        }
        ResponseHelper::outputJsonV2(array(), 'fail', Errno::FATAL);
    }

    public function actionGet()
    {
        $dataContent = new DataContent();
        $arrData = $dataContent->findById($this->deviceId);
        $strContent = isset($arrData['content']) ? $arrData['content'] : '';
        ResponseHelper::outputJsonV2(array(
            'content' => $strContent
        ));
    }
}
