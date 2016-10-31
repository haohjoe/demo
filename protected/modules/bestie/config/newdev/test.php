<?php
return CMap::mergeArray(require(__DIR__ . '/main.php'), array(
    'import' => array(
        'application.modules.' . MODULE_NAME . '.tests.StaticClassMap'
    )
));
