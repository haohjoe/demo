<?php
! defined('TEST') && define('TEST', 'TEST');
YiiBase::setPathOfAlias('ptest', dirname(__FILE__) . '/../../lib/ptest');

return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array(
        'import' => array(
            'ptest.base.*',
        ),
    )
);
