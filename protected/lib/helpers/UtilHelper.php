<?php
class UtilHelper
{
    const KB                        = 1024;         // 1KB
    const MB                        = 1048576;  // 1MB
    const MINUTE                    = 60;
    const HOUR                      = 3600;
    const DAY                       = 86400;
    const WEEK                      = 604800;
    
    const CN_AREA                   = 1;//国内代码
    const NOCN_AREA                 = 2;//国外代码
    
    private static $now                 = null;
    
    /**
     * MongoDate 转换为 float 
     * 
     * @param MongoDate $_date 
     * @static
     * @access public
     * @return float
     */
    public static function mongoDate2Float(MongoDate $date) 
    {
        $sec   = $date->sec;
        $usec  = $date->usec;
        return $sec + ($usec / 1000000);
    }

    /**
     * 浮点型转换为MongoDate.
     * 
     * @param float $_floatNum
     * @static
     * @access public
     * @return MongoDate
     */
    public static function float2MongoDate($floatNum) 
    {
        $aryDate = explode('.', $floatNum);
        $sec  = intval($aryDate[0]);

        if (isset($aryDate[1]) == true) {
            $usec = intval(str_pad($aryDate[1], 3, '0') * 1000);
        } else {
            $usec = 0;
        }
        return new MongoDate($sec, $usec);
    }
    
    /**
     * @brief 判断地区语言参数是否为中文，包括但不仅限于大陆、台湾、香港
     * @param String $locale
     * @return boolean
     */
    public static function isChinese($locale) 
    {
        $locale = strtolower($locale);
        if (0 === strpos($locale, 'zh')) {
            return true;
        }
        return false;
    }
    
    //该函数用来将utf-8（一个中文的长度为3）的字符串当成gbk（一个中文的长度为2）来计算长度
    public static function countLengthLookSameAsGBK($string) 
    {
        $n = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
        }
        return $noc;
    }
    
    public static function time() 
    {
        return time();
    }
    
    public static function microtime() 
    {
        return microtime(true);
    }
    
    public static function now() 
    {
        if (self::$now === null) {
            self::$now = time();
        }
        return self::$now;
    }
    
    /**
     * @desc 获取每天的时间偏移量
     * @return 返回当前时间和当天0点的差值
     */
    public static function getTodayTimeOffset($intNow = null) 
    {
        if ($intNow === null) {
            $intNow = self::time();
        }
        $arrDate = getdate($intNow);
        //$intNow为每天的时间偏移量
        $intNow -= mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
        return $intNow;
    }
    
    /**
     * 
     * 获取指定时间 当天/当月/当年起始时间戳
     * @param unknown_type $intTime
     * @param unknown_type $strTimeSpace
     */
    public static function getStartTime($intTime = null, $strTimeSpace = 'DAY') 
    {
        $intRstTime = 0;
        if (empty($intTime)) {
            $intTime = self::time();
        }
        $arrDate = getdate($intTime);
        switch ($strTimeSpace) {
            case 'DAY':
                $intRstTime = mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
                break;
            case 'WEEK':
                $intDayTime = mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
                $intWeek = strftime("%u", $intTime);
                $intRstTime = $intDayTime - ($intWeek - 1) * self::DAY;
                break;
            case 'MONTH':
                $intRstTime = mktime(0, 0, 0, $arrDate['mon'], 1, $arrDate['year']);
                break;
            case 'YEAR':
                $intRstTime = mktime(0, 0, 0, 1, 1, $arrDate['year']);
                break;
            default:
                break;
        }
        
        return $intRstTime;
    }
    
    public static function mtSrand() 
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        
        return mt_srand($seed);
    }
    
    public static function mtRand($min, $max) 
    {
        return mt_rand($min, $max);
    }
    
    public static function checkStringIsPureEn($str) 
    {
        return mb_strlen($str) === strlen($str);
    }
    
/**
     * 转换为语言设置
     * 
     * @codeCoverageIgnore
     * @param string $locale 
     * @access public
     * @return string 可选值: zh_cn/zh_tw/en_us
     */
    public static function getLanguage($locale) 
    {
        $language = '';
        switch ($locale) {
            case 'zh-Hans':
                $language = 'zh_CN';
                break;
            case 'zh-Hant':
                $language = 'zh_TW';
                break;
            case 'zh_CN':
                $language = 'zh_CN';
                break;
            case 'zh_TW':
                $language = 'zh_TW';
                break;
            default:
                $language = 'en_US';
                break;
        }
        return strtolower($language);
    }
    
    /**
     * 获取区域代码，暂时支持国内1 国外2
     * Enter description here ...
     * @param string $lang 客户端传过来得locale
     * @param string $mcc  客户端传过来得mcc
     */
    public static function getAreaNum($lang, $mcc)
    {
        //454	HK	香港 (中华人民共和国)
        //455	MO	澳门 (中华人民共和国)
        //460	CN	中华人民共和国
        //461	CN	中华人民共和国
        $cnMccArr = array(454, 455, 460, 461);
        $area = self::CN_AREA;// 国内
        $lang = self::getLanguage($lang);
        if ($lang != 'zh_cn' && in_array(intval($mcc), $cnMccArr) == false) {
            $area = self::NOCN_AREA;//国外
        }
        return $area;
    }
}
