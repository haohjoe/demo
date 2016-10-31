<?php

/**
 * @author xiaoshiyong@camera360.com
 * @date 2016/6/6
 */
class DaoUserLocale extends DbWrapper
{

    const COLLECTION_NAME = 'userLocale';
    
    const ID = '_id'; // uid string
    const LOCALE = 'locale'; // string cn,other

    public function __construct($solution)
    {
        parent::__construct('db.cm.' . $solution, self::COLLECTION_NAME);
    }
    
    /**
     * 获取某个用户的locale
     * @param string $id
     * @return array
     */
    public function getById($id)
    {
        $query[self::ID] = $id;
        
        $doc = $this->conn->findOne($query);
        if (false === $doc) {
            return false;
        }
        if (empty($doc)) {
            return array();
        }
        self::transform($doc);
        $rst['id'] = self::getPorp($doc, self::ID);
        $rst['locale'] = self::getPorp($doc, self::LOCALE, '');

        return $rst;
    }
    
    /**
     * 获取多个用户的locale
     * @param Array $ids
     * @return array
     */
    public function getByIds(Array $ids)
    {
        $query[self::ID] = array(
            '$in' => $ids 
        );
    
        $docs = $this->conn->query($query);
        if (false === $docs) {
            return false;
        }
        if (empty($docs)) {
            return array();
        }
        self::transform($docs);
        foreach ($docs as $key => $val) {
            $rst[$key]['id'] = self::getPorp($val, self::ID);
            $rst[$key]['locale'] = self::getPorp($val, self::LOCALE, '');
        }
        
        return $rst;
    }
    
    public function setLocale($strUid, $locale)
    {
        $query[self::ID] = $strUid;
        
        $doc = array(
            self::LOCALE => $locale
        );
        
        $rst = $this->conn->modify($query, $doc, false, true);
        
        return $rst;
    }

    public function batchSetLocale(array $ids, $locale)
    {
        $query[self::ID] = array(
            '$in' => $ids
        );
        $doc = array(
            self::LOCALE => $locale
        );

        $rst = $this->conn->modify($query, $doc, true, false);

        return $rst;
    }

    public function batchAddLocale(array $ids, $locale)
    {
        $docs = array();
        foreach ($ids as $id) {
            $docs[] = array(
                self::ID => $id,
                self::LOCALE => $locale,
            );
        }
        $rst = $this->conn->batchAdd($docs);

        return $rst;
    }
    
    public function find()
    {
        $cursor = $this->conn->find();

        return $cursor;
    }
}
