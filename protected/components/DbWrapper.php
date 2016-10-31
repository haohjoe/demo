<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/03/13
 */
class DbWrapper
{

    protected $conn = null;

    public function __construct($componentId, $strCollName)
    {
        $arrDbConfig = Yii::app()->params['db'];
        if (! isset($arrDbConfig[$componentId])) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[db config error] componentId[' . $componentId . ']', Errno::INTERNAL_SERVER_ERROR);
        }
        $this->conn = new ModelDataMongoCollection($componentId, $arrDbConfig[$componentId], $strCollName);
    }

    public static function transform(&$data)
    {
        if (empty($data)) {
            return $data;
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($value instanceof MongoId) {
                    $data[$key] = strval($value);
                } elseif ($value instanceof MongoDate) {
                    list ($usec, $sec) = explode(" ", strval($value));
                    $data[$key] = (float) $usec + (float) $sec;
                } elseif (is_array($value)) {
                    $data[$key] = self::transform($value);
                }
            }
        } else {
            if ($data instanceof MongoId) {
                $data = strval($data);
            } elseif ($data instanceof MongoDate) {
                list ($usec, $sec) = explode(" ", strval($data));
                $data = (float) $usec + (float) $sec;
            }
        }
        
        return $data;
    }

    public static function getPorp($value, $porp, $default = null)
    {
        return is_array($value) && array_key_exists($porp, $value) ? $value[$porp] : $default;
    }
}
