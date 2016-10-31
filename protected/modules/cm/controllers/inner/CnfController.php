<?php

/**
 * 配置控制器
 */
class CnfController extends CmInnerController
{
    /**
     * 更新任务配置
     */
    public function actionTaskCnfUpdate()
    {
        // 任务id
        $args['id'] = ControllerParameterValidator::validateInteger($_REQUEST, 'id', 1, null);
        // 经验值
        $args['score'] = ControllerParameterValidator::validateFloat($_REQUEST, 'score', 0, null);
        // c点
        $args['cpoint'] = ControllerParameterValidator::validateFloat($_REQUEST, 'cpoint', 0, null);
        // 阶梯经验值
        $args['stepScore'] = ControllerParameterValidator::validateString($_REQUEST, 'step_score', 0, null, '');
        // 阶梯c点
        $args['stepCpoint'] = ControllerParameterValidator::validateString($_REQUEST, 'step_cpoint', 0, null, '');
        // 任务状态
        $args['status'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'status', array(0, 1), 0);
        // 开始时间
        $args['sTime'] = ControllerParameterValidator::validateInteger($_REQUEST, 's_time', 1, null, '');
        // 结束时间
        $args['eTime'] = ControllerParameterValidator::validateInteger($_REQUEST, 'e_time', 1, null, '');
        // 开始等级
        $args['sGrade'] = ControllerParameterValidator::validateInteger($_REQUEST, 's_grade', - 1, null, - 1);
        // 结束等级
        $args['eGrade'] = ControllerParameterValidator::validateInteger($_REQUEST, 'e_grade', - 1, null, - 1);
        // 是否支持批量操作
        $args['batch'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'batch', array(0, 1), 0);
        // 是否允许重复执行
        $args['repeat'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'repeat', array(0, 1), 0);
        // 周期模式
        $args['rFlag'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'r_flag', array(0, 1, 2), 0);
        // 周期系数
        $args['rRatio'] = ControllerParameterValidator::validateInteger($_REQUEST, 'r_ratio', 0, null, 0);
        // 周期内可执行次数
        $args['rCount'] = ControllerParameterValidator::validateInteger($_REQUEST, 'r_count', - 1, null, - 1);
        // 周期内可获得最大c点数
        $args['rCpoint'] = ControllerParameterValidator::validateFloat($_REQUEST, 'r_cpoint', - 1, null, - 1);
        // 周期内最大可获得经验值
        $args['rScore'] = ControllerParameterValidator::validateFloat($_REQUEST, 'r_score', - 1, null, - 1);
        // 是否对操作对象过滤
        $args['rFilter'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'r_filter', array(0, 1), 0);
        // 阶梯经验、积分是否要考虑是否为周期模式. 
        $args['rStep'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'r_step', array(0, 1), 0);

        $logic = new CnfLogic($this->solution);
        $rst = $logic->taskCnfUpdate($args);
        if (! $rst) {
            throw new Exception('Task config update failed', Errno::INTERNAL_SERVER_ERROR);
        }

        ResponseHelper::outputJsonV2($rst);
    }

    /**
     * 列取任务配置列表
     */
    public function actionTaskCnfList()
    {
        $logic = new CnfLogic($this->solution);
        $data = $logic->taskCnfList();

        ResponseHelper::outputJsonV2($data);
    }

    /**
     * 获取等级配置列表
     */
    public function actionGradeCnfList()
    {
        $logic = new CnfLogic($this->solution);
        $data = $logic->gradeCnfList();

        ResponseHelper::outputJsonV2($data);
    }
}
