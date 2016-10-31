<?php

/**
 * RuleBase class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class RuleBase
{
    const MAX_ACTION_NUMS = 5000;

    public function __construct()
    {
    }

    /**
     * 拿到已操作的的action记录进行和现有要执行的动作进行“规则校验”.
     * @param string $uid 用户uid
     * @param array $task 任务信息
     * @param string $target
     * @param array $actions 周期内执行过的动作数组
     * @param int $counter 执行该任务总次数
     * @return array | boolean
     */
    public function handle($uid, $task, $target, &$actions, &$counter)
    {
        $rule = $task['rule'];
        $cActions = count($actions); // actions 数量

        // 无过滤情况不考虑target.
        if ($rule['r_filter'] == 0) {
            $target = '';
        }

        // 需要根据参数'target'过滤重复动作 【无周期模式target无效】
        if ($target !== '') {
            foreach ($actions as $action) {
                if ($action['target'] == $target) {
                    // target相同，表示是重复动作，例如任务"下载camera360的应用"，需要过滤掉重复下载相同应用的情况
                    LogHelper::pushLog('ErrTask_' . $task['id'], 'target');
                    return false;
                }
            }
        }

        // 构建新动作结构
        $newAction = array(
            't_id' => $task['id'],
            'target' => $target,
            'c_time' => UtilHelper::now(),
            'cpoint' => 0,
            'score' => 0
        );

        // 获取一个完成该任务可以获取到的经验数 & 积分数
        $cs = $this->getCpointAndScore($task, $cActions, $counter);

        // 不能重复执行的情况，一定是第一次执行任务
        if ($rule['repeat'] == 0) {
            $newAction['cpoint'] = $cs['cpoint'];
            $newAction['score'] = $cs['score'];
            $actions[] = $newAction; // 将新的action同时写入已执行action数组中，此时肯定actions中只有newAction
            return $newAction;
        }

        // 对重复模式进行规则处理...
        $rFlag = $rule['r_flag'];
        // 获取一个周期内的开始时间[r_flag = 0 时开始时间为0].
        $repeatStart = $this->getRepeatStart($rFlag, $rule['r_ratio'], $rule['s_time']);

        $totalCpoint = 0;
        $totalScore = 0;
        // 对所有actions循环获取各个action记录值.
        foreach ($actions as $index => $action) {
            if ($action['c_time'] < $repeatStart) {
                // 只保留当前周期的历史动作, 不在本周期内的历史action数据会被清空.
                unset($actions[$index]); // 注意： r_flag 为 0 模式下会永不清空action记录.
                // bugfix：修复计数器bug.
                $cActions --;
            } else { // bugfix：unset($actions[$index])后其实$action变量的内容还在的.
                $totalCpoint += $action['cpoint']; // 所有已执行的action和要执行的action中累计的c点数.
                $totalScore += $action['score'];
            }
        }

        // 周期内次数已达到极限， 动作直接pass.
        if ($rule['r_count'] != - 1 && $cActions >= $rule['r_count']) {
            // 未达到重复次数上限
            LogHelper::pushLog('ErrTask_' . $task['id'], 'r_count_' . $cActions);
            return false;
        }

        $boolCheck = false;
        if ($rule['r_cpoint'] == - 1) {
            // C点无上限
            $newAction['cpoint'] = $cs['cpoint'];
            $boolCheck = true;
        } elseif ($totalCpoint < $rule['r_cpoint']) { // 周期内c点上限判断
            // 未达到C点上限
            $intRemain = $rule['r_cpoint'] - $totalCpoint;
            $newAction['cpoint'] = ($intRemain > $cs['cpoint']) ? $cs['cpoint'] : $intRemain;
            $boolCheck = true;
        }
        if ($rule['r_score'] == - 1) {
            // 经验值无上限
            $newAction['score'] = $cs['score'];
            $boolCheck = true;
        } elseif ($totalScore < $rule['r_score']) { // 周期内经验值上限判断
            // 未达到经验值上限
            $intRemain = $rule['r_score'] - $totalScore;
            $newAction['score'] = ($intRemain > $cs['score']) ? $cs['score'] : $intRemain;
            $boolCheck = true;
        }
        if (! $boolCheck) { // 检查不通过.
            LogHelper::pushLog('ErrTask_' . $task['id'], 'check');
            return false;
        }

        // 新动作的cpoint & score都为0
        if ($newAction['cpoint'] == 0 && $newAction['score'] == 0) {
            LogHelper::pushLog('ErrTask_' . $task['id'], 'csEqualZero');
            return false;
        }

        if ($rFlag == 0) { // 无周期模式
            $actions = array(); // 无周期模式不记录actions
        } else {
            // 将新的action注入到actions数组.
            $actions[] = $newAction;
            // 在无周期情况下actions太大导致存储问题.
            if ($cActions + 1 > self::MAX_ACTION_NUMS) {
                LogHelper::warning('Action num is too big with uid:' . $uid . ',tid:' . $task['id'] . ',count:' . ($cActions + 1));
                // @todo 是否进行截取？
                array_shift($actions);
            }
        }
        ++ $counter;

        return $newAction;
    }

    /**
     * 获取某个任务的积分 & 经验
     * @param $task array 任务信息
     * @param $cActions int 周期内执行过的动作数
     * @param $counter 任务总执行次数
     */
    private function getCpointAndScore(array $task, $cActions, $counter)
    {
        $cs = array(
            'cpoint' => 0,
            'score' => 0
        );
        if ($task['rule']['r_step'] == 1 && $task['rule']['r_flag'] == 1) { // step和是否周期相关
            $num = $cActions + 1; // 周期下的总数 +1
        } else {
            $num = $counter + 1; // 总数 +1
        }

        // 积分
        if (empty($task['step_cpoint'])) { // 未设置阶梯积分
            $cs['cpoint'] = $task['cpoint'];
        } else {
            krsort($task['step_cpoint']);
            foreach ($task['step_cpoint'] as $c => $n) {
                if ($num >= $c) {
                    $cs['cpoint'] = floatval($n);
                    LogHelper::pushLog('StepCpoint_' . $task['id'], $n);
                    break;
                }
            }
        }

        // 经验
        if (empty($task['step_score'])) { // 未设置阶梯经验
            $cs['score'] = $task['score'];
        } else {
            krsort($task['step_score']);
            foreach ($task['step_score'] as $c => $n) {
                if ($num >= $c) {
                    $cs['score'] = floatval($n);
                    LogHelper::pushLog('StepScore_' . $task['id'], $n);
                    break;
                }
            }
        }

        return $cs;
    }

    /**
     * 计算一个周期的起始时间
     *
     * @param int $repeatFlag
     * @param int $ratio
     * @param string $taskStart
     * @return number
     */
    public function getRepeatStart($repeatFlag, $ratio, $taskStart)
    {
        if ($repeatFlag == CmConst::RULE_REPEAT_PERIOD_DAY) { // 天
            $timeSpace = 'DAY';
            $period = $ratio * UtilHelper::DAY;
        } elseif ($repeatFlag == CmConst::RULE_REPEAT_PERIOD_WEEK) { // 周
            $timeSpace = 'WEEK';
            $period = $ratio * UtilHelper::WEEK;
        } else {
            return 0;
        }

        $taskStart = ($taskStart == '') ? 0 : strtotime($taskStart);
        $taskStart = UtilHelper::getStartTime($taskStart, $timeSpace);
        $time = UtilHelper::getStartTime(UtilHelper::now(), $timeSpace);
        $intRtn = $time - ($time - $taskStart) % $period;

        return $intRtn;
    }

    // 【暂时不用】
    private function formatAction(array $action)
    {
        return implode(':', array_values($action));
    }

    /**
     * 【暂时不用】
     * 解析字符串action为数组
     * @param string $strAction
     * @return multitype:|number
     */
    private function parseAction($strAction)
    {
        $count = 5;
        $arr = explode(':', $strAction);
        if (count($arr) < $count) {
            LogHelper::warning('Action less than ' . $count . ' with strAction:' . $strAction);
            return array();
        }
        $rst['t_id'] = intval($arr[0]);
        $rst['target'] = $arr[1];
        $rst['c_time'] = intval($arr[2]);
        $rst['cpoint'] = floatval($arr[3]);
        $rst['score'] = floatval($arr[4]);

        return $rst;
    }
}
