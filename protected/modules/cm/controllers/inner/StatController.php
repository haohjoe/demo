<?php

/**
 * 统计
 */
class StatController extends CmInnerController
{
    /**
     * 根据日期获取统计数据.
     */
    public function actionGetByDay()
    {
        // 日期.
        $day = ControllerParameterValidator::validateInteger($_POST, 'day', 20160101, 20200101);
        if (strtotime($day) === false) {
            throw new ParameterValidationException('Day is invalid');
        }

        $logic = new StatLogic($this->solution, $this->appName);
        $data = $logic->getByDay($day);

        ResponseHelper::outputJsonV2($data);
    }
}
