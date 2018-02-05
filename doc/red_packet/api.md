# api接口：

### 主题创建接口
**请求URL：** 
- `/redpacket/theme/create-theme`
  
**请求方式：**
- POST 

**参数：** 
``
|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|title |是  |string |主题标题 |
|desc |是  |string |主题描述 |
|img |是  |string |主题图片地址 |
|thumb_img |是  |string |主题缩略图地址 |
|share_img |是  |string |分享缩略图地址 |

 **返回示例**

``` 
{````````
    "code": 0,
    "message": "成功",
    "data": []
}

```

### 主题列表接口
**请求URL：** 
- `/redpacket/theme/theme-list`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |

 **返回示例**

``` 
{
	"code": 0,
	"message": "success",
	"data": {
		"list": [{
			"id": "1",
			"title": "名字",
			"desc"："描述"，
			"img": "/upload/test/image2.png",
			"thumb_img": "/upload/test/image.png",
			"share_img": "/upload/test/image1.png",
			"addtime": "5234242"
		}, {
			"id": "2",
			"title": "名字",
			"desc"："描述"，
			"img": "/upload/test/image2.png",
			"thumb_img": "/upload/test/image.png",
			"share_img": "/upload/test/image1.png",
			"addtime": "5234242"
		}],
		"image_url":"http://t9-cname.ugchain.com"
	}
}

```
--

### 中心化账户地址接口
**请求URL：** 
- `/redpacket/common/center-address`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "address": "0x3a96700a6cce699c8219332202eca67b1442fbe1"
    }
}

```
--


### 创建红包接口
**请求URL：** 
- `/redpacket/redpacket/create-packet`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|title |是  |string |红包标题 |
|from_address |是  |string |发红包账户地址 |
|to_address |是  |string |发红包账户地址 |
|amount |是  |string |金额 |
|quantity |是  |string |个数 |
|theme_id |是  |string |主题ID |
|theme_img |是  |string |主题图片 |
|theme_thumb_img |是  |string |主题缩略图 |
|theme_share_img |是  |string |主题分享图 |
|type |是  |string |类型 0等额红包；1随机红包 |
|raw_transaction |是  |string |离线签名 |
|hash |是  |string |txid |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "share_url": "http://ugwallet.local/redpacket/we-chat-red-packet/redirect-url?redpacket_id=8",
        "id": 8,//红包ID
        "status": 1,//状态值 0:部署中 1:创建成功
        "expire_time": "1517652727",//过期时间
        "create_succ_time": "1517566327"//创建成功时间
    }
}

```


--

### 兑换接口
**请求URL：** 
- `/redpacket/redpacket/exchange`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|code |是  |string |红包兑换码 |
|address |是  |string |当前账户地址 |
|type |是  |string |类型 1获取红包信息；2兑换红包 |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "id": 2
        "title": "大吉大利"
        "amount":1.98
    }
}

```
--

### 红包详情
**请求URL：** 
- `/redpacket/redpacket/detail`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 当前账户地址 |
|id |是  |string |红包id |
|page |否  |string | 当前页(默认0) |
|pageSize |否  |string |每页展示数据（默认10）|

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "txid": "3ee2w2ww2w21e3e3",      //交易id
        "title": "冲破天机",              //红包标题
        "status": 2,                    //红包状态0创建红包;1链上失败;2创建成功;3:已领光;4:已过期'
        "quantity": 10,                 //红包个数
        "already_received_quantity": 90,//已领红包个数
        "amount": "100",                //红包金额
        "already_received_amount": "TODO",//已领红包金额
        "finish_time": "",              //领光时间
        "expire_time": "",              //过期时间
        "last_time": "17:04",           //剩余时间时间
        "current_time": "01-31 06:55",  //当前时间
        "theme_img": "",                //主题图片
        "theme_thumb_img": "",          //主题缩略图
        "theme_share_img": "",          //主题分享图
        "redPacketRecordList": [        //领取记录
            {
                "wx_name": "ddew",      //微信昵称
                "wx_avatar": "2321w21", //微信头像
                "amount": "90",         //领取金额
                "status": 4,            //状态 领取状态；1已领取；2兑换中；3兑换失败 4兑换成功 5已过期
                "time": "12-26 44:38"   //领取时间
            }
        ]
        "image_url": "http://wallet-pro.dev:8088" //拼接url
        "share_url": "http://ugwallet.local/redpacket/we-chat-red-packet/redirect-url?redpacket_id=1"//微信分享连接
    }
}

```


