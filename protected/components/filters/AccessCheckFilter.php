<?php

class AccessCheckFilter extends CAccessControlFilter
{

    protected $_innerApiCall = false;

    /**
     * 设置标识，表明是内部接口调用
     *
     * @access public
     * @return void
     */
    public function setIsInner()
    {
        $this->_innerApiCall = true;
    }

    /**
     * hook preFilter
     *
     * @param mixed $filterChain            
     * @access protected
     * @return bool
     */
    protected function preFilter($filterChain)
    {
        if ($this->_innerApiCall == false) {
            $aryInfo = $this->publicLogin();
        } else {
            $aryInfo = $this->innerLogin();
        }
        
        if (is_array($aryInfo) == true) {
            $aryUser = array(
                'id' => $aryInfo['userId'],
                'name' => $aryInfo['nickname']
            );
            foreach ($aryUser as $key => $value) {
                Yii::app()->user->$key = $value;
            }
        }
        return parent::preFilter($filterChain);
    }

    /**
     * 外部接口登录
     *
     * @access public
     * @return array
     */
    public function publicLogin()
    {
        $userId = Yii::app()->request->getParam('userId');
        $appkey = Yii::app()->request->getParam('appkey');
        $token = Yii::app()->request->getParam('token');
        
        if ($userId == null || $appkey == null || $token == null) {
            return false;
        }
        $cacheModel = new PGCache('cache.member');
        $loginCacheKey = md5("user_token:${userId}_${token}");
        
        $aryCacheUserInfo = $cacheModel->get($loginCacheKey);
        if ($aryCacheUserInfo != false) {
            LogHelper::pushLog('loginCheck', 'cache');
            return $aryCacheUserInfo;
        }
        
        try {
            $aryUserInfo = SsoUserHelper::exchangeInfo($userId, $appkey, $token, 1);
            $cacheModel->set($loginCacheKey, $aryUserInfo, 300);
            LogHelper::pushLog('loginCheck', 'sso');
        } catch (Exception $e) {
            LogHelper::warning('获取用户信息失败. status:' . $e->getCode() . ' message:' . $e->getMessage());
            return false;
        }
        
        return $aryUserInfo;
    }

    /**
     * 内部登录.
     *
     * @access public
     * @return array
     */
    public function innerLogin()
    {
        $userId = Yii::app()->request->getParam('userId');
        if ($userId == null) {
            return false;
        }
        $aryUsers = SsoUserHelper::multiInfo(array(
            $userId
        ));
        if ($aryUsers == false || count($aryUsers) == 0) {
            return false;
        }
        return current($aryUsers);
    }

    /**
     * 拒绝用户访问.
     *
     * @param IWebUser $user            
     * @param mixed $message            
     * @access public
     * @return void
     */
    public function accessDenied(IWebUser $user, $message)
    {
        if ($user->isGuest == true) {
            ResponseHelper::outputJsonV2(array(), '', Errno::USER_LOGIN_REQUIRED);
        } else {
            // @todo: 还未想清楚, 由于不用于更高的权限检查,暂时用不到
            ResponseHelper::outputJsonV2(array(), $message, Errno::USER_LOGIN_REQUIRED);
        }
    }
}
