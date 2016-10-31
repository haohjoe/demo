<?php
$config = CMap::mergeArray(
    require(__DIR__ . '/main.php'),
    array()
);
return $config;
