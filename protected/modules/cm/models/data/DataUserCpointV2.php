<?php

/**
 * DataUserCpointV2 class file
 *
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/10
 *
 */
class DataUserCpointV2 extends DataBase
{
    public $solution;
    public $objDaoUserCpointV2 = null;
    public $cache = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoUserCpointV2 = Factory::create('DaoUserCpointV2', array($this->solution));
        $this->cache = new PGCache('cache.cm.' . $solution);
    }

    /**
     * 获取单个用户的经验值
     * @param $uid string 用户uid
     * @return false | array
     */
    public function getByUid($uid)
    {
        $arrRet = $this->cache->get(CmConst::CACHE_USER_CPOINT_PREFIX . $uid);
        if ($arrRet !== false) { // 读取缓存成功.
            return $arrRet;
        }
        $arrRet = $this->objDaoUserCpointV2->getInfoByUid($uid);
        if ($arrRet !== false) { // 写入缓存
            $this->cache->set(CmConst::CACHE_USER_CPOINT_PREFIX . $uid, $arrRet);
        }

        return $arrRet;
    }

    /**
     * 获取多个用户的c币
     * @param $uids array 用户uids
     * @return array 如果该uid下无记录则会返回对应的空数组 'uid' => array()
     */
    public function getByUids(array $uids)
    {
        if (empty($uids)) {
            return array();
        }
        $cKeys = array();
        foreach ($uids as $uid) {
            $cKeys[] = CmConst::CACHE_USER_CPOINT_PREFIX . $uid;
        }
        $mRst = $this->cache->mget($cKeys); // 返回值中的key的在配置中的keyPrefix会自动去除.
        $cRst = $dUids = array();
        foreach ($uids as $uid) {
            $ck = CmConst::CACHE_USER_CPOINT_PREFIX . $uid;
            if (isset($mRst[$ck])) {
                $cRst[$uid] = $mRst[$ck]; // 重新封装缓存数组
            } else {
                $dUids[] = $uid; // 未命中
            }
        }

        // 有在缓存中未获取到的数据,从数据库中获取
        $qRst = array();
        if ($dUids) {
            $qRst = $this->objDaoUserCpointV2->getInfoByUids($dUids);
        }

        // 把未命中的缓存写入到缓存中
        foreach ($qRst as $qk => $row) {
            if ($row === false) { // 从数据库中获取该条数据失败.
                unset($qRst[$qk]);
            } else { // 异常的数据不进入缓存.
                // $row 可能为空数组.
                $this->cache->set(CmConst::CACHE_USER_CPOINT_PREFIX . $qk, $row);
            }
        }
        $tRst = array_merge($cRst, $qRst);
        foreach ($tRst as $tk => $tv) {
            if (empty($tv)) {
                unset($tRst[$tk]);
            }
        }

        return $tRst;
    }

    /**
     * 删除缓存
     */
    public function delCacheByUid($uid)
    {
        if (! $this->cache->delete(CmConst::CACHE_USER_CPOINT_PREFIX . $uid)) {
            // 重试一次.
            $this->cache->delete(CmConst::CACHE_USER_CPOINT_PREFIX . $uid);
        }
    }
}
