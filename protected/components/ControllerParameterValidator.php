<?php

/**
 * 
 * @author liuhongwei
 *
 */
class ControllerParameterValidator
{

    /**
     *
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param int $default  NAN
     * @throws ParameterValidationException
     * @return int 检查是否为空，为空并且设置默认值，则返回默认值
     */
    public static function checkEmpty($mixed, $porp, $default = NAN)
    {
        if (is_array($mixed)) {
            if (isset($mixed[$porp])) {
                $value = $mixed[$porp];
            } elseif (@is_nan($default)) {
                throw new ParameterValidationException("$porp is required!");
            } else {
                return $default;
            }
        } else {
            $value = $mixed;
        }
        
        // 处理空白字符
        if (is_string($value)) {
            $value = trim($value); 
        }
        
        if (! isset($value) || @is_nan($value)) {
            if (@is_nan($default)) {
                throw new ParameterValidationException("$porp can't be empty!");
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    /**
     *
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param int $min default null
     * @param int $max default null
     * @param int $default NAN
     * @throws ParameterValidationException
     * @return int 对整形的过滤处理
     */
    public static function validateInteger($mixed, $porp, $min = null, $max = null, $default = NAN)
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        
        if (! preg_match(self::$numberPattern, "$value")) {
            if (@is_nan($default)) {
                throw new ParameterValidationException("$porp must be an integer.");
            } else {
                return $default;
            }
        }
        
        $value = intval($value);
        
        if ($min !== null && $value < $min) {
            throw new ParameterValidationException("$porp is too small (minimum is $min)");
        }
        
        if ($max !== null && $value > $max) {
            throw new ParameterValidationException("$porp is too big (maximum is $max)");
        }
        
        return $value;
    }
    // 这是对整形的正则过滤
    private static $numberPattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

    /**
     * 进行浮点处理
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param float $min            
     * @param float $max            
     * @param float $default NAN
     * @throws ParameterValidationException
     * @return number
     */
    public static function validateFloat($mixed, $porp, $min = null, $max = null, $default = NAN)
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        
        if (! preg_match(self::$numberPattern, "$value")) {
            if (@is_nan($default)) {
                throw new ParameterValidationException("$porp must be an number.");
            } else {
                return $default;
            }
        }
        
        $value = floatval($value);
        
        if ($min !== null && $value < $min) {
            throw new ParameterValidationException("$porp is too small (minimum is $min)");
        }
        
        if ($max !== null && $value > $max) {
            throw new ParameterValidationException("$porp is too big (maximum is $max)");
        }
        
        return $value;
    }

    /**
     * 注意：字符串特殊处理， 如果设置了默认值且字符串为空， 则返回默认值
     * 
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param int $min            
     * @param int $max            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return String
     */
    public static function validateString($mixed, $porp, $min = null, $max = null, $default = NAN)
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        
        // 字符串特殊处理， 如果设置了默认值且字符串为空， 则返回默认值
        if (! @is_nan($default)) {
            if (empty($value)) {
                return $default;
            }
        }
        $length = mb_strlen($value); // 这里不能用strlen，字符串长度跟编码有关
        if ($min !== null && $length < $min) {
            throw new ParameterValidationException("$porp is too short (minimum is $min characters)");
        }
        
        if ($max !== null && $length > $max) {
            throw new ParameterValidationException("$porp is too long (maximum is $max characters)");
        }
        
        return $value;
    }

    private static $mongoDatePattern = '/^1[\.\d]{12,}$/';

    /**
     * 1394087667 = 2014-03-06 14:30
     * 对时间进行过滤
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param Array $validValues            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return MongoDate
     */
    public static function validateMongoDate($mixed, $porp, $default = NAN)
    {
        $value = ControllerParameterValidator::validateFloat($mixed, $porp, 0, null, $default);
        if (! $value) {
            if (@is_nan($default)) {
                throw new ParameterValidationException("$porp is invalid");
            } elseif ($default) {
                $value = $default;
            } else {
                return $default;
            }
        }
        
        $startArr = explode('.', $value);
        $startUsec = 0;
        if (isset($startArr[1])) {
            $usec = $startArr[1];
            $usec = str_pad($usec, 6, '0');
            $startUsec = (substr($usec, 0, 3)) * 1000;
        }
        $startTime = new MongoDate(intval($startArr[0]), $startUsec);
        return $startTime;
    }

    /**
     * 对枚举型字串进行处理
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param Array $validValues            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return String
     */
    public static function validateEnumString($mixed, $porp, $validValues, $default = NAN)
    {
        $value = ControllerParameterValidator::validateString($mixed, $porp, null, null, $default);
        if (@is_nan($default) && ! in_array($value, $validValues)) {
            throw new ParameterValidationException("$porp is not valid!");
        }
        return $value;
    }

    /**
     *
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param Array $validValues            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return String
     */
    public static function validateEnumInteger($mixed, $porp, $validValues, $default = NAN)
    {
        $value = ControllerParameterValidator::validateInteger($mixed, $porp, null, null, $default);
        if (@is_nan($default) && ! in_array($value, $validValues)) {
            throw new ParameterValidationException("$porp is not valid!");
        }
        return $value;
    }

    private static $mongoIdPattern = '/^[0-9A-Fa-f]{24}$/';

    /**
     * MongId的处理
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return String
     */
    public static function validateMongoIdAsString($mixed, $porp, $default = NAN)
    {
        $value = ControllerParameterValidator::validateString($mixed, $porp, 24, 24, $default);
        if ($value instanceof MongoId) {
            return $value->__toString();
        }
        
        if (@is_nan($default) && ! preg_match(self::$mongoIdPattern, $value)) {
            throw new ParameterValidationException("$porp must be an valid mongoId.");
        }
        return $value;
    }

