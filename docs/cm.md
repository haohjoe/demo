# 会员体系

配置任务时注意事项：
* 如果配置了任务可重复情况下：请配置r_flag & r_ratio & （r_count || r_score && r_cpoint) 防止恶意刷分。
* 尽量不要以mq的方式进行调取member内部接口，因为mq不小心会导致无限制调接口。

###更新历史

更新时间 |更新简介 | 更新人
:------- |:--------| -----
2014.12.05 |文档创建。接口初次定义| xxx
***

### 目的
会员体系

### 接口定义与说明


#### inner接口
* [获取用户c点](#获取用户c点) 
* [管理员增加用户c点](#管理员增加用户c点) 
* [管理员减少用户c点](#管理员减少用户c点) 
* [用户消费c点](#用户消费c点) 
* [检查用户消费是否成功](#检查用户消费是否成功) 
* [用户撤销c点](#用户撤销c点) 
* [获取用户订单列表](#获取用户订单列表) 
* [获取任务配置列表](#获取任务配置列表)
* [获取等级配置列表](#获取等级配置列表)
* [更新任务配置](#更新任务配置)
* [批量获取用户积分、经验、等级](#批量获取用户积分、经验、等级)
* [根据积分、经验值排序分页获取用户](#根据积分、经验值排序分页获取用户)
* [执行任务](#执行任务)

#### <b id='获取用户c点'>获取用户c点</b>
获取某个用户

#####URL
https://HOST/cm/inner/user/getCpoint

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
uid | 必填 | string | 用户uid

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			"cpoint": 1
		}
	}
	
#### <b id='管理员增加用户c点'>管理员增加用户c点</b>
管理员增加用户c点，请谨慎使用！！！

#####URL
https://HOST/cm/inner/user/adminAddCpoint

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
uid | 必填 | string | 用户uid
cpoint | 必填 | float | c币
adminId | 必填 | string | 管理员id

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			"cpoint": 100.00, // 当前c币
			"result": 1  	  // 1:成功；0：失败
		}
	}
	
#### <b id='管理员减少用户c点'>管理员减少用户c点</b>
管理员减少用户c点，请谨慎使用！！！

#####URL
https://HOST/cm/inner/user/adminReduceCpoint

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
uid | 必填 | string | 用户uid
cpoint | 必填 | float | c币
adminId | 必填 | string | 管理员id

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			"cpoint": 100.00, // 当前c币
			"result": 1  	  // 1:成功；0：失败
		}
	}

#### <b id='用户消费c点'>用户消费c点</b>
用户消费c点

#####URL
https://HOST/cm/inner/useCpoint/consume

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
orderId | 必填 | string | 订单id
uid | 必填 | string | 用户uid
cpoint | 必填 | float | 消费的c点数量

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			"cpoint":12,
			'id':"mall_11" ,//消费id
		}
	}
	
#### <b id='检查用户消费是否成功'>检查用户消费是否成功</b>
检查用户消费是否成功

#####URL
https://HOST/cm/inner/order/checkConsumeIsSucc

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
orderId | 必填 | string | 订单id
uid | 必填 | string | 用户uid

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			"result":1, // 是否消费成功 1：成功；0：失败
		}
	}


#### <b id='用户撤销c点'>用户撤销c点</b>
用户撤销c点

#####URL
https://HOST/cm/inner/useCpoint/revoke

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
orderId | 必填 | string | 订单id
uid | 必填 | string | 用户uid
cpoint | 必填 | float | 撤销的c点数量

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			'id':"mall_11" ,//消费id
		}
	}
	
#### <b id='获取用户订单列表'>获取用户订单列表</b>
获取用户订单列表

#####URL
https://HOST/cm/inner/order/revoke

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
orderId | 必填 | string | 订单id
uid | 必填 | string | 用户uid
cpoint | 必填 | float | 撤销的c点数量

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			'id':"mall_11" ,//消费id
		}
	}
	
	
#### <b id='获取任务配置列表'>获取任务配置列表</b>
获取任务配置列表

#####URL
https://HOST/cm/inner/cnf/taskCnfList

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			[{
				'id' : 1 , // 任务id
				'name': '分享任务', // 任务名称
				'desc': '分享任务', // 任务描述
				'score' : 1 , // 经验
				'cpoint' : 1 , // 积分
				'step_score' : {1:10,2:20} , // 阶梯经验
				'step_cpoint' : {1:10,2:20} , // 阶梯积分
				'status' : 1 , // 任务状态 
				's_time' : 1 , // 开始时间
				'e_time' : 1 , // 结束时间
				's_grade' : 1 , // 开始等级
				'e_grade' : 1 , // 结束等级
				'batch' : 1 , // 是否可批量执行
				'repeat' : 1 , // 是否可以重复完成
				'r_flag' : 1 , // 重复模式
				'r_ratio' : 1 , // 一个周期内最多重复执行多少次
				'r_cpoint' : 1 , // 一个周期内最多获得多少C点
				'r_score' : 1 , // 一个周期内最多获得多少经验值
				'r_filter' : 1 , // 是否需要根据参数
				'r_step' : 1 , // 阶梯经验、积分是否要考虑是否为周期模式. 
				'u_time' : 1333234444, // 最后更新时间（时间戳格式）
			}]
		}
	}
	
	
