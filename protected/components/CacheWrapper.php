<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/03/13
 */
class CacheWrapper
{

    private static $instance = false;
    private $cache = null;
    private $arrLocalCache = array();

    public static function getInstance($componentId)
    {
        if (! isset(self::$instance[$componentId])) {
            self::$instance[$componentId] = new CacheWrapper($componentId);
        }
        return self::$instance[$componentId];
    }

    public function __construct($componentId)
    {
        // $this->cache = NULL;
        $this->cache = new PGCache($componentId);
    }

    public function get($key)
    {
        if ($this->cache === null) {
            return false;
        }
        // 先从本机查
        if (isset($this->arrLocalCache[$key])) {
            return $this->arrLocalCache[$key];
        }
        
        $ret = $this->cache->get($key);
        // 设置本机缓存
        if ($ret !== false) {
            $this->arrLocalCache[$key] = $ret;
        }
        return $ret;
    }

    public function mget($keys)
    {
        if ($this->cache === null) {
            return false;
        }
        
        $ret = true;
        if (is_array($keys)) {
            if (! empty($keys)) {
                $ret = $this->cache->mget($keys);
            }
        } else {
            $ret = $this->cache->get($keys);
        }
        // 设置本机缓存
        if ($ret !== false && ! is_array($keys)) {
            $this->arrLocalCache[$keys] = $ret;
        }
        return $ret;
    }
    
    // 设置缓存
    public function set($key, $value, $expire = 0)
    {
        if ($this->cache === null) {
            return false;
        }
        $ret = $this->cache->set($key, $value, $expire);
        if (false === $ret) {
            // do wf log
            return false;
        }
        $this->arrLocalCache[$key] = $value;
        
        return $ret;
    }
    
    // 设置缓存
    public function mset($data, $expire = 0)
    {
        if ($this->cache === null) {
            return false;
        }
        if (! is_array($data)) {
            return false;
        }
        if (empty($data)) {
            return true;
        }
        $ret = $this->cache->mset($data, $expire);
        if (false === $ret) {
            // do wf log
            return false;
        }
        
        return $ret;
    }
    
    // 删除缓存
    public function delete($key)
    {
        if ($this->cache === null) {
            return false;
        }
        $ret = $this->cache->delete($key);
        unset($this->arrLocalCache[$key]);
        if (false === $ret) {
            // do wf log
            return false;
        }
        
        return $ret;
    }
    
    // 删除缓存
    public function mdelete($keys)
    {
        if ($this->cache === null) {
            return false;
        }
        $ret = true;
        if (is_array($keys)) {
            if (! empty($keys)) {
                $ret = $this->cache->mdelete($keys);
            }
        } else {
            $ret = $this->cache->delete($keys);
        }
        // 设置本机缓存
        if ($ret !== false) {
            if (is_array($keys)) {
                foreach ($keys as $k) {
                    unset($this->arrLocalCache[$k]);
                }
            } else {
                unset($this->arrLocalCache[$keys]);
            }
        }
        
        return $ret;
    }

    public function advancedMget($strPrefixKey, $arrKeys, $strKeyName, $objDao, $fn, $query = array(), $expire = 0, $unique = true)
    {
        if (empty($arrKeys) || ! is_array($arrKeys)) {
            return array();
        }
        $arrCacheKeys = array();
        // 组装批量cache key
        foreach ($arrKeys as $strKey) {
            $arrCacheKeys[] = $strPrefixKey . $strKey;
        }
        if ($this->cache === null) {
            $arrResult = array();
        } else {
            // 批量mc查询
            $arrResult = $this->mget($arrCacheKeys);
        }
        
        $arrKeysToQuery = array();
        if (empty($arrResult)) {
            $arrKeysToQuery = $arrKeys;
        } elseif (count($arrResult) < count($arrKeys)) {
            foreach ($arrKeys as $strKey) {
                // 从mc查询不到，放到数据库查询中
                if (! isset($arrResult[$strPrefixKey . $strKey]) || $arrResult[$strPrefixKey . $strKey] === false) {
                    $arrKeysToQuery[] = $strKey;
                }
            }
        }
        // 剩下的批量查询数据库
        $arrQueryResult = null;
        if (! empty($arrKeysToQuery)) {
            if (! is_array($query)) {
                $query = array();
            }
            array_unshift($query, $arrKeysToQuery);
            $arrQueryResult = call_user_func_array(array(
                $objDao,
                $fn
            ), $query);
            if ($arrQueryResult === false) {
                return false;
            }
        }
        if (is_array($arrQueryResult)) {
            $arrNewResult = array();
            foreach ($arrQueryResult as $v) {
                $strKey = $v[$strKeyName];
                if ($unique) {
                    $arrResult[$strPrefixKey . $strKey] = $v;
                    $arrNewResult[$strPrefixKey . $strKey] = $v;
                } else {
                    $arrResult[$strPrefixKey . $strKey][] = $v;
                    $arrNewResult[$strPrefixKey . $strKey][] = $v;
                }
            }
            if (! empty($arrNewResult)) {
                $this->mset($arrNewResult, $expire);
            }
        }
        
        // 转换结果的形式，以key为主键
        $arrOutResult = array();
        if (is_array($arrResult)) {
            foreach ($arrKeys as $strKey) {
                if (isset($arrResult[$strPrefixKey . $strKey])) {
                    $arrOutResult[$strKey] = $arrResult[$strPrefixKey . $strKey];
                }
            }
        }
        
        return $arrOutResult;
    }
}
