<?php

/**
* 切分一条日志，获取到里面的字段信息
*/
function parseLog($strLine)
{
    $arrLog = array();
    if (empty($strLine)) {
        return $arrLog;
    }
    $arrLog['_origin'] = $strLine; // 原始日志
    $intMatch = preg_match('#^(\d{4}/\d{2}/\d{2} \d{2}:\d{2}:\d{2}) \[([^\]]+)\] \[([^\]]+)\]\s*(.*)#', $strLine, &$arrMatch);
    if ($intMatch == 1) {
        $arrLog['time'] = $arrMatch[1]; // 时间
        $arrLog['loglevel'] = $arrMatch[2]; // 日志级别
        $arrLog['category'] = $arrMatch[3]; // 模块
        $arrLog['logStr'] = $arrMatch[4]; // 用户打印信息
    } else {
        return $arrLog;
    }
    if ($arrLog['loglevel'] == 'notice') {
        $intMatch = preg_match('#\[logid:([^\]]+)\] \[(\d+)\(ms\)\] \[(\d+)\(MB\)\] \[([^\]]+)\] \[(.*)(?=\]\sprofile\[)\] profile\[([^\]]*)\](.*)#', $arrLog['logStr'], &$arrMatch);
        if ($intMatch == 1) {
            unset($arrLog['logStr']);
            $arrLog['logid'] = $arrMatch[1]; // logid
            $arrLog['cost'] = $arrMatch[2]; // 耗时ms
            $arrLog['mem'] = $arrMatch[3]; // 内存MB
            $arrLog['uripath'] = $arrMatch[4]; // uripath
            $arrLog['pushLogStr'] = $arrMatch[5]; // pushLog方法打印的所有日志组成的字符串
            $arrLog['profile'] = $arrMatch[6]; // profile信息
                                                   // $arrLog['logStr'] = $arrMatch[7];
        }
        // echo $arrLog['pushLogStr'] . "\n";
        if (! empty($arrLog['pushLogStr'])) {
            $intMatch = preg_match_all('#([^=]+)=(.*?)(?=\s[^=]+=|$)#', $arrLog['pushLogStr'], &$arrMatch, PREG_SET_ORDER);
            if ($intMatch > 0) {
                foreach ($arrMatch as $arrTmp) {
                    $arrLog['pushLog'][trim($arrTmp[1])] = trim($arrTmp[2]);
                }
            }
        }
    }
    return $arrLog;
}

//$strLog = '2014/06/25 15:00:01 [notice] [msg] [logid:bcae53aa737102528220] [70(ms)] [2(MB)] [/msg/invitation/list] abc';
//$strLog = '2014/06/25 15:00:34 [notice] [user] [logid:bcae53aa739202567681] [68(ms)] [2(MB)] [/user/friend/followList] [signpass={"course":"-1.000000","altitude":"492.512421","vAccuracy":"10.000000","hAccuracy":"85.693824","speed":"-1.000000","coordinateLon":"104.071046","coordinateLat":"30.542374"} params={"masterId":"032950522d2ebe8c1744a06e","sp":"","channel":"appstore","appName":"CameraCircle","locale":"zh-Hans","userId":"032950522d2ebe8c1744a06e","device":"iPhone5","timestamp":"1403679635.068546","platform":"ios","cid":"","systemVersion":"7.1.1","appVersion":"1.0","token":"***<160chars>","signpass":"***<280chars>","appkey":"21gf3dzhf7t12oes","eid":"0A488853-BEA1-491F-8813-1C76E081421D","sig":"7bb4ae83582888c0d5cbfa39f31df958"} nickname=sevenwen logStat=1 status=200] profile[itest.camera360.com/api/user/info=29.3(ms)/1,mongo.cc_user.query=1.4(ms)/2,itest.camera360.com/api/user/multi=14.0(ms)/1,127.0.0.1/inform/inner/inform=6.4(ms)/1]';
//$strLog = '2014/06/25 15:00:34 [notice] [user] [logid:bcae53aa739202567681] [68(ms)] [2(MB)] [/user/friend/followList] [signpass={"course":"-1.000000","altitude":"492.512421","vAccuracy":"10.000000","hAccuracy":"85.693824","speed":"-1.000000","coordinateLon":"104.071046","coordinateLat":"30.542374"} params={"masterId":"032950522d2ebe8c1744a06e","sp":"","channel":"appstore","appName":"CameraCircle","locale":"zh-Hans","userId":"032950522d2ebe8c1744a06e","device":"iPhone5","timestamp":"1403679635.068546","platform":"ios","cid":"","systemVersion":"7.1.1","appVersion":"1.0","token":"***<160chars>","signpass":"***<280chars>","appkey":"21gf3dzhf7t12oes","eid":"0A488853-BEA1-491F-8813-1C76E081421D","sig":"7bb4ae83582888c0d5cbfa39f31df958"} nickname=sevenwen logStat=1 status=200] profile[itest.camera360.com/api/user/info=29.3(ms)/1,mongo.cc_user.query=1.4(ms)/2,itest.camera360.com/api/user/multi=14.0(ms)/1,127.0.0.1/inform/inner/inform=6.4(ms)/1] counting[]';
//$strLog = "2014/06/25 15:44:59 [error] [cc] [logid:bcae53aa7dfb024f730e] trace[Controller.php:121,Controller->run] exception 'ParameterValidationException' with message 'appname is required!' in /home/worker/data/www/CC_Server/protected/components/ControllerParameterValidator.php:23";
//var_dump($strLog);
//$arr = parseLog($strLog);
//var_dump($arr);
