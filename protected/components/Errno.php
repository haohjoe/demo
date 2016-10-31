<?php

class Errno
{
    const USER_LOGIN_REQUIRED           = 420; // 需要登录
    const PARAMETER_VALIDATION_FAILED   = 401;
    const PRIVILEGE_NOT_PASS            = 403;
    const INTERNAL_SERVER_ERROR         = 500; // 内部错误
    const FATAL                         = 500;

    const NOT_CONCURRENT_EXE = 10000;   // 不可并发执行

    const CPOINT_NOT_ENOUGH  = 10001;    // c币不足
    const ORDER_NOT_FOUND    = 10003;    // 订单不存在
    const NO_PAID            = 10004;    // 订单未支付
    const DUPLICATE_CONSUME  = 10006;    // 订单重复消费
    const CANNOT_REVOKE      = 10007;    // 该订单不可被撤销

    const API_HAS_CLOSED = 10100;        // 接口已临时关闭
}