    private static $etagPattern = '/^[0-9a-zA-Z_\-]+$/';

    /**
     * 这里是处理唯一标识，etag
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param String $default  NAN
     * @throws ParameterValidationException
     * @return etag
     */
    public static function validateEtag($mixed, $porp, $default = NAN)
    {
        $value = ControllerParameterValidator::validateString($mixed, $porp, 5, null, $default);
        if (@is_nan($default) && ! preg_match(self::$etagPattern, $value)) {
            throw new ParameterValidationException("$porp must be an valid etag.");
        }
        return $value;
    }

    /**
     *
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return int
     */
    public static function validatePhotoId($mixed, $porp, $default = NAN)
    {
        $value = ControllerParameterValidator::validateInteger($mixed, $porp, 1, null, $default);
        return $value;
    }

    /**
     *
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return int 相册id的过滤处理
     */
    public static function validateAlbumId($mixed, $porp, $default = NAN)
    {
        $value = ControllerParameterValidator::validateInteger($mixed, $porp, 1, null, $default);
        return $value;
    }

    /**
     * 将传入的字符串按照指定的方式切割为数组
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param float $min            
     * @param float $max            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return Array
     */
    public static function validateArray($mixed, $porp, $split = ',', $min = null, $max = null, $default = NAN)
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        if (null == $value) {
            if (@is_nan($default)) {
                return array(); 
            } else {
                return $default; 
            }
        }
        if (! is_array($value)) {
            $value = explode($split, $value);
        }
        
        $length = count($value);
        
        if ($min !== null && $length < $min) {
            throw new ParameterValidationException("$porp is too short (minimum is $min elements).");
        }
        
        if ($max !== null && $length > $max) {
            throw new ParameterValidationException("$porp is too long (maximum is $max elements).");
        }
        return $value;
    }

    /**
     *
     * @param Array $mixed/mixed $data
     * @param String $porp            
     * @param String $default NAN
     * @throws ParameterValidationException
     * @return MongoId
     */
    public static function validateUserId($mixed, $porp, $default = NAN)
    {
        $ret = ControllerParameterValidator::validateMongoIdAsString($mixed, $porp, $default);
        return $ret;
    }

    /**
     * 验证公共参数是否正确
     *
     * @param array $aryData            
     * @static
     *
     * @access public
     * @return array
     */
    public static function validateCommonParamters($aryData)
    {
        $longtitude = false;
        $latitude = false;
        
        if (isset($aryData['signpass'])) {
            $signpass = $aryData['signpass'];
            $userId = isset($aryData['userId']) ? $aryData['userId'] : null;
            
            $key = 'c3601012';
            if ($userId != null && 24 == strlen($userId)) {
                $key = substr($userId, 16);
            }
            $crypt = new Crypt3Des($key, $key);
            $json = $crypt->decrypt($signpass);
            $aryInfo = @json_decode($json, true);
            if ($aryInfo == false || isset($aryInfo['coordinateLon']) == false || isset($aryInfo['coordinateLat']) == false) {
                LogHelper::warning('resolve signpass failed.');
            } else {
                $longtitude = $aryInfo['coordinateLon'];
                $latitude = $aryInfo['coordinateLat'];
                LogHelper::pushLog('GPS_Lon_Lat', $longtitude . '.' . $latitude);
            }
        }
        
        $appName = ControllerParameterValidator::validateString($aryData, 'appName', 1, 50, null);
        if (! $appName) {
            $appName = ControllerParameterValidator::validateString($aryData, 'appname');
        }
        $appVersion = ControllerParameterValidator::validateString($aryData, 'appVersion', 1, 50, null);
        if (! $appVersion) {
            $appVersion = ControllerParameterValidator::validateString($aryData, 'appversion');
        }
        
        $systemVersion = ControllerParameterValidator::validateString($aryData, 'systemVersion');
        $platform = ControllerParameterValidator::validateEnumString($aryData, 'platform', array(
            'ios',
            'android',
            'iphone',
            'wp',
            'other'
        ));
        if ($platform == 'iphone') {
            $platform = 'ios';
        }
        $device = ControllerParameterValidator::validateString($aryData, 'device');
        $deviceId = ControllerParameterValidator::validateString($aryData, 'eid', 1, 50, null);
        if (! $deviceId) {
            $deviceId = ControllerParameterValidator::validateString($aryData, 'deviceId');
        }
        $locale = ControllerParameterValidator::validateString($aryData, 'locale');
        $channel = ControllerParameterValidator::validateString($aryData, 'channel');
        $cid = ControllerParameterValidator::validateString($aryData, 'cid', null, null, '');
        $mnc = ControllerParameterValidator::validateString($aryData, 'mnc', null, null, ''); // 移动网络代码
        $mcc = ControllerParameterValidator::validateString($aryData, 'mcc', null, null, ''); // 移动国家代码
        $icc = ControllerParameterValidator::validateString($aryData, 'icc', null, null, ''); // isoCountryCode
        
        return array(
            'appName' => $appName,
            'appVersion' => $appVersion,
            'systemVersion' => $systemVersion,
            'platform' => $platform,
            'device' => $device,
            'deviceId' => $deviceId,
            'locale' => $locale,
            'channel' => $channel,
            'cid' => $cid,
            'longtitude' => $longtitude,
            'latitude' => $latitude,
            'mnc' => $mnc,
            'mcc' => $mcc,
            'icc' => $icc
        );
    }
}
