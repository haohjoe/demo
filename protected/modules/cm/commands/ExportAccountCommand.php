<?php
ini_set('memory_limit', '1024M');

/**
 * 导出用户账户信息
 */
class ExportAccountCommand extends CConsoleCommand
{

    public function __construct()
    {
    }

    /**
     * ./yiic --module=cm ExportAccount exe0
     */
    public function actionExe0()
    {
        $fileName = '/home/worker/data/www/runtime/member/user_account.csv';
        file_put_contents($fileName, "uid;cpoint;score", FILE_APPEND);

        $o1 = new PGMongoCollection('db.cm.c360', 'mb_cm_c360', 'userCpoint');
        $o2 = new PGMongoCollection('db.cm.c360', 'mb_cm_c360', 'userScore');

        $cpoints = array();
        $c1 = $o1->find();
        foreach ($c1 as $k => $v) {
            if (! isset($v['cpoint'])) {
                continue;
            }
            $cpoints[$k] = $v['cpoint'];
        }

        $scores = array();
        $c2 = $o2->find();
        foreach ($c2 as $k => $v) {
            if (! isset($v['score'])) {
                continue;
            }
            $scores[$k] = $v['score'];
        }

        $uids = array_unique(array_merge(array_keys($cpoints), array_keys($scores)));
        foreach ($uids as $uid) {
            $c = empty($cpoints[$uid]) ? 0 : $cpoints[$uid];
            $s = empty($scores[$uid]) ? 0 : $scores[$uid];
            $line = $uid . ";" . $c . ";" . $s . PHP_EOL;
            file_put_contents($fileName, $line, FILE_APPEND);
        }

        Yii::app()->end();
    }

    /**
     * ./yiic --module=cm ExportAccount exe1 --num=10000
     */
    public function actionExe1($num)
    {
        $fileName = '/home/worker/data/www/runtime/member/user_sort_cpoint.csv';
        file_put_contents($fileName, "uid;cpoint" . PHP_EOL);

        $o = new PGMongoCollection('db.cm.c360', 'mb_cm_c360', 'userCpoint');
        $sort = array(
            'cpoint' => -1
        );
        $c = $o->find()->sort($sort)->limit($num);
        foreach ($c as $k => $v) {
            if (! isset($v['cpoint'])) {
                continue;
            }
            $line = $k . ';' . $v['cpoint'] . PHP_EOL;
            file_put_contents($fileName, $line, FILE_APPEND);
        }
    }
}
