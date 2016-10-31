<?php
ini_set('memory_limit', '1024M');

/**
 * 导出用户账户信息
 */
class TestCommand extends CConsoleCommand
{

    public function __construct()
    {
    }

    /**
     * ./yiic --module=cm test exe0
     */
    public function actionExe0()
    {
        $dao = new DaoUserTask('c360');
        $doc = array(
            'id' => '0f064d52469523c46ab99124_4',
            'score' => 1.1,
            'cpoint' => 1.1,
            'c_time' => 1418379383,
            'u_time' => 1418699713,
            'incCounter' => 1,
            'uid' => '0f064d52469523c46ab99124',
            'actions' => array(
                10 => array(
                    't_id' => 4,
                    'target' => '',
                    'c_time' => 1418699713,
                    'cpoint' => 0.1,
                    'score' => 0.1
                )
            )
        );
        $rst = $dao->updateData($doc);
        var_dump($rst);
    }

    /**
     * ./yiic --module=cm test exe1
     */
    public function actionExe1()
    {
        for ($i = 0; $i < 1000; $i ++) {
            $id = new MongoId();
            var_dump(strval($id));
        }
    }

    /**
     * ./yiic --module=cm test exe2
     */
    public function actionExe2()
    {
        $uids = array(
            '0f55f8565adbf8eb7a358dff',
            '0f0ba356586b0b2365358dff',
            '0153b85657347fcc2d358dfe',
        );

        $duc = new DaoUserCpointV2('c360', true);
        $res = $duc->getInfoByUids($uids);
        var_dump($res);
    }
}
