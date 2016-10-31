<?php
ini_set('memory_limit', '1024M');

/**
 * 同步用户所在地区脚本.
 */
class ReadEachLocaleDataCommand extends CConsoleCommand
{
    private $localeDb = null;
    private $cpointDb = null;

    private $day = null;
    private $cpointDistribution = array();

    public function __construct()
    {
        $solution = 'c360';

        $this->localeDb = new DaoUserLocale($solution);
        $this->cpointDb = new DaoUserCpoint($solution);

        $this->cpointDistribution = array(
            'cn' => array(
                '0-600' => 0,
                '600-1000' => 0,
                '1000-2000' => 0,
                '2000~' => 0,
            ),
            'other' => array(
                '0-600' => 0,
                '600-1000' => 0,
                '1000-2000' => 0,
                '2000~' => 0,
            ),
        );
        $this->day = date('Ymd', strtotime('-1 day'));
        // $this->day = '20160606';
    }

    /**
     * ./yiic --module=cm readEachLocaleData execute
     */
    public function actionExecute()
    {
        // $now = UtilHelper::now();
        $now = strtotime($this->day);
        // 循环处理每个解决方案

        foreach (array('china', 'other') as $lv) {
            if ($lv == 'china') {
                $locale = 'cn';
            } else {
                $locale = 'other';
            }
            $awsS3Config = Yii::app()->params['awsS3Data'];
            $objAwsS3 = new AwsS3Helper($awsS3Config);
            $key = $awsS3Config['keys']['cpointDistribute'];
            $key = str_replace('#YM#', date('Ym', $now), $key);
            $key = str_replace('#D#', date('d', $now), $key);
            $key = str_replace('#YMD#', date('Ymd', $now), $key);
            $key = str_replace('.csv', '_' . $lv . '.csv', $key);
            if (! $objAwsS3->doesObjectExist($key)) {
                echo 'aws s3 object ' . $key . ' not exists!';
                continue;
            }
            $body = $objAwsS3->getObject($key);
            $body->rewind();
            $n = 0;
            $data = array();
            $totalUsers = 0;
            $totalCpoints = 0;
            while (true) {
                $n ++;
                // 读取一条数据.
                $line = $body->readLine();
                echo $line . PHP_EOL;
                // 判断行是否合法.
                if (empty($line)) {
                    break;
                }
                $line = explode(';', $line);
                if (count($line) != 2) {
                    continue;
                }

                $totalUsers ++;
                $totalCpoints += $line[1];

                $data[$line[0]] = $line[1];
                if ($n == 200) {
                    $this->setUserLocale($data, $locale);
                    $this->cpointDistribution($data, $locale);
                    $data = array();
                    $n = 0;
                }
            }

            // 记录统计数据
            $this->setStatData($totalUsers, $totalCpoints, $locale);
        }

        Yii::app()->end();
    }

    /**
     * 更新地区数据
     */
    private function setUserLocale(array $data, $locale)
    {
        $uids = array_keys($data);
        // 查找已存在记录.
        $records = $this->localeDb->getByIds($uids);
        if (empty($records)) { // 没有记录、查找失败.
            try {
                $this->localeDb->batchAddLocale($uids, 'cn');
            } catch (Exception $e) {
            }
            return;
        }
        $uUids = array_keys($records); // 需要更新的.
        if ($uUids) {
            $this->localeDb->batchSetLocale($uUids, 'cn');
        }
        $aUids = array_diff($uids, $uUids); // 需要插入的.
        if ($aUids) {
            $this->localeDb->batchAddLocale($aUids, 'cn');
        }
    }

