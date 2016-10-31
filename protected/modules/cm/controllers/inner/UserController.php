<?php

/**
 * 用户控制器
 */
class UserController extends CmInnerController
{
    /**
     * 直接添加积分,谨慎使用本接口；请仅提供给后台管理员使用
     * 这种方式添加c币后会在c币流水表中记录rec_id为“admin_add_cpoint”
     * 每次增加c币数不能超过1,000,000
     */
    public function actionAdminAddCpoint()
    {
        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // c币
        $cpoint = ControllerParameterValidator::validateFloat($_POST, 'cpoint', 0.01, 1000000.00);
        // 管理员id
        $adminId = ControllerParameterValidator::validateString($_POST, 'adminId', 1, 50);

        $logic = new UserLogic($this->solution, $this->appName);
        $rst = $logic->addCpointByUid($uid, $cpoint, $adminId);

        ResponseHelper::outputJsonV2($rst);
    }

    /**
     * 直接减少积分,谨慎使用本接口；请仅提供给后台管理员使用
     * 这种方式添加c币后会在c币流水表中记录rec_id为“admin_reduce_cpoint”
     * 每次增加c币数不能超过1,000,000
     */
    public function actionAdminReduceCpoint()
    {
        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');
        // c币
        $cpoint = ControllerParameterValidator::validateFloat($_POST, 'cpoint', 0.01, 1000000.00);
        // 管理员id
        $adminId = ControllerParameterValidator::validateString($_POST, 'adminId', 1, 50);

        $logic = new UserLogic($this->solution, $this->appName);
        $rst = $logic->reduceCpointByUid($uid, $cpoint, $adminId);

        ResponseHelper::outputJsonV2($rst);
    }

    /**
     * 获取用户c点数
     */
    public function actionGetCpoint()
    {
        // 用户uid
        $uid = ControllerParameterValidator::validateMongoIdAsString($_POST, 'uid');

        $logic = new UserLogic($this->solution, $this->appName);
        $data = $logic->getCpointByUid($uid);

        ResponseHelper::outputJsonV2($data);
    }

    /**
     * 批量获取用户c点数
     */
    public function actionMultiGetCpoint()
    {
        // 用户uids
        $uids = ControllerParameterValidator::validateArray($_POST, 'uids');
        foreach ($uids as $uid) {
            ControllerParameterValidator::validateMongoIdAsString($uid, 'uid');
        }

        $logic = new UserLogic($this->solution, $this->appName);
        $data = $logic->getCpointByUids($uids);

        ResponseHelper::outputJsonV2($data);
    }

    /**
     * 列取用户信息（积分 + 经验 + 等级）
     */
    public function actionListInfo()
    {
        $uids = ControllerParameterValidator::validateArray($_POST, 'uids');
        foreach ($uids as $uid) {
            ControllerParameterValidator::validateMongoIdAsString($uid, 'uid');
        }

        $logic = new UserLogic($this->solution, $this->appName);
        $data = $logic->listInfo($uids);

        ResponseHelper::outputJsonV2($data);
    }

    /**
     * 列取排序后的用户信息
     * 目前只建议cc应用使用这个接口。
     */
    public function actionListSortInfo()
    {
        return array(); // 因为mongodb->mysql暂不支持

        // 条件 支持：score、cpoint、grade=1格式
        $args['condition'] = ControllerParameterValidator::validateString($_REQUEST, 'condition');
        if (! in_array($args['condition'], array('score', 'cpoint')) && ! preg_match('/^grade=\d+$/', $args['condition'])) {
            throw new ParameterValidationException('condition format error');
        }
        // 排序条件 1：正序；-1：倒序
        $args['sort'] = ControllerParameterValidator::validateEnumInteger($_REQUEST, 'sort', array(- 1, 1), - 1);
        if (empty($args['sort'])) {
            $args['sort'] = - 1;
        }
        $args['page'] = ControllerParameterValidator::validateInteger($_REQUEST, 'page', 1, 500, 1);
        $args['limit'] = ControllerParameterValidator::validateInteger($_REQUEST, 'limit', 1, 100, 20);

        $logic = new UserLogic($this->solution, $this->appName);
        $data = $logic->listSortInfo($args);

        ResponseHelper::outputJsonV2($data);
    }
}
