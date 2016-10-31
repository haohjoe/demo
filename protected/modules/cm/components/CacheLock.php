<?php

/**
 * 缓存实现锁，支持Redis2.6.12+ 和 Memcached
 * User: xiaoshiyong@camera360.com
 * Date: 15/12/10
 */
class CacheLock
{
    private $cache = null;

    /**
     * 获取memcached/redis连接.
     * @param $key
     * @return Memcached
     */
    public function __construct($solution)
    {
        $this->cache = new PGCache('cache.cm.' . $solution . '.lock');
    }

    /**
     * 加锁
     * @param $key string 锁关键字
     * @param $expireTime int   单位（s）超时时间， 当进程在锁定后出错，这样永远不会释放锁了，只能等到缓存失效
     * @param $retry int 失败后重试获取锁次数.
     * @return boolean true 成功获取到锁 false 获取锁失败 setNx
     */
    public function addLock($key, $expireTime = 5, $retry = 3)
    {
        $n = 0;
        do {
            if ($this->add($key, 1, $expireTime)) {
                LogHelper::pushLog('AddLockSucc_Retry', $n);
                return true;
            }
            if ($retry == 0) {
                break;
            }
            $n ++;
            usleep(pow($n, 2) * 10000); // 失败后幂等休息： 10ms,40ms,90ms,160ms...
        } while ($retry --);

        LogHelper::pushLog('AddLockFailed_Retry', $n);
        return false;
    }

    /**
     * 释放锁
     * @param $key string 锁关键字
     * @param $retry int 失败后重试获取锁次数.
     * @return boolean true 释放成功 false 释放失败
     */
    public function releaseLock($key, $retry = 5)
    {
        $n = 0;
        do {
            if ($this->cache->delete($key)) {
                LogHelper::pushLog('DelLockSucc_Retry', $n);
                return true;
            }
            if ($retry == 0) {
                break;
            }
            $n ++;
            usleep($n * 10000); // 失败后阶梯休息： 10ms,20ms,30ms,40ms,50ms...
        } while ($retry --);

        LogHelper::pushLog('DelLockFailed_Retry', $n);
        return false;
    }

    /**
     * 兼容memcached、redis的add方法
     */
    private function add($key, $value, $expireTime)
    {
        if (property_exists($this->cache, 'redisCache') && $this->cache->redisCache) { // 需要Redis2.6.12+.
            return $this->cache->set($key, $value, array('nx', 'ex' => $expireTime));
        } else { // memcached版本
            return $this->cache->add($key, $value, $expireTime);
        }
    }
}
