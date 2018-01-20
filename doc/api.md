[TOC]
## 勋章&&虚拟资产接口文档

### 测试环境host
- `http://t9-cname.ugchain.com`

### 生产环境host
- `http://app.ugchain.com`

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
|pageSize |是  |string | 每页展示多少（默认10） |

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
    "message": "成功",
    "data": {
        "medal_info": {
            "id": "2",
            "token_id": "MTUxNjM2NjMxMDU=",
            "theme_img": "uploads/1516366310696.jpg",
            "theme_thumb_img": "uploads/1516366310875.jpg",
            "medal_name": "周康勋章",
            "theme_name": "新年快乐",
            "material_type": "5",
            "amount": "2000",
            "address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9",
            "addtime": "1516366310",
            "founder": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9"
        },
        "list": [
            {
                "address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9",
                "addtime": "1516366310"
            }
        ]
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
|page |否  |string | 第几页（默认0）  |
|pageSize |是  |string | 每页展示多少（默认10） |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "list": [
            {
                "id": "2",  //勋章id
                "theme_id": "1",   //主题ID
                "token_id": "MTUxNjM2NjMxMDU=", //唯一ID
                "theme_img": "uploads/  1516366310696.jpg", //主题图片
                "theme_thumb_img": "uploads/1516366310875.jpg", //主题缩略图片
                "medal_name": "周康勋章", //勋章名称
                "theme_name": "新年快乐", //刻字
                "material_type": "5", //材质 1:钻石2:水晶3:金质4:银质5:铜质
                "amount": "2000",  //价格
                "address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9", //持有者地址
                "status": "1",  //勋章状态 0:铸造中 1:成功 2:失败
                "addtime": "1516366310", //创建时间
                "medal_id": "2",
                "from_address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9",
                "to_address": ""
            }
        ],
        "is_next_page": "0",
        "count": 1
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

### 跨链划转记录
**请求URL：** 
- `/user/asset/transfer-record`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 地址 |
|page |否  |string | 第几页(默认1) |
|pageSize |否  |string | 几条（默认10） |
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

### 跨链划转通知
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
--

### 广告位申请
**请求URL：** 
- `/user/user/create-advertise`

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 地址 |
|phone |是  |string |手机号 |

**返回示例**

``` 
{
 "code": "0",
 "message": "success",
 "data": ""
}
```
--

### UG划转通知
**请求URL：** 
- `/user/asset/ug-transfer-notice`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|from |是  |string | 地址 |
|to |是  |string | 地址 |
|txid |是  |string |交易id |
|amount |是  |string |价格 |

 **返回示例**

``` 
{
 "code": "0",
 "message": "success",
 "data": ""
}
```

--

### UG交易列表
**请求URL：** 
- `/user/asset/trade-record`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|address |是  |string | 地址 |
|page |否  |string | 当前页 |
|pageSize |否  |string |每页展示数据 |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "list": [
            {
                "id": "2",
                "app_txid": "312321",
                "ug_txid": "31231",
                "from_address": "222",//转账地址
                "to_address": "4231",//接收地址
                "amount": "100",//金额
                "blocknumber": "101",//块
                "status": "1",//状态 0:待确认,1:成功 2:失败
                "addtime": "3123132",//创建时间
                "trade_time":"3123141"//交易时间
            },
            {
                "id": "1",
                "app_txid": "222",
                "ug_txid": "222",
                "from_address": "1111",
                "to_address": "222",
                "amount": "100",
                "blocknumber": "100",
                "status": "1",
                "addtime": "31232131",
                "trade_time":"3123141"
            }
        ],
        "is_next_page": "0",//是否有下一页
        "count": "2",
        "page": "1",
        "pageSize": "10"
    }
}
```

