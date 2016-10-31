<?php

/**
 * DataReCpointLog class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class DataReCpointLog extends DataBase
{
    public $solution;
    public $objDaoReCpointLog = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoReCpointLog = Factory::create('DaoReCpointLog', array($this->solution));
    }
}
