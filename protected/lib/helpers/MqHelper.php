<?php
class MqHelper
{
    /**
     * 各子系统广播消息.
     * 
     * @param string $opuid 
     * @param string $opcode 
     * @param array  $opinfo 
     *
     * @static
     * @access public
     * @return bool  消息是否成功发送
     */
    public static function broadcast($opuid, $opcode, $opinfo = array()) 
    {
        // 透传__isAdmin
        $opinfo['__isAdmin'] = isset($GLOBALS['__isAdmin']) ? $GLOBALS['__isAdmin'] : 0;
        $aryTestUsers = array(
            '53d9a7bd8852d6c82f4c347f',
            '53eb4f2d8852d637560c4d3d',
            '04091c528486e4f631ffae17',
            '539037ee8852d6ce73d0ebeb',
        );
        LogHelper::trace('使用新的MQ发布接口. opuid:' . $opuid . ' opcode:' . $opcode);
        return Yii::app()->getComponent('mq')
            ->publish($opuid, $opcode, $opinfo);
    }

    /**
     * 解析参数
     * 
     * @static
     * @access public
     * @return void
     */
    public static function parse() 
    {
        if (isset($_REQUEST['info']) == true) {
            $mqInfo = $_REQUEST['info'];
            $_REQUEST['info'] = unserialize($mqInfo);
            $_POST['info'] = unserialize($mqInfo);
        }
        if (isset($_REQUEST['time']) == true) {
            $_REQUEST['time'] = floatval($_REQUEST['time']);
            $_POST['time'] = floatval($_REQUEST['time']);
        }
    }
}