### 红包列表
**请求URL：** 
- `/redpacket/redpacket/list`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 当前账户地址 |
|type |是  |string | 类型；0我收到的；1我发出的 |
|page |否  |string | 当前页(默认0) |
|pageSize |否  |string |每页展示数据（默认10）|

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "list": [
            {
                "id": "3",        //红包id
                "title": "小韭菜", //红包名称
                "address": "e3r32rr2r3e23e3e32e32", //红包创建账户
                "amount": "150", //红包金额
                "quantity": "6", //红包数量
                "receive": "1"  //已领数量
                "theme_id": "3", //红包主题
                "theme_img": "",                //主题图片
                "theme_thumb_img": "",          //主题缩略图
                "theme_share_img": "",          //主题分享图
                "txid": "32e322r42r43r4r43rdff43r43", //红包交易id
                "type": "1",    //红包类型0等额红包；1随机红包
                "back_amount": "0", //红包退还金额
                "status": "2",  //(发出状态)红包状态；0创建红包;1创建失败;2创建成功;3:已领光;4:已过期',
                                //（收到状态)领取状态；1已领取；2兑换中(也显示兑换成功)；3兑换失败 4兑换成功 5已过期
                "addtime": "1517723724", //红包创建时间
                "fail_time": "0",   //创建失败时间
                "create_succ_time": "0", //创建成功时间
                "finish_time": "0", //领完时间
                "expire_time": "0", //过期时间(我收到和我发出共用)
                "exchange_time": "1512131232" //兑换时间（我收到的）
            },
            {
                "id": "2",
                "title": "大吉大利",
                "address": "e3r32rr2r3e23e3e32e32",
                "amount": "200",
                "quantity": "30",
                "receive": "0"
                "theme_id": "2",
                "theme_img": "",                //主题图片
                "theme_thumb_img": "",          //主题缩略图
                "theme_share_img": "",          //主题分享图
                "txid": "9i8r47ry74yr7y4y7ry7r4rr4",
                "type": "0",
                "back_amount": "0",
                "status": "2",
                "addtime": "1519888338",
                "fail_time": "0",
                "create_succ_time": "0",
                "finish_time": "0",
                "expire_time": "0",
                "exchange_time": "1512131232" //兑换时间（我收到的）
            },
            {
                "id": "1",
                "title": "冲破天机",
                "address": "e3r32rr2r3e23e3e32e32",
                "amount": "100",
                "quantity": "10",
                "receive": "0"
                "theme_id": "1",
                "theme_img": "",                //主题图片
                "theme_thumb_img": "",          //主题缩略图
                "theme_share_img": "",          //主题分享图
                "txid": "3ee2w2ww2w21e3e3",
                "type": "1",
                "back_amount": "0",
                "status": "2",
                "addtime": "1513243432",
                "fail_time": "0",
                "create_succ_time": "0",
                "finish_time": "0",
                "expire_time": "0",
                "exchange_time": "1512131232" //兑换时间（我收到的）
            }
        ],
        "is_next_page": "0",   //是否有下一页
        "count": 3,         //总记录条数
        "page": "1",        //当前页
        "pageSize": "10",   //一页显示多少条
        "received_amount": 450, //共发出(收到)多少UGC
        "received_quantity": 3  //共发出(收到)多少红包
        "image_url": "http://wallet-pro.dev:8088" //拼接url
    }
}
```

### 微信红包分享接口
**请求URL：** 
- `redpacket/we-chat-red-packet/share`
  
**请求方式：**
- GET/POST

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|openId |是  |string | 微信用户openID  |
|red_packet_id |是  |inter | 红包ID  |


 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "txid": "0xasdasd123asdfasdfvbnghjc", //发起红包的钱包地址
        "title": "冲破天际", //红包主题
        "status": "1", //领取状态；1已领取；2兑换中；3兑换失败 4兑换成功 5已过期
        "quantity": "100", //红包总个数
        "already_received_quantity": "50", //已经领取的个数
        "amount": "100", //红包总额
        "already_received_amount": "100", //已经领取的红包额度
        "code": "", //红包口令
        "record_list": [
            {
                "wx_name": "gengxiankun", //微信昵称
                "wx_avatar": "http://wc.com/gengxk.png", //微信头像
                "amount": "2", //领取金额
            }
        ],
        "qrcode_url": "http://xx.com/xx"
    }
}
```

### 微信红包领取接口
**请求URL：** 
- `redpacket/we-chat-red-packet/receive`
  
**请求方式：**
- POST/GET

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|openId |是  |string | 微信用户openID  |
|nickname |是  |inter | 微信用户昵称  |
|headimgurl |是  |inter | 微信用户头像url  |
|red_packet_id |是  |inter | 红包ID  |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "code": "EScdsqWXxx", //红包口令
    }
}
```

### app端主动监听数据是否上链
**请求URL：** 
- `redpacket/redpacket/listen-repacket`
  
**请求方式：**
- POST

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|packet_id |是  |inter | 红包ID  |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "status": 0, //0:部署中 1:成功
    }
}
```
