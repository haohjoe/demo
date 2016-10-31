<?php
$params = array(
    // 解决方案 (不同解决方案的逻辑处理一致，数据存储到不同数据库)
    'solutions' => array(
        'c360',
        'cc'
    ),
    // 应用名称 => 积分、经验系统解决方案
    'appNames' => array(
        'photoTask' => 'c360',  // 拍照挑战
        'mall' => 'c360',       // 新电商
        'activity' => 'c360',   // 活动
        'CameraCircle' => 'cc'  // 照片圈
    ),
    // 解决方案的配置优先来源
    'solutionCnfSource' => array(
        'c360' => 'file', // 所有配置从文件配置数据（纯文件）
        'cc' => 'db' // 除配置文件中有的剩余数据从数据库中读取（文件 + 数据库）
    ),
    // 不同的解决方案选用不同的数据库
    'db' => array(
        'db.cm.c360' => 'mb_cm_c360', // c360解决方案使用c360库（mysql、mongodb数据库同名）
        'db.cm.cc' => 'mb_cm_cc'      // cc解决方案使用cc库（mysql、mongodb数据库同名）
    ),
    // 不同的解决方案选用不同的缓存组件
    'cache' => array(
        'c360' => array(
            'componentId' => 'cache.cm.c360',
            'key' => array()
        ),
        'cc' => array(
            'componentId' => 'cache.cm.cc',
            'key' => array()
        ),
    ),
    // 任务类型，主要用于对任务进行聚合显示给用户
    'taskTypes' => array(
        1, // 一般完成性任务
        2, // 获取奖励性任务
        3, // 进行积分抽奖
    ),
    // 每日完成任务可获得的经验、积分最大值【细粒度为appName】
    $config['dayGetCpointAndScoreLimit'] = array(
        'photoTask' => array(
            'cpoint' => 1000,    // -1：无限制
            'score' => 2000,    // -1：无限制
        ),
        'mall' => array(
            'cpoint' => 1000,    // -1：无限制
            'score' => 2000,    // -1：无限制
        ),
        'activity' => array(
            'cpoint' => 1000,    // -1：无限制
            'score' => 2000,    // -1：无限制
        ),
        'CameraCircle' => array(
            'cpoint' => 100,    // -1：无限制
            'score' => 2000,    // -1：无限制
        ),
    )
);

$solutionParmas = array();
// 加载不同应用下的不同配置文件
// 如果$solution没找到，此时不抛异常或返回错误，因为在控制器中会进行判断appName的合法性.
if (isset($_REQUEST['appName']) && in_array($_REQUEST['appName'], array_keys($params['appNames']))) {
    $solution = $params['appNames'][$_REQUEST['appName']];
    $solutionParmas = require(dirname(__FILE__) . '/solutions/' . $solution . '.php');
}
// 测试环境默认用cc解决方案
if (defined('TEST') && empty($solutionParmas)) {
    $solutionParmas = require(dirname(__FILE__) . '/solutions/cc.php');
}

return array_merge($params, $solutionParmas);
