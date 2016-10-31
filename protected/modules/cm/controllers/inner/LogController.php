<?php

/**
 * 流水日志控制器
 */
class LogController extends CmInnerController
{
    /**
     * 获取用户c币流水
     */
    public function actionGetCpointLog()
    {

    }

    /**
     * 获取用户(积分、经验)流水
     */
    public function actionGetAccountLog()
    {
        // 指定用户
        $args['uid'] = ControllerParameterValidator::validateUserId($_REQUEST, 'uid', '');
        // 开始时间
        $args['st'] = ControllerParameterValidator::validateFloat($_REQUEST, 'st', 0, null, null);
        // 结束时间
        $args['et'] = ControllerParameterValidator::validateFloat($_REQUEST, 'et', 0, null, null);
        $args['limit'] = ControllerParameterValidator::validateInteger($_REQUEST, 'limit', 1, 100, 20);
        // 积分、c点类型
        $types = array(
            DaoAccountChangeLog::TYPE_CPOINT,
            DaoAccountChangeLog::TYPE_SCORE
        );
        $args['type'] = ControllerParameterValidator::validateEnumString($_REQUEST, 'type', $types, null);
        $args['op'] = ControllerParameterValidator::validateString($_REQUEST, 'op', 1, 100, '');
        $args['target'] = ControllerParameterValidator::validateString($_REQUEST, 'target', 0, 100, '');
        // 入账、出账
        $args['ioType'] = ControllerParameterValidator::validateEnumString($_REQUEST, 'ioType', array('in', 'out'), '');

        $arl = new AccountRecordLogic();
        $data = $arl->getList($args);

        ResponseHelper::outputJsonV2($data);
    }
}
