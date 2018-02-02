doc/api.md
[TOC]
## 玫瑰接口文档

### 测试环境host
- `http://t9-cname.ugchain.com`

### 生产环境host
- `http://app.ugchain.com`


### 创建玫瑰
**请求URL：** 
- `/rose/rose/create-rose`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|theme_id |是  |string |  主题id (1-7)|
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

### 我的玫瑰列表-我的资产
**请求URL：** 
- `/rose/rose/get-list`
  
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
               "amount":"勋章价格",               
               "create_time": "1516006696",
               "update_time": "1516006696"
           },
           {
               "id": "2",
               "address":"y7y7y7y7y7u8u8u8i9i9io0",
               "icon": "/inamge/aadasd.jpg",
               "medal_name": "阿瓦隆大狗币2",
               "theme_name": "刻字内容",
               "material_type":"勋章材质",
               "amount":"勋章价格",               
               "create_time": "1516006696",
               "update_time": "1516006696"
           }
       ],
       "page": "1",
       "pageSize": "10",
       "count": "100",
       "is_next_page":"1" //1有下一页 0暂无数据,
       "image_url":"http://t9-cname.ugchain.com"
      }
  }
```

--

### 勋章详情
**请求URL：** 
- `/rose/rose/rose-detail`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|rose_id |是  |string | 勋章id |
|page |否  |string |  |
|pageSize |否  |string |  |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "list": [
            {
                "address": "r74ry74yr74yr74y7ry74yr7y47yry4ry4rrrrewr",
                "addtime": "1516174975"
            }
        ],
        "is_next_page": "0",
        "count": "1",
        "page": "1",
        "pageSize": "10"
    }
}
```
--

### 勋章交易历史
**请求URL：** 
- `/rose/rose/rose-history`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|address |是  |string | 用户地址 |
|page |否  |string | 第几页（默认0）  |
|pageSzie |是  |string | 每页展示多少（默认10） |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": {
        "list": [
            {
                "id": "2",
                "theme_id": "1",
                "token_id": "MTUxNjM2NjMxMDU=",
                "theme_img": "uploads/1516366310696.jpg",
                "theme_thumb_img": "uploads/1516366310875.jpg",
                "medal_name": "周康勋章",
                "theme_name": "新年快乐",
                "material_type": "5",
                "amount": "2000",
                "address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9",
                "status": "1",   //0转增中；1成功；2失败
                "addtime": "1516366310",
                "medal_id": "2",
                "from_address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9",
                "to_address": ""
            }
        ],
        "is_next_page": "0",
        "count": 1,
        "image_url"=>"http://t9-cname.ugchain.com"

    }
}
```
--

### 勋章转赠
**请求URL：** 
- `/rose/rose/rose-give`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|rose_id |是  |string | 玫瑰ID |
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
