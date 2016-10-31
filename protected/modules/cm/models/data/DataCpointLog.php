<?php

/**
 * DataCpointLog class file
 *
 * @author xiaoshiyong@camera360.com
 * @date 2015/12/10
 *
 */
class DataCpointLog extends DataBase
{
    public $solution;
    public $objDaoCpointLog = null;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDaoCpointLog = Factory::create('DaoCpointLog', array($this->solution));
    }
}
