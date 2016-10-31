<?php

/**
 * 统计logic
 * @author xiaoshiyong@camera360.com
 * @date 20150611
 */
class StatLogic
{
    public $solution;
    public $appName;

    public $objDaoStat;

    public function __construct($solution, $appName)
    {
        $this->solution = $solution;
        $this->appName = $appName;

        $this->objDaoStat = Factory::create('DaoStat', array($this->solution));
    }

    /**
     * 添加积分【后台操作】
     */
    public function getByDay($day)
    {
        $data = $this->objDaoStat->getByDay($day);
        if (empty($data)) {
            return array();
        }

        return $data;
    }
}
