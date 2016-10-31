<?php
// 等级和经验值的对应关系
$config['grade'] = array(
    1 => 0,
    2 => 500,
    3 => 1000
);

$config['task'] = array(
    //========== 用户 ==========//
    array(
        'id' => 100,
        'task_type' => 1,
        'name' => '用户注册', // 即为注册
        'desc' => '用户注册 即 首次登陆，一次性操作',
        'ops' => array(
            'firstLogin'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 101,
        'task_type' => 1,
        'name' => '用户登陆',
        'desc' => '用户登陆',
        'ops' => array(
            'login'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 102,
        'task_type' => 1,
        'name' => '关注用户',
        'desc' => '关注用户',
        'ops' => array(
            'followUser'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 103,
        'task_type' => 1,
        'name' => '用户被关注',
        'desc' => '用户被关注',
        'ops' => array(
            'userBeFollow'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    
    //========== 照片 ==========//
    array(
        'id' => 200,
        'task_type' => 1,
        'name' => '首次发图',
        'desc' => '首次发图，一次性操作',
        'ops' => array(
            'firstPublishPhoto'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 201,
        'task_type' => 1,
        'name' => '发布照片',
        'desc' => '发布照片',
        'ops' => array(
            'publishPhoto'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 202,
        'task_type' => 1,
        'name' => '对照片点赞',
        'desc' => '对照片点赞',
        'ops' => array(
            'likePhoto'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 203,
        'task_type' => 1,
        'name' => '照片被点赞',
        'desc' => '',
        'ops' => array(
            'photoBeLike'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 204,
        'task_type' => 1,
        'name' => '分享照片',
        'desc' => '分享照片',
        'ops' => array(
            'sharePhoto'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 205,
        'task_type' => 1,
        'name' => '照片被分享',
        'desc' => '照片被分享',
        'ops' => array(
            'photoBeShare'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 206,
        'task_type' => 1,
        'name' => '照片被设置精选',
        'desc' => '照片被设置精选',
        'ops' => array(
            'photoBeSetChoice'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 207,
        'task_type' => 1,
        'name' => '评论照片',
        'desc' => '评论照片',
        'ops' => array(
            'commentPhoto'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    
    //========== 专辑  ==========//
    array(
        'id' => 300,
        'task_type' => 1,
        'name' => '创建专辑',
        'desc' => '创建专辑',
        'ops' => array(
            'createAlbum'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 301,
        'task_type' => 1,
        'name' => '推荐专辑',
        'desc' => '推荐专辑',
        'ops' => array(
            'recommandAlbum'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    
    //========== 绑定第三方 ==========//
    array(
        'id' => 400,
        'task_type' => 1,
        'name' => '绑定手机号',
        'desc' => '绑定手机号，一次性操作',
        'ops' => array(
            'bindPhone'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 401,
        'task_type' => 1,
        'name' => '绑定微信',
        'desc' => '绑定微信，一次性操作',
        'ops' => array(
            'bindWeixin'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 402,
        'task_type' => 1,
        'name' => '绑定微博',
        'desc' => '绑定微博，一次性操作',
        'ops' => array(
            'bindSina'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    array(
        'id' => 403,
        'task_type' => 1,
        'name' => '绑定QQ',
        'desc' => '绑定QQ，一次性操作',
        'ops' => array(
            'bindQQ'
        ),
        'rule' => array(
            'ref_class' => 'RuleBase'
        )
    ),
    
);

return $config;
