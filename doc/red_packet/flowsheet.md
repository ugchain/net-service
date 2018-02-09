# 流程图
### 主题创建接口

```flow
st=>start: 接口传参
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 校验主题、图片
op4=>operation: 添加到数据库
st->op1->cond
cond(yes)->op4->e
cond(no)->e
```
--

### 主题列表接口

```flow
st=>start: 开始
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 查询数据库
op4=>operation: 组装数据列表
st->op1->cond
cond(yes)->op4->e
cond(no)->e
```
--

### 中心化账户地址接口

```flow
st=>start: 开始
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 查询配置信息
op4=>operation: 组装数据列表
st->op1->cond
cond(yes)->op4->e
cond(no)->e
```
--

### 创建红包

```flow
st=>start: 开始
e=>end: 返回数据
op=>operation: 接收参数
cond1=>condition: 判断参数
cond2=>condition: 生成随机红包金额
cond3=>condition: 存入redis缓存中
cond4=>condition: 发送给UG网络离线签名(tranfer)
cond5=>condition: 组装数据插入数据库
cond6=>condition: 链上查询是否上链
op1=>operation: 组装数据信息
st->op->cond1
cond1(yes, left)->cond2(yes, left)->cond3(yes, left)->cond4(yes, left)->cond5(yes, left)->cond6(yes, left)->op1->e
cond1(no)->op1->e
cond2(no)->op1->e
cond3(no)->op1->e
cond4(no)->op1->e
cond5(no)->op1->e
cond6(no)->op1->e
```

--

### 兑换红包

```flow
st=>start: app传参
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 校验code，address
op2=>operation: 查询数据库
op3=>operation: 发送交易上链
op4=>operation: 修改数据库

st->op1->cond
cond(yes)->op2->op3->op4->e
cond(no)->e
```
--


### 红包详情
```flow
st=>start: app传参
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 校验参数
op2=>operation: 查询数据库


st->op1->cond
cond(yes)->op2->e
cond(no)->e
```

### 红包列表
```flow
st=>start: app传参
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 校验参数address
op2=>operation: 查询数据库


st->op1->cond
cond(yes)->op2->e
cond(no)->e
```

