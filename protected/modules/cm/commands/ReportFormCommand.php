<?php
ini_set('memory_limit', '1024M');

/**
 * 统计脚本
 */
class ReportFormCommand extends CConsoleCommand
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
     * 获取某天的积分情况
     * ./yiic --module=cm reportForm day
     */
    public function actionDay()
    {
        // 循环处理每个解决方案
        $solution = 'c360';
        // 前一天时间.
        $day = date('Ymd', strtotime('-1 day'));
        $start = strtotime($day);
        $end = $start + UtilHelper::DAY;

        $localDb = new DaoUserLocale($solution);
        $statDb = new DaoStat($solution);
        $mongoDb = new DaoAccountChangeLog($solution);
        $sort = array(
            '_id' => -1
        );
        $fields = array(
            'uid' => 1,
            'type' => 1,
            'amount' => 1,
            'c_time' => 1,
        );
        $cursor = $mongoDb->find(array(), $fields)->sort($sort);

        $cnAddCpoints = 0; // 国内增加积分数
        $otherAddCpoints = 0;  // 国外增加积分数
        $cnUseCpoints = 0;  // 国内消耗积分数
        $otherUseCpoints = 0;  // 国外消耗积分数

        $n = 0;
        $flag = 0;
        $adds = array();
        $uses = array();
        foreach ($cursor as $k => $v) {
            $n ++;
            if (! empty($v['type']) || empty($v['uid']) || empty($v['c_time']) || empty($v['amount'])) { // 0: cpoint;1:score
                continue;
            }
            $time = UtilHelper::mongoDate2Float($v['c_time']);
            if ($time > $end) {
                continue;
            }
            if ($time < $start) {
                break;
            }
            $uid = $v['uid']->__toString();
            if ($v['amount'] > 0) {
                if (isset($adds[$uid])) {
                    $adds[$uid] += $v['amount'];
                } else {
                    $adds[$uid] = $v['amount'];
                }
            } else {
                if (isset($uses[$uid])) {
                    $uses[$uid] += $v['amount'];
                } else {
                    $uses[$uid] = $v['amount'];
                }
            }
            $flag ++;
            if ($flag == 10) { // 每10条执行一次
                // 拿出uids
                $uids = array_unique(array_merge(array_keys($adds), array_keys($uses)));
                // 获取所属地区
                $data = $localDb->getByIds($uids);
                foreach ($adds as $ak => $av) {
                    if (isset($data[$ak]) && $data[$ak]['locale'] == 'cn') {
                        $cnAddCpoints += $av;
                    } else {
                        $otherAddCpoints += $av;
                    }
                }
                foreach ($uses as $uk => $uv) {
                    if (isset($data[$uk]) && $data[$uk]['locale'] == 'cn') {
                        $cnUseCpoints += $uv;
                    } else {
                        $otherUseCpoints += $uv;
                    }
                }
                // 置0
                $flag = 0;
                $adds = array();
                $uses = array();
            }
        }

        // 将数据写入stat表中.
        $statDb->setDataByKey($day . '_cn_add_cpoints', $cnAddCpoints, $day);
        $statDb->setDataByKey($day . '_other_add_cpoints', $otherAddCpoints, $day);
        $statDb->setDataByKey($day . '_cn_use_cpoints', $cnUseCpoints, $day);
        $statDb->setDataByKey($day . '_other_use_cpoints', $otherUseCpoints, $day);

        echo '已扫描' . $n . '条数据' . PHP_EOL;

        Yii::app()->end();
    }

    /**
     * 获取积分情况
     * ./yiic --module=cm reportForm all
     */
    public function actionAll()
    {

    }
}
