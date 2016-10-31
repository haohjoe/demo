<?php

/**
 * 
 * @author yangbing
 * @date 2016-02-25
 */
class DaoContent extends ModelDataMongoCollection
{

    public static $id;

    public static $content;

    public static $updateTime;

    public function __construct()
    {
        parent::__construct('db.bestie', 'bestie', 'content');
    }

    public function setContent($id, $content, MongoDate $updateTime)
    {
        $arrCriteria = array(
            '_id' => $id
        );
        $arrData = array(
            'content' => $content,
            'updateTime' => $updateTime
        );
        return $this->modify($arrCriteria, $arrData, false, true);
    }

    public function findById($id)
    {
        $arrCriteria = array(
            '_id' => $id
        );
        return $this->findOne($arrCriteria);
    }
}
