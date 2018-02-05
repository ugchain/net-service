doc/api.md
[TOC]
## 玫瑰接口文档

### 测试环境host
- `http://t9-cname.ugchain.com`

### 生产环境host
- `http://app.ugchain.com`

### 主题创建接口
**请求URL：** 
- `/rose/theme/create-theme`
  
**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|title |是  |string |主题标题 |
|img |是  |string |主题图片地址 |
|thumb_img |是  |string |主题缩略图地址 |
|content |是  |string |内容 |

 **返回示例**

``` 
{
    "code": 0,
    "message": "成功",
    "data": []
}

```

----

### 主题创建接口
**请求URL：** 
- `/rose/theme/theme-list`
  
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
			"content":"大四淡定啦",
			"img": "/upload/test/image2.png",
			"thumb_img": "/upload/test/image.png",
			"share_img": "/upload/test/image1.png",
			"addtime": "5234242"
		}, {
			"id": "2",
			"title": "名字",
			"content":"大四淡定啦",
			"img": "/upload/test/image2.png",
			"thumb_img": "/upload/test/image.png",
			"share_img": "/upload/test/image1.png",
			"addtime": "5234242"
		}],
		"image_url":"http://t9-cname.ugchain.com"
	}
}

```

----

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
    "rose_id": 1  //勋章id
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
    "message": "成功",
    "data": {
        "list": [
            {
                "id": "1",
                "theme_id": "2",
                "token_id": "0336D537CB4ECB4E3E3C1A943E3C1A9435F41A9435F45E2E35F45E2E9C0E5E2E",
                "theme_img": "uploads/1517566795471.jpg",
                "theme_thumb_img": "uploads/1517566795142.jpg",
                "rose_name": "银玫瑰4343",
                "theme_name": "金金的打发开发了",
                "material_type": "1",
                "amount": "5000",
                "address": "0x46683c946c4970b0d3f6f2ed18771a1ae34e9683",
                "status": "1",
                "addtime": "1517567091",
                "update_time": "0"
            },
            {
                "id": "2",
                "theme_id": "3",
                "token_id": "F3D7E0FC82518251C55A4F78C55A4F78B8AE4F78B8AE6B3BB8AE6B3BEC446B3B",
                "theme_img": "uploads/1517566829626.jpg",
                "theme_thumb_img": "uploads/1517566829325.jpg",
                "rose_name": "银玫瑰4343",
                "theme_name": "金金的打发开发了",
                "material_type": "1",
                "amount": "5000",
                "address": "0x46683c946c4970b0d3f6f2ed18771a1ae34e9683",
                "status": "1",
                "addtime": "1517567095",
                "update_time": "0"
            },
            {
                "id": "3",
                "theme_id": "4",
                "token_id": "9F6D858E035D035D4EDF008A4EDF008A479F008A479F945B479F945B4673945B",
                "theme_img": "uploads/1517566852510.jpeg",
                "theme_thumb_img": "uploads/1517566852995.jpeg",
                "rose_name": "银玫瑰4343",
                "theme_name": "金金的打发开发了",
                "material_type": "1",
                "amount": "1000",
                "address": "0x46683c946c4970b0d3f6f2ed18771a1ae34e9683",
                "status": "1",
                "addtime": "1517567106",
                "update_time": "0"
            },
            {
                "id": "4",
                "theme_id": "5",
                "token_id": "BF820A30475D475D78B5A49678B5A4960E38A4960E38CA2B0E38CA2BE736CA2B",
                "theme_img": "uploads/1517566874394.jpeg",
                "theme_thumb_img": "uploads/1517566874166.jpeg",
                "rose_name": "银玫瑰4343",
                "theme_name": "金金的打发开发了",
                "material_type": "1",
                "amount": "2000",
                "address": "0x46683c946c4970b0d3f6f2ed18771a1ae34e9683",
                "status": "1",
                "addtime": "1517567112",
                "update_time": "0"
            }
        ],
        "is_next_page": "0",
        "count": "4",
        "page": "1",
        "pageSize": "10",
        "image_url": "http://t9-cname.ugchain.com"
    }
}
```

--

### 玫瑰详情
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

### 玫瑰交易历史
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
                "rose_name": "周康勋章",
                "theme_name": "新年快乐",
                "material_type": "5",
                "amount": "2000",
                "address": "0x03afebB4Fa17051a6F2f1306d732161d85E4A6b9",
                "status": "1",   //0转增中；1成功；2失败
                "addtime": "1516366310",
                "rose_id": "2",
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

### 玫瑰转赠
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
