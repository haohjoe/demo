<?php

/**
 * RuleEngine class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/11/25
 *
 */
class RuleEngine
{
    public $solution;
    public $objDataUserScore;

    public function __construct($solution)
    {
        $this->solution = $solution;
        $this->objDataUserScore = Factory::create('DataUserScore', array($this->solution));
    }
    
    /**
     * 执行引擎
     * @param string $uid 用户uid
     * @param string $target
     * @param int $times 次数
     * @param array $preTasks 需要去完成的任务 
     * @param array $userTasks 用户需要完成任务中存在的历史做过的任务.
     * @param int $intCurGrade 当前经验等级
     * @throws Exception
     * @return array
     */
    public function run($uid, $target, $times, $preTasks, $userTasks, $intCurGrade)
    {
        $now = UtilHelper::now();
        
        // 执行 "基本规则" 判定逻辑
        $tasks = array();
        foreach ($preTasks as $tid => $val) {
            if ($val['status'] == 0) {
                // 任务状态已下线
                LogHelper::pushLog('ErrTask_' . $tid, 'status');
                continue;
            }
            if ($val['rule']['s_time'] != '' && $now < strtotime($val['rule']['s_time'])) {
                // 任务没有开始
                LogHelper::pushLog('ErrTask_' . $tid, 's_time');
                continue;
            }
            if ($val['rule']['e_time'] != '' && $now > strtotime($val['rule']['e_time'])) {
                // 任务已经截止
                LogHelper::pushLog('ErrTask_' . $tid, 'e_ime');
                continue;
            }
            if ((isset($val['rule']['s_grade']) && $val['rule']['s_grade'] != - 1) && $intCurGrade < $val['rule']['s_grade']) {
                // 用户等级太低
                LogHelper::pushLog('ErrTask_' . $tid, 's_grade_' . $intCurGrade);
                continue;
            }
            if ((isset($val['rule']['e_grade']) && $val['rule']['e_grade'] != - 1) && $intCurGrade > $val['rule']['e_grade']) {
                // 用户等级太高
                LogHelper::pushLog('ErrTask_' . $tid, 'e_grade' . $intCurGrade);
                continue;
            }
            if (! isset($userTasks[$tid])) {
                // 从来没有执行过该任务
                $tasks[$tid] = $val;
                continue;
            }
            if ($val['rule']['repeat'] == 0) {
                // 过滤掉不能重复执行的任务，执行到此处说明该任务已做过.
                LogHelper::pushLog('ErrTask_' . $tid, 'repeat');
                continue;
            }
            $tasks[$tid] = $val;
        }

        // --对每个可能的任务，执行规则判定逻辑--
        $cTasks = false;
        foreach ($tasks as $tid => $val) {
            if (! isset($val['rule']['ref_class']) || empty($val['rule']['ref_class'])) {
                throw new Exception('msg[ref_class conf missing] taskId[' . $tid . ']');
            }
            // 用户某个任务下已执行过的动作.
            $actions = isset($userTasks[$tid]['actions']) ? $userTasks[$tid]['actions'] : array();
            // 用户某个任务支持总次数
            $counter = empty($userTasks[$tid]['counter']) ? 0 : $userTasks[$tid]['counter'];
            // 根据ref_class，选取规则判定器.
            $objRuleHandler = Factory::create($val['rule']['ref_class']);
            if (false === $objRuleHandler) {
                throw new Exception('msg[create rule ref class fail] ref_class[' . $val['rule']['ref_class'] . ']');
            }
            
            if ($val['rule']['batch'] == 0 || $val['rule']['repeat'] == 0) {
                // 不支持批量执行任务
                $remainTimes = 1;
            } else {
                $remainTimes = $times;
            }
            // --循环获取一个任务remainTimes次后的数据--
            while ($remainTimes --) {
                // 调用判定器得到新的可执行的动作,同时已有的$actions也会将新动作添加进去（$actions是完整的）
                $newAction = $objRuleHandler->handle($uid, $val, $target, $actions, $counter);
                // 无新动作需要做.
                if (false === $newAction) {
                    LogHelper::pushLog('ArrNewActionIsEmpty', 'false');
                    break;
                }
                // $cTasks 中没tid记录 用于初始化.
                if (! isset($cTasks[$tid])) {
                    if (isset($userTasks[$tid])) { // 已有完成记录
                        // 重复完成任务
                        $cTasks[$tid] = $userTasks[$tid];
                        $cTasks[$tid]['cpoint'] = 0;
                        $cTasks[$tid]['score'] = 0;
                        $cTasks[$tid]['incCounter'] = 0;
                    } else {
                        // 完成了新任务
                        $cTasks[$tid] = array(
                            'uid' => $uid,
                            't_id' => $tid,
                            'c_time' => $now,
                            'u_time' => $now,
                            'actions' => null,
                            'incCounter' => 0,
                            'cpoint' => 0,
                            'score' => 0
                        );
                    }
                }
                
                $cTasks[$tid]['u_time'] = $now;
                $cTasks[$tid]['actions'] = $actions;
                $cTasks[$tid]['incCounter'] += 1;
                $cTasks[$tid]['cpoint'] += $newAction['cpoint']; // 新动作中要加的c点
                $cTasks[$tid]['score'] += $newAction['score']; // 新动作中要加的经验值
            }
        }
        
        return $cTasks;
    }
}
