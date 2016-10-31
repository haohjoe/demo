<?php

class TestCommand extends ConsoleCommand
{

    /**
     * ./yiic --module=bestie test index
     */
    public function actionIndex()
    {
        $arrDefault = array(
            'deviceId' => 'ybCommandDeviceId',
            'appName' => 'system',
            'appVersion' => '1.0.0',
            'systemVersion' => '1.0.0',
            'platform' => 'ios',
            'deviceId' => 'systemtest',
            'device' => 'system',
            'locale' => 'zh_CN',
            'channel' => 'systemChannel'
        );
        $arrPost = array(
            'X-PG-Credential' => 'bestiesystemtest',
            'X-PG-Expires' => 36000,
            'X-PG-Time' => time()
        );
        $strFactor = $arrPost['X-PG-Credential'] . $arrPost['X-PG-Time'] . $arrPost['X-PG-Expires'];
        $arrParams = array();
        foreach ($arrDefault as $strKey => $value) {
            $arrParams[$strKey] = $strKey . '=' . rawurlencode($value);
        }
        ksort($arrParams);
        $strMethod = 'GET';
        $strUri = '/bestie/data/get';
        $strToSign = strtoupper($strMethod) . "\n" . $strUri . "\n" . implode('&', $arrParams);
        $strSignKey = hash_hmac("sha256", $strFactor, 'systemtest');
        $strCalcSignature = hash_hmac("sha256", $strToSign, $strSignKey);
        $arrPost['Signature'] = $strCalcSignature;
        $arr = array_merge($arrDefault, $arrPost);
        echo http_build_query($arr);
    }

    public function actionSet()
    {
        $arrDefault = array(
            'deviceId' => 'ybCommandDeviceId',
            'appName' => 'system',
            'appVersion' => '1.0.0',
            'systemVersion' => '1.0.0',
            'platform' => 'ios',
            'deviceId' => 'systemtest',
            'device' => 'system',
            'locale' => 'zh_CN',
            'channel' => 'systemChannel',
            'content' => json_encode(array(
                'ddd' => 123,
                'dfdf' => 333
            ))
        );
        $arrPost = array(
            'X-PG-Credential' => 'bestiesystemtest',
            'X-PG-Expires' => 36000,
            'X-PG-Time' => time()
        );
        $strFactor = $arrPost['X-PG-Credential'] . $arrPost['X-PG-Time'] . $arrPost['X-PG-Expires'];
        $arrParams = array();
        foreach ($arrDefault as $strKey => $value) {
            $arrParams[$strKey] = $strKey . '=' . rawurlencode($value);
        }
        ksort($arrParams);
        $strMethod = 'POST';
        $strUri = '/bestie/data/set';
        $strToSign = strtoupper($strMethod) . "\n" . $strUri . "\n" . implode('&', $arrParams);
        $strSignKey = hash_hmac("sha256", $strFactor, 'systemtest');
        $strCalcSignature = hash_hmac("sha256", $strToSign, $strSignKey);
        $arrPost['Signature'] = $strCalcSignature;
        $arr = array_merge($arrDefault, $arrPost);
        echo print_r($arr);
    }
}