#### <b id='获取等级配置列表'>获取等级配置列表</b>
获取等级配置列表

#####URL
https://HOST/cm/inner/cnf/gradeCnfList

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			1: 500,
			2: 1000,
			3: 20000
		}
	}
	
	
#### <b id='更新任务配置'>更新任务配置</b>
更新任务配置

#####URL
https://HOST/cm/inner/cnf/taskCnfUpdate

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
id | 必填 | int | 任务id
score | 必填 | float | 经验
cpoint | 必填 | float | 积分
step_score | 选填 | string | 阶梯经验 eg '1:2,20:40.5' 表示第一次 - 第19次每次加2个经验；第20次以上每次加40.5个经验
step_cpoint | 选填 | string | 阶梯积分 eg '1:2,20:40.5' 
status | 必填 | int | 任务状态 (0:开启；1：关闭)
s_time | 选填 | int | 开始时间(时间戳格式，精确到秒)，空字符串表示不限制，默认空字符串
e_time | 选填 | int | 结束时间(时间戳格式，精确到秒)，空字符串表示不限制，默认空字符串
s_grade | 选填 | int | 开始等级，默认-1
e_grade | 选填 | int | 结束等级，默认-1
batch | 选填 | int | 是否可批量执行，取值0/1，默认1
repeat | 选填 | int | 是否可以重复完成，取值0/1，默认1
r_flag | 选填 | int | 重复模式 0：无；1：天；2周，默认0
r_ratio | 选填 | int | 周期系数eg:r_flag为1，r_ratio为2，表示2天为一个周期，默认0
r_count | 选填 | int | 一个周期内最多重复执行多少次，-1表示不限制次数上限，默认-1
r_cpoint | 选填 | float | 一个周期内最多获得多少C点，-1表示不限制经验值上限，默认-1
r_score | 选填 | float | 一个周期内最多获得多少经验值，-1表示不限制C点上限，默认-1
r_filter | 选填 | int | 是否需要根据参数'target'过滤重复动作，取值0/1，默认0
r_step | 选填 | int | 阶梯经验、积分是否要考虑是否为周期模式，取值0/1，默认0

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
		}
	}
	
	
#### <b id='批量获取用户积分、经验、等级'>批量获取用户积分、经验、等级</b>
批量获取用户积分、经验、等级

#####URL
https://HOST/cm/inner/user/listInfo

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
uids | 必填 | string | 用户uid，多个id以','隔开

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": [{
			‘fefefefa0000000000000331’ : {
				‘cpoints’ : 10,
				‘scores’: 10,
				‘grade’: 2
			}
		}]
	}
	
	
#### <b id='根据积分、经验值排序分页获取用户'>根据积分、经验值排序分页获取用户</b>
根据积分、经验值排序分页获取用户

#####URL
https://HOST/cm/inner/user/listSortInfo

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称
sig | 必填 | String | 签名
condition | 必填 | string | 查询条件；支持score=1、cpoint=1、grade=1，可“=*”，只传eg："score"。
sort | 选填 | int | 排序方式；1：正序；-1：倒序，默认倒序-1
page | 选填 | int | 页码；默认1
limit | 选填 | int | 每页显示数；默认20，最大100

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": [{
			‘fefefefa0000000000000331’ : {
				‘cpoints’ : 10,
				‘scores’: 10,
				‘grade’: 2
			}
		}]
	}
	

#### <b id='执行任务'>执行任务</b>
提交执行任务

#####URL
https://HOST/cm/inner/task/submit

#####参数
名称|必须|类型|描述
:------- |:--------|:--------| -----
appName | 必填 | String | app名称如：CameraCircle
sig | 必填 | String | 签名
uid | 必填 | string | 用户id
op | 必填 | string | 操作类型如：share分享
target | 选填 | string | 操作对象
times | 选填 | int | 操作次数，默认为1

#####HTTP请求方式
POST

#####数据格式
urlencode,json

#####返回结果

 	{
		"status": 200,
		"message": "",
		"serverTime": 1396257634, // 服务器时间
		"data": {
			'cpoint' => 5, // 本次新加积分
            'score' => 6, // 本次新加经验
            'newScore' => 50, // 用户最新的总经验
            'newCpoint' => 60, // 用户最新的总积分
            'code' => 0, // 0:成功完成任务;1:规则未通过;2:op map task 失败
            'grades' => array(  // 等级配置
            	1 => 0,
    			2 => 100,
            )
		}
	}
	

#### <b id='错误码'>错误码</b>

错误码|说明
:------- |:--------|:--------| -----
10000 | 不可同时执行，请等待
10001 | c币不足
10003 | 订单不存在
10004 | 订单未支付
10007 | 该订单不可被撤销
10100 | 接口已临时关闭