    /**
     * 统计数据
     */
    private function cpointDistribution(array $data, $locale)
    {
        foreach ($data as $uid => $cpoint) {
            if ($cpoint == 0) {
                continue;
            }
            if ($cpoint > 0 && $cpoint <= 600) {
                $this->cpointDistribution[$locale]['0-600'] ++;
            } elseif ($cpoint > 600 && $cpoint <= 1000) {
                $this->cpointDistribution[$locale]['600-1000'] ++;
            } elseif ($cpoint > 1000 && $cpoint <= 2000) {
                $this->cpointDistribution[$locale]['1000-2000'] ++;
            } elseif ($cpoint > 2000) {
                $this->cpointDistribution[$locale]['2000~'] ++;
            }
        }
    }

    /**
     * 记录统计数据
     */
    private function setStatData($totalUsers, $totalCpoints, $locale)
    {
        $statDb = new DaoStat('c360');

        foreach ($this->cpointDistribution as $key => $val) {
            foreach ($val as $k => $v) {
                $statDb->setDataByKey($this->day . '_' . $key . '_cpoint_distribution_' . $k, $v, $this->day);
            }
        }
        // 设置总量数据
        $statDb->setDataByKey($this->day . '_' . $locale . '_total_user_nums', $totalUsers, $this->day);
        $statDb->setDataByKey($this->day . '_' . $locale . '_total_cpoints', $totalCpoints, $this->day);
        $avg = $totalUsers == 0 ? 0 : $totalCpoints / $totalUsers;
        $statDb->setDataByKey($this->day . '_' . $locale . '_user_avg_cpoints', $avg, $this->day);
    }

    /**
     * ./yiic --module=cm readEachLocaleData t1
     */
    public function actionT1()
    {
        $uids = array(
            '57577c82862d5a70a7f3b3c4' => 100,
            '57577c82862d5a70a7f3b4c6' => 200,
            '57577c82862d5a70a7f3b4c5' => 300,
        );
        $uids = array_keys($uids);
        // 查找
        $records = $this->localeDb->getByIds($uids);
        if (empty($records)) {
            try {
                $this->localeDb->batchAddLocale($uids, 'cn');
            } catch (Exception $e) {
            }
            return;
        }
        $uUids = array_keys($records); // 需要更新的.
        if ($uUids) {
            $this->localeDb->batchSetLocale($uUids, 'cn');
        }
        $aUids = array_diff($uids, $uUids); // 需要插入的.
        if ($aUids) {
            $this->localeDb->batchAddLocale($aUids, 'cn');
        }
    }

    /**
     * ./yiic --module=cm readEachLocaleData t2
     */
    public function actionT2()
    {
        $statDb = new DaoStat('c360');

        var_dump($statDb->setDataByKey('xxx', 333, '20160608'));
    }

    /**
     * ./yiic --module=cm readEachLocaleData cn
     */
    public function actionCn()
    {
        // $now = UtilHelper::now();
        $this->day = '20160628';
        $now = strtotime($this->day);
        $locale = 'china';
        $awsS3Config = Yii::app()->params['awsS3Data'];
        $objAwsS3 = new AwsS3Helper($awsS3Config);
        $key = $awsS3Config['keys']['cpointDistribute'];
        $key = str_replace('#YM#', date('Ym', $now), $key);
        $key = str_replace('#D#', date('d', $now), $key);
        $key = str_replace('#YMD#', date('Ymd', $now), $key);
        $key = str_replace('.csv', '_' . $locale . '.csv', $key);
        if (! $objAwsS3->doesObjectExist($key)) {
            echo 'aws s3 object ' . $key . ' not exists!';
            return;
        }
        $body = $objAwsS3->getObject($key);
        $body->rewind();
        $n = 0;
        $file = '/home/worker/data/www/runtime/member/cn_gt_250_uids.csv';
        while (true) {
            $n ++;
            // 读取一条数据.
            $line = $body->readLine();
            echo $line . PHP_EOL;
            // 判断行是否合法.
            if (empty($line)) {
                break;
            }
            $line = explode(';', $line);
            if (count($line) != 2 || $line[1] < 250) {
                continue;
            }
            file_put_contents($file, $line[0] . PHP_EOL, FILE_APPEND);
        }

        Yii::app()->end();
    }
}
