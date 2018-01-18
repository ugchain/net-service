[TOC]
## 勋章&&虚拟资产接口文档

### 测试环境host
- `http://t9-cname.ugchain.com`

### 生产环境host
- 待定

### 创建账户(暂无使用)
**请求URL：** 
- `/user/user/create-user`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|nickname |是  |string | 用户昵称  |
|address |是  |string | 账户地址  |

 **返回示例**

``` 
{
  "code": 0,
  "message": "success",
  "data": {
    "uid": 1  //用户id
  }
}
```
--

### 创建勋章
**请求URL：** 
- `/medal/medal/create-medal`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|theme_id |是  |string |  主题id |
|medal_name |是  |string | 勋章名称  |
|theme_img |是  |string | 主题图片  |
|theme_thumb_img |是  |string | 主题缩略图片  |
|theme_name |是  |string | 刻字内容  |
|material_type |是  |string | 勋章材质（1:钻石2:水晶3:金质4:银质5:铜质）  |
|amount |是  |string | 价格 |
|address |是  |string | 地址 |

 **返回示例**

``` 
{
  "code": 0,
  "message": "success",
  "data": {
    "medal_id": 1  //勋章id
  }
}
```

--

### 我的勋章列表-我的资产
**请求URL：** 
- `/medal/medal/get-list`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 账户地址 |
|page |否  |string | 第几页（默认0）  |
|pageSzie |是  |string | 每页展示多少（默认10） |

 **返回示例**

``` 
{
  "code": 0,
  "message": "success",
  "data": {
       "list": [
           {
               "id": "1",
               "address":"y7y7y7y7y7u8u8u8i9i9io0",
               "icon": "/inamge/aadasd.jpg",
               "medal_name": "阿瓦隆大狗币",
               "theme_name": "刻字内容",
               "material_type": "勋章材质",
               "amount":"勋章价格",               "create_time": "1516006696"
           },
           {
               "id": "2",
               "address":"y7y7y7y7y7u8u8u8i9i9io0",
               "icon": "/inamge/aadasd.jpg",
               "medal_name": "阿瓦隆大狗币2",
               "theme_name": "刻字内容",
               "material_type":"勋章材质",
               "amount":"勋章价格",               "create_time": "1516006696"
           }
       ],
       "page": "1",
       "pageSize": "10",
       "count": "100",
       "is_next_page"=>"1"//1有下一页 0暂无数据
      }
  }
```

--

### 勋章详情
**请求URL：** 
- `/medal/medal/medal-detail`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|medal_id |是  |string | 勋章id |

 **返回示例**

``` 
{
	"code": 0,
	"message": "success",
	"data": {
		"medal_info": {
			"medal_id": "1",
			"medal_name": "名称",
			"theme_name": "刻字信息",
			"theme_img": "主题图片",
			"theme_thumb_img": "主题缩略图",
			"amount": "金额",
			"token_id": "12313213112123123",
			"address": "当前持有者信息",
			"founder": "创始人地址"
		},
		"list": [{
			"address": "1233",
			"addtime": "1313231"
		}, {
			"address": "1233",
			"addtime": "1313231"
		}, {
			"address": "1233",
			"addtime": "1313231"
		}, {
			"address": "1233",
			"addtime": "1313231"
		}, {
			"address": "1233",
			"addtime": "1313231"
		}]
	}
}
```
--

### 勋章交易历史
**请求URL：** 
- `/medal/medal/medal-history`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 用户地址 |

 **返回示例**

``` 
{
	"code": 0,
	"message": "success",
	"data": {
		"list": [{
			"medal_id": "1",
			"medal_name": "名字",
			"theme_name": "大吉大利,今晚吃鸡",
			"amount": "100",
			"address": "1213132",
			"addtime": "5234242",
			"status": "1"
		},
		{
			"medal_id": "2",
			"medal_name": "名字",
			"theme_name": "大吉大利,今晚吃鸡",
			"amount": "100",
			"address": "1213132",
			"addtime": "5234242",
			"status": "1"
		},
		{
			"medal_id": "3",
			"medal_name": "名字",
			"theme_name": "大吉大利,今晚吃鸡",
			"amount": "100",
			"address": "1213132",
			"addtime": "5234242",
			"status": "1"
		},
		{
			"medal_id": "4",
			"medal_name": "名字",
			"theme_name": "大吉大利,今晚吃鸡",
			"amount": "100",
			"address": "1213132",
			"addtime": "5234242",
			"status": "1"
		}]
	}
}
```
--

### 勋章转赠
**请求URL：** 
- `/medal/medal/medal-give`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|medal_id |是  |string | 勋章ID |
|owner_address |是  |string | 转增者地址 |
|recipient_address |是  |string | 接收者地址 |
 **返回示例**

``` 
{
	"code": 0,
	"message": "success",
	"data": ""
}
```
--

### 划转记录
**请求URL：** 
- `/user/asset/transfer-record`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 地址 |
 **返回示例**

``` 
{
	"code": "0",
	"message": "success",
	"data": {
		"list": [{
			"txid": "11111",
			"type": "1",
			"addtime": "5231312",
			"amount": "200",
			"status": "0"//确认中
		}, 
		{
			"txid": "11111",
			"type": "1",
			"addtime": "5231312",
			"amount": "200",
			"status": "5"//成功
		},
		{
			"txid": "11111",
			"type": "1",
			"addtime": "5231312",
			"amount": "200",
			"status": "6"//失败
		}]
	}
}
```
--

### 划转通知
**请求URL：** 
- `/user/asset/transfer-notice`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 地址 |
|txid |是  |string |交易id |
|amount |是  |string |价格 |
|type |是  |string |通知类型（1:eth->ug 2:ug->eth） |

 **返回示例**

``` 
{
 "code": "0",
 "message": "success",
 "data": ""
}
```

