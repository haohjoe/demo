<?php

/**
 * SubmitTaskController class file
 * 
 * @author zhanglu@camera360.com
 * @date 2014/03/13
 *
 */
class SubmitTaskController extends CmController
{
    /**
     * 提交任务
     */
    public function actionIndex()
    {
        return true;

        // 用户uid
        $args['uid'] = Yii::app()->user->id;
        // 操作码
        $args['op'] = ControllerParameterValidator::validateString($_REQUEST, 'op', 1, null);
        // 操作对象值，如被评论图片的id
        $args['target'] = ControllerParameterValidator::validateString($_REQUEST, 'target', 0, null, '');
        // 执行次数
        $args['times'] = ControllerParameterValidator::validateInteger($_REQUEST, 'times', 1, null, 1); // 次数
        
        $objLogicSubmitTask = new SubmitTaskLogic($this->solution);
        $rst = $objLogicSubmitTask->execute($args);
        
        // 失败抛异常
        if (false === $rst) {
            throw new Exception('Submit task failed with uid[' . $args['uid'] . '] op[' . $args['op'] . ']' . ' msg[submit task fail]', Errno::INTERNAL_SERVER_ERROR);
        }
        LogHelper::pushLog('submitSuccess', 'true');
        
        ResponseHelper::outputJsonV2($rst);
    }
}
