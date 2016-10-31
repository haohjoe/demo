<?php

/**
 * 
 * @author yangbing
 * @date 2016-02-25
 */
class DataContent
{

    public $daoContent;

    public function __construct()
    {
        $this->daoContent = new DaoContent();
    }

    public function setContent($id, $content, MongoDate $updateTime)
    {
        return $this->daoContent->setContent($id, $content, $updateTime);
    }

    public function findById($id)
    {
        $arrCriteria = array(
            '_id' => $id
        );
        return $this->daoContent->findOne($arrCriteria);
    }
}
