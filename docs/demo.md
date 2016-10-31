# 帐号打通接口设计

###更新历史

更新时间 |更新简介 | 更新人
:------- |:--------| -----
2014.8.10 |文档创建。接口初次定义| xxx
***

### 目的
demo展示

### 接口定义与说明

* [Demo展示](#Demo展示) 
* [Inner接口展示](#Inner接口展示) 


#### <b id='Demo展示'>Demo展示</b>
这里写说明
#####URL
https://xxxx/demo/demo
#####参数
* userId 用户ID

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
			"uid": "EEWERDXXXXX"
		}

	}
  
  

#### <b id='Inner接口展示'>Inner接口展示</b>
获取用户是否有新动态

#####URL
https://XXXX/demo/inner/test

#####参数
* uid 用户id

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
			"uid": "DDXX133DD"
		}
	}
	
