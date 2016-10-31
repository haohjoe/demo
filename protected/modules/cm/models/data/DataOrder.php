<?php

/**
 * DataOrder class file
 *
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/10
 *
 */
class DataOrder extends DataBase
{
    public $solution;
    public $appName;
    public $cache = null;

    public $objDaoOrder = null;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;
        $this->cache = new PGCache('cache.cm.' . $solution);
        $this->objDaoOrder = Factory::create('DaoOrder', array($this->solution));
    }

    public function getByAoId($uid, $aoId)
    {
        $arrRet = $this->cache->get(CmConst::CACHE_ORDER_PREFIX . $aoId);
        if ($arrRet !== false) { // 读取缓存成功.
            return $arrRet;
        }
        $arrRet = $this->objDaoOrder->getInfoByAoId($uid, $aoId);
        if ($arrRet !== false) {
            // 写入缓存
            $this->cache->set(CmConst::CACHE_ORDER_PREFIX . $aoId, $arrRet);
        }

        return $arrRet;
    }

    /**
     * 删除缓存
     */
    public function delCacheByAoId($aoId)
    {
        $this->cache->delete(CmConst::CACHE_ORDER_PREFIX . $aoId);
    }
}
