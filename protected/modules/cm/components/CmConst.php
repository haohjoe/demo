<?php

/**
 * @author zhanglu@camera360.com
 * @date 2014/11/26
 */
class CmConst
{
    // 重复周期模式
    const RULE_REPEAT_PERIOD_NONE = 0;
    const RULE_REPEAT_PERIOD_DAY = 1;
    const RULE_REPEAT_PERIOD_WEEK = 2;

    const CODE_RIGHT_RULE = 0; // 符合规则，完成任务
    const CODE_WRONG_RULE = 1; // 不符合规则，没有完成任务
    const CODE_MAP_TASK_FAIL = 2; // op map task 失败

    const CACHE_USER_CPOINT_PREFIX = 'ucp_';    // 用户积分信息缓存前缀
    const CACHE_ORDER_PREFIX = 'o_';            // 订单信息缓存前缀
}
