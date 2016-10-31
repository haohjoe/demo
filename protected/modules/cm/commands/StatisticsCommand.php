<?php
ini_set('memory_limit', '2048M');

/**
 * 统计脚本
 */
class StatisticsCommand extends CConsoleCommand
{
    private $mongoDbs = array();
    private $solutions = array();

    public function __construct()
    {
        $this->solutions = Yii::app()->params['solutions'];
        foreach ($this->solutions as $solution) {
            $this->mongoDbs[$solution] = Yii::app()->getComponent('db.cm.' . $solution);
        }
    }

    /**
     * 获取所有用户积分
     * ./yiic --module=cm Statistics AllUserCpoint
     */
    public function actionAllUserCpoint()
    {
        // 循环处理每个解决方案
        $solution = 'c360';
        $file = RUNTIME_DIR . '/user_cpoint.csv';

        $mongoDb = new DaoUserCpoint($solution);
        $cursor = $mongoDb->find();
        $n = 0;
        foreach ($cursor as $k => $v) {
            $n ++;
            $cpoint = round(floatval($v['cpoint']), 2);
            file_put_contents($file, $k . ";" . $cpoint . PHP_EOL, FILE_APPEND);
        }

        echo '已完成' . $n . '条数据' . PHP_EOL;

        Yii::app()->end();
    }

    /**
     * 国内C币数量用户占比（eg：1000-2000积分有多少；500-1000多少用户）
     * ./yiic --module=cm Statistics ChinaUserCpoint
     */
    public function actionChinaUserCpoint()
    {
        // 循环处理每个解决方案
        $solution = 'c360';
        $file = RUNTIME_DIR . '';
        $mongoDb = new DaoUserCpoint($solution);
        $cursor = $mongoDb->find();
        $n = 0;

        foreach ($cursor as $k => $v) {
            $n ++;
            $cpoint = floatval($v['cpoint']);
        }

        echo '已完成' . $solution . '的迁移，共迁移' . $n . '条数据' . PHP_EOL;

        Yii::app()->end();
    }

    /**
     * 每天生成统计报表，存储在s3上.
     * ./yiic --module=cm Statistics ReportForms
     */
    public function actionReportForms()
    {
        $now = UtilHelper::time();
        // 循环处理每个解决方案
        $solution = 'c360';

        $mongoDb = new DaoUserCpoint($solution);
        $sort = array(
            'cpoint' => - 1
        );
        $cursor = $mongoDb->find()->sort($sort);
        $n = 0;

        $awsS3Config = Yii::app()->params['awsS3Data'];
        $objAwsS3 = new AwsS3Helper($awsS3Config);
        $key = $awsS3Config['keys']['cpointDistribute'];
        $key = str_replace('#YM#', date('Ym', $now), $key);
        $key = str_replace('#D#', date('d', $now), $key);
        $key = str_replace('#YMD#', date('Ymd', $now), $key);
        // 注意内存，如果太大可以考虑使用s3的文件上传方式来处理.
        $lines = '';
        foreach ($cursor as $k => $v) {
            $n ++;
            $cpoint = round(floatval($v['cpoint']), 2);
            $lines .= $k . ";" . $cpoint . PHP_EOL;
        }
        $params = array(
            'Body' => $lines
        );
        $rst = $objAwsS3->putObject($key, $params);
        if (empty($rst)) {
            $arrLog = array(
                'msg' => 'objAwsS3->putObject failed'
            );
            LogWrapper::warning($arrLog);
            return false;
        }

        echo '已完成' . $n . '条数据' . PHP_EOL;

        Yii::app()->end();
    }
}
