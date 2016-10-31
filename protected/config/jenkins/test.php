<?php

return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array(
        'import' => array(
            'yii-ext.tests.PTest',
        ),
    )
);