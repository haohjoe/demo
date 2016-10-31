<?php
// 等级和经验值的对应关系
$config['grade'] = array(
    1 => 0,
    2 => 100,
    3 => 400,
    4 => 800,
    5 => 1500,
    6 => 3000,
    7 => 10000,
    8 => 25000,
    9 => 50000,
    10 => 100000,
    11 => 200000,
    12 => 300000,
);

$config['task'] = array(
    //参与投票
    array(
        'id' => 1,
        'task_type' => 1, // 1：一般性任务；2：获奖性任务；3：进行积分抽奖
        'name' => '参与投票',
        'desc' => '2经验值/次',
        'ops' => array(
            'vote'
        ),
        'score' => 2,
        'cpoint' => 0,
        'step_score' => array(),    // 阶梯经验 eg array(1 => 2, 100 => 3) 次数>=1 && 次数<100 增加为2; 次数>=100 时为3; 设置了则会覆盖掉score. 【注：key必须从1开始】
        'step_cpoint' => array(),   // 阶梯积分 规则同step_score
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '',
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 1, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期 注意：r_flag模式下target参数是无效的，因为不会记录actions
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => 0, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0, // 是否需要根据参数'target'过滤重复动作
            'r_step' => 0   // 是否考虑阶梯经验、积分的计算使用到r_falg. r_step=1&&r_flag=1时计算次数为周期内次数；；r_step==0 || r_flag==0考虑的则为总次数（“counter”）
        )
    ),
    //上传照片加积分
    array(
        'id' => 2,
        'task_type' => 1,
        'name' => '上传照片加积分',
        'desc' => '20点/次',
        'ops' => array(
            'addpicPoint'
        ),
        'score' => 0,
        'cpoint' => 20,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )

    ),
    //任务结束按排名加积分
    array(
        'id' => 3,
        'task_type' => 2,
        'name' => '排名10%',
        'desc' => '100C点/次',
        'ops' => array(
            'tenpercent'
        ),
        'score' => 0,
        'cpoint' => 100,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 4,
        'task_type' => 2,
        'name' => '排名11%-30%',
        'desc' => '60点/次',
        'ops' => array(
            'thirtypercent'
        ),
        'score' => 0,
        'cpoint' => 60,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 5,
        'task_type' => 2,
        'name' => '排名30%-60%',
        'desc' => '30点/次',
        'ops' => array(
            'sixtypercent'
        ),
        'score' => 0,
        'cpoint' => 30,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 6,
        'task_type' => 2,
        'name' => '排名61%-80%',
        'desc' => '20点/次，50经验值/次',
        'ops' => array(
            'eightypercent'
        ),
        'score' => 0,
        'cpoint' => 20,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 7,
        'task_type' => 1,
        'name' => '浏览挑战',
        'desc' => '10点/次，50经验值/次',
        'ops' => array(
            'watchTask'
        ),
        'score' => 50,
        'cpoint' => 10,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 1, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => 50, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => 10, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 8,
        'task_type' => 2,
        'name' => '排名80%-100%',
        'desc' => '10点/次',
        'ops' => array(
            'hundredpercent'
        ),
        'score' => 0,
        'cpoint' => 10,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 9,
        'task_type' => 1,
        'name' => '分享照片页面',
        'desc' => '20经验值/次',
        'ops' => array(
            'share'
        ),
        'score' => 20,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => 0, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 10,
        'task_type' => 1,
        'name' => '分享成就页面',
        'desc' => '0点/次，20经验值/次',
        'ops' => array(
            'sharechallenge'
        ),
        'score' => 20,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => 0, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 11,
        'task_type' => 1,
        'name' => '上传照片加经验',
        'desc' => '50经验值/次',
        'ops' => array(
            'addpicscore'
        ),
        'score' => 50,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )

    ),

    //任务结束按排名加经验
    array(
        'id' => 12,
        'task_type' => 1,
        'name' => '排名10%',
        'desc' => '，500经验值/次',
        'ops' => array(
            'tenpercentscore'
        ),
        'score' => 500,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 13,
        'task_type' => 2,
        'name' => '排名11%-30%',
        'desc' => '300经验值/次',
        'ops' => array(
            'thirtypercentscore'
        ),
        'score' => 300,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 14,
        'task_type' => 2,
        'name' => '排名30%-60%',
        'desc' => '150经验值/次',
        'ops' => array(
            'sixtypercentscore'
        ),
        'score' => 150,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 15,
        'task_type' => 2,
        'name' => '排名61%-80%',
        'desc' => '100经验值/次',
        'ops' => array(
            'eightypercentscore'
        ),
        'score' => 100,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 16,
        'task_type' => 2,
        'name' => '排名80%-100%',
        'desc' => '50经验值/次',
        'ops' => array(
            'hundredpercentscore'
        ),
        'score' => 50,
        'cpoint' => 0,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 17,
        'task_type' => 1,
        'name' => '投票每日任务',
        'desc' => '20积分/次',
        'ops' => array(
            'voteAward'
        ),
        'score' => 0,
        'cpoint' => 10,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 1, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 2, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    //照片被抽中使用奖励10C
    array(
        'id' => 18,
        'task_type' => 2,
        'name' => '奖励10C',
        'desc' => '照片被抽中使用奖励10积分/次',
        'ops' => array(
            'useMyPic10'
        ),
        'score' => 0,
        'cpoint' => 10,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 1, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 1, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),

    ////////-=-mall专用-=-//////////
    array(
        'id' => 1010,
        'task_type' => 2,
        'name' => '奖励5c币',
        'desc' => '电商抽奖奖励5c币/次',
        'ops' => array(
            'mallAward5'
        ),
        'score' => 0,
        'cpoint' => 5,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1011,
        'task_type' => 2,
        'name' => '奖励10c币',
        'desc' => '电商抽奖奖励10c币/次',
        'ops' => array(
            'mallAward10'
        ),
        'score' => 0,
        'cpoint' => 10,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1012,
        'task_type' => 2,
        'name' => '奖励20c币',
        'desc' => '电商抽奖奖励20c币/次',
        'ops' => array(
            'mallAward20'
        ),
        'score' => 0,
        'cpoint' => 20,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1013,
        'task_type' => 2,
        'name' => '奖励400c币',
        'desc' => '电商抽奖奖励400c币/次',
        'ops' => array(
            'mallAward400'
        ),
        'score' => 0,
        'cpoint' => 400,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1014,
        'task_type' => 2,
        'name' => '奖励50c币',
        'desc' => '电商抽奖奖励50c币/次',
        'ops' => array(
            'mallAward50'
        ),
        'score' => 0,
        'cpoint' => 50,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1015,
        'task_type' => 2,
        'name' => '奖励100c币',
        'desc' => '电商抽奖奖励100c币/次',
        'ops' => array(
            'mallAward100'
        ),
        'score' => 0,
        'cpoint' => 100,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1016,
        'task_type' => 2,
        'name' => '奖励30c币',
        'desc' => '电商抽奖奖励30c币/次',
        'ops' => array(
            'mallAward30'
        ),
        'score' => 0,
        'cpoint' => 30,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1017,
        'task_type' => 2,
        'name' => '奖励300c币',
        'desc' => '电商抽奖奖励300c币/次',
        'ops' => array(
            'mallAward300'
        ),
        'score' => 0,
        'cpoint' => 300,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),

    ////////-=-activity专用-=-//////////
    array(
        'id' => 1101,
        'task_type' => 2,
        'name' => '奖励20c币',
        'desc' => '活动抽奖奖励20c币/次',
        'ops' => array(
            'activityAward20'
        ),
        'score' => 0,
        'cpoint' => 20,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
    array(
        'id' => 1102,
        'task_type' => 2,
        'name' => '奖励50c币',
        'desc' => '活动抽奖奖励50c币/次',
        'ops' => array(
            'activityAward50'
        ),
        'score' => 0,
        'cpoint' => 50,
        'step_score' => array(),
        'step_cpoint' => array(),
        'status' => 1,
        'rule' => array(
            'ref_class' => 'RuleBase',
            's_time' => '', // 任务起止时间，空字符串表示不限制
            'e_time' => '',
            's_grade' => - 1,
            'e_grade' => - 1,
            'batch' => 0, // 是否可以批量执行，取值0/1。打开开关时，使用参数times
            'repeat' => 1, // 是否可以重复完成，取值0/1
            'r_flag' => 0, // 重复模式，0：无周期，1：天周期，2：周周期
            'r_ratio' => 0, // 周期系数，例如r_flag为1，r_ratio为2，表示2天为一个周期
            'r_count' => - 1, // 一个周期内最多重复执行多少次，-1表示不限制次数上限
            'r_score' => - 1, // 一个周期内最多获得多少经验值，-1表示不限制经验值上限
            'r_cpoint' => - 1, // 一个周期内最多获得多少C点，-1表示不限制C点上限
            'r_filter' => 0,
            'r_step' => 0
        )
    ),
);

return $config;
