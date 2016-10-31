<?php

/**
 * 任务控制器
 */
class TaskController extends CmInnerController
{
    /**
     * 提交任务
     */
    public function actionSubmit()
    {
        // 判断是否关闭submit
        if (isset(Yii::app()->params['closeApi']['submit']) && Yii::app()->params['closeApi']['submit'] === 1) {
            ResponseHelper::outputJsonV2(array());
        }
        
        // 用户uid
        $args['uid'] = ControllerParameterValidator::validateMongoIdAsString($_REQUEST, 'uid');
        // 操作码
        $args['op'] = ControllerParameterValidator::validateString($_REQUEST, 'op', 1, 100);
        // 操作对象值，如被评论图片的id
        $args['target'] = ControllerParameterValidator::validateString($_REQUEST, 'target', 0, 1000, '');
        // 执行次数
        $args['times'] = ControllerParameterValidator::validateInteger($_REQUEST, 'times', 1, null, 1); // 次数

        $stl = new SubmitTaskLogic($this->solution, $this->appName);
        $data = $stl->execute($args);

        // 失败抛异常
        if (false === $data) {
            throw new Exception('Submit task failed with uid[' . $args['uid'] . '] op[' . $args['op'] . ']' . ' msg[submit task fail]', Errno::INTERNAL_SERVER_ERROR);
        }
        // 成功打日志
        LogHelper::pushLog('SubmitTask', 'success');

        ResponseHelper::outputJsonV2($data);
    }
    
    /**
     * 提交任务只做简单处理，不经过各种规则，主要用于初期批量导入日志数据用.
     */
    public function actionSubmitNoRule()
    {
        return true; // 暂时关闭本功能.

        // 用户uid
        $args['uid'] = ControllerParameterValidator::validateMongoIdAsString($_REQUEST, 'uid');
        // 操作码
        $args['op'] = ControllerParameterValidator::validateString($_REQUEST, 'op', 1, 100);
        // 执行次数
        $args['times'] = ControllerParameterValidator::validateInteger($_REQUEST, 'times', 1, null, 1); // 次数
        
        $objLogicSubmitTask = new SubmitTaskLogic($this->solution);
        $rst = $objLogicSubmitTask->executeNoRule($args);
        
        // 失败抛异常
        if (false === $rst) {
            throw new Exception('SubmitNoRule task failed with uid[' . $args['uid'] . '] op[' . $args['op'] . ']' . ' msg[submitTaskNoRule fail]', Errno::INTERNAL_SERVER_ERROR);
        }
        // 成功打日志
        LogHelper::pushLog('SubmitTask', 'success');
        
        ResponseHelper::outputJsonV2($rst);
    }
}
