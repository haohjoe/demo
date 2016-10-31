<?php
class SsoUserHelper
{

    /**
     *批量获取系统系统用户信息
     * @param array $userids  用户id数组
     * @param string $locale  语言 默认中文
     * @return [02700e52244725974ce8df73] => Array
    (
        [userId] => 02700e52244725974ce8df73
        [email] => claywong@qq.com
        [avatar] => https://dn-c360.qbox.me/c11e509d955e48f3ac0255b65b890407
        [lastLoginTime] => 1394529023
        [nickname] => clay57
        [gender] => 1
        [birthday] => 1987-01-01
    )

[06b38752244685426d55b5cc] => Array
    (
        [userId] => 06b38752244685426d55b5cc
        [email] => claywong@sina.cn
        [avatar] => https://dn-c360.qbox.me/1f7cc08b61263bfce16c42bb51a85b04
        [lastLoginTime] => 1394089558
        [nickname] => ClayWong
        [gender] => 
        [birthday] => 
    )
     * 
     */
    public static function multiInfo(array $userids, $locale = '')
    {
        $ssoUserConfig = Yii::app()->params['ssoUser'];
        $url = $ssoUserConfig['host'] . '/api/user/multi';
        $postArr = array(
            'appkey' => $ssoUserConfig['appkey'],
            'userIds' => join(',', $userids)
        );
        $locale && $postArr['locale'] = $locale;
        $sign = SecurityHelper::sign($postArr, $ssoUserConfig['appsecret']);
        $postArr['sig'] = $sign;
        $url .= '?' . http_build_query($postArr);
        $data = InnerHttpCallHelper::execHttpCall($url, array(), 'GET');
        $out = array();
        if ($data) {
            foreach ($data as $uid => $user) {
                if (isset($user['email'])) {
                    $user['email'] = '';
                }
                $out[$uid] = $user;
            }
            
        }
        return $out;

    }

    /**
     * 使用token交换用户信息
     * 
     * @param string $userId 
     * @param string $appkey 
     * @param string $token 
     * @access public
     * @return array/false
     */
    public static function exchangeInfo($userId, $appkey, $token, $timeout = 5) 
    {
        $ssoUserConfig = Yii::app()->params['ssoUser'];
        $url = $ssoUserConfig['host'] . '/api/user/info';
        $params = array(
            'userId'    => $userId,
            'appkey'    => Yii::app()->params['ssoUser']['appkey'],
            'token'     => $token
        );
        $sign = SecurityHelper::sign($params, $ssoUserConfig['appsecret']);
        $params['sig'] = $sign;
        $url .= '?' . http_build_query($params);
        return InnerHttpCallHelper::execHttpCall($url, array(), 'GET', $timeout);
    }
    
    /**
     * email登录
     * @param unknown $email
     * @param unknown $password
     * @param string $locale
     * @return mixed
     */
    public static function emailLogin($email, $password, $locale = '')
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)==false) {
            throw new ParameterValidationException('Invalid Email');
        }
        $ssoUserConfig = Yii::app()->params['ssoUser'];
        $url = $ssoUserConfig['host'] . '/api/user/login';
        $postArr = array(
                'appkey' => $ssoUserConfig['appkey'],
                'email' => $email,
                'password'=>$password
        );
        $locale && $postArr['locale'] = $locale;
        $sign = SecurityHelper::sign($postArr, $ssoUserConfig['appsecret']);
        $postArr['sig'] = $sign;
        return InnerHttpCallHelper::execHttpCall($url, $postArr, 'POST');
    }
    

    /**
     * email登录
     * @param unknown $email
     * @param unknown $password
     * @param string $locale
     * @return mixed
     */
    public static function emailRegister($email, $password, $locale = '')
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)==false) {
            throw new ParameterValidationException('Invalid Email');
        }
        $ssoUserConfig = Yii::app()->params['ssoUser'];
        $url = $ssoUserConfig['host'] . '/api/user/register';
        $postArr = array(
                'appkey' => $ssoUserConfig['appkey'],
                'email' => $email,
                'password'=>$password
        );
        $locale && $postArr['locale'] = $locale;
        $sign = SecurityHelper::sign($postArr, $ssoUserConfig['appsecret']);
        $postArr['sig'] = $sign;
        return InnerHttpCallHelper::execHttpCall($url, $postArr, 'POST');
    }
    
    
    /**
     * 使用token交换用户信息
     *
     * @param string $userId
     * @param string $appkey
     * @param string $token
     * @access public
     * @return array/false
     */
    public static function nickname($nickname) 
    {
        $ssoUserConfig = Yii::app()->params['ssoUser'];
        $url = $ssoUserConfig['host'] . '/api/user/nickname';
        $params = array(
                'appkey'    => Yii::app()->params['ssoUser']['appkey'],
                'nickname'   => $nickname
        );
        $sign = SecurityHelper::sign($params, $ssoUserConfig['appsecret']);
        $params['sig'] = $sign;
        $url .= '?' . http_build_query($params);
        return InnerHttpCallHelper::execHttpCall($url, array(), 'GET');
    }
}
