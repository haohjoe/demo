<?php

/**
 * 过滤，用于防刷，非针对单个任务，而是整体针对一个请求处理
 *
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/17
 *
 */
class RuleFilter
{
    public $solution;
    public $appName;
    public $objDataUserDayGot;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;
        $this->objDataUserDayGot = Factory::create('DataUserDayGot', array($this->solution));
    }

    /**
     * 每日做任务可再获得的c币、积分数
     */
    public function dayCanAdd($uid, $day = null)
    {
        $cs = array();

        if ($day === null) {
            $day = date('Ymd');
        }

        // 查询用户某日已获得积分、经验
        $id = DataUserDayGot::makeId($this->appName, $uid, $day);
        $got = $this->objDataUserDayGot->findById($id);
        $cs['cpoint'] = empty($got['cpoint']) ? 0 : $got['cpoint'];
        $cs['score'] = empty($got['score']) ? 0 : $got['score'];

        // 从配置中读取每日限值
        $csLimit = Yii::app()->params['dayGetCpointAndScoreLimit'][$this->appName];
        $cs['cpoint'] = $csLimit['cpoint'] == -1 ? -1 : max(0, $csLimit['cpoint'] - $cs['cpoint']);
        $cs['score'] = $csLimit['score'] == -1 ? -1 : max(0, $csLimit['score'] - $cs['score']);

        return $cs;
    }
}
