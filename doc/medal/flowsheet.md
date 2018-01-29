## 流程图
###创建账户流程图
```flow
st=>start: 开始
e=>end: 返回信息
io1=>inputoutput: 输入昵称、地址
sub1=>operation: 数据库查询地址
cond=>condition: 是否有此用户 
op=>operation: 创建用户信息

st->io1->sub1->cond->op
cond(no)->op->e
cond(yes)->e
```
--

### 创建勋章流程图
```flow
st=>start:  开始
e=>end: 返回信息
io1=>inputoutput:  主题id、勋章名称、刻字内容、勋章材质、价格、图片信息、地址
sub1=>operation: 验证传参
cond=>condition: 参数是否验证通过 
op=>operation: 创建勋章

st->io1->sub1->cond->op
cond(yes)->op->e
cond(no)->e
```
--

###我的勋章列表-我的资产
```flow
st=>start:  开始
e=>end: 返回信息
io1=>inputoutput: 地址、page、每页展示多少
sub1=>operation: 数据库查询
cond=>condition: 数据库是否有信息
op=>operation: 勋章列表信息

st->io1->sub1->cond->op
cond(yes)->op->e
cond(no)->e
```
--

###勋章详情
```flow
st=>start:  开始
e=>end: 返回信息
io1=>inputoutput: 勋章id
sub1=>operation: 勋章表查询、勋章交易表查询
cond=>condition: 数据库是否有信息
op=>operation: 勋章详细数据

st->io1->sub1->cond->op
cond(yes)->op->e
cond(no)->e
```
--

###勋章交易历史
```flow
st=>start:  开始
e=>end: 返回信息
io1=>inputoutput: 用户地址
sub1=>operation: 勋章表查询、勋章交易表查询
cond=>condition: 数据库是否有信息
op=>operation: 交易历史数据

st->io1->sub1->cond->op
cond(yes)->op->e
cond(no)->e
```
--

###勋章转赠
```flow
st=>start:  开始
e=>end: 返回信息
io1=>inputoutput: 勋章ID、转增者地址、接收者地址
sub1=>operation: 勋章ID、转增者地址查询数据库
cond=>condition: 该勋章是否在该地址中
op=>operation: 转赠勋章

st->io1->sub1->cond->op
cond(yes)->op->e
cond(no)->e
```
--

### 划转记录

```flow
st=>start: app传参
e=>end: 返回数据
op1=>operation: 接收参数
cond=>condition: 校验地址
op2=>operation: 查询数据库

st->op1->cond
cond(yes)->op2->e
cond(no)->e
```
--

### 划转通知

```flow
st=>start: app传参
e=>end: 返回数据
op=>operation: 接收参数
cond1=>condition: 校验地址
cond2=>condition: 校验交易id
er=>operation: 返回错误
st->op->cond1
cond1(yes, right)->cond2(yes, right)->e
cond1(no)->e
cond2(no)->e
```
--

###根据txid 监听 以太网络/ug网络（脚本）

```flow
st=>start: 开始
e=>end: 返回数据
op=>operation: 根据初始化块,循环扫链上"块"交易信息
cond1=>condition: "块"交易信息唯一标识是否在数据库中
cond2=>condition: 是否更新数据库中status字段修改为 确认块状态 成功
cond3=>condition: 是否通知以太网络/ug网络 owner执行者
cond4=>condition: 是否更新数据库中status字段修改为 通知owner状态成功
op1=>operation: 更新本地 “块”
st->op->cond1
cond1(yes, left)->cond2(yes, left)->cond3(yes, left)->cond4(yes, left)->op1->e
cond1(no)->op1->e
cond2(no)->e
cond3(no)->op1->e
cond4(no)->op1->e
```

###主动 监听 以太网络/ug网络（脚本）

```flow
st=>start: 开始
e=>end: 返回数据
op=>operation: 根据初始化块,循环扫链上"块"交易信息
cond1=>condition: "块"交易含有data.freeze标识,插入数据,状态为确认块状态
cond3=>condition: 是否通知以太网络/ug网络 owner执行者
cond4=>condition: 是否更新数据库中status字段修改为 通知owner状态成功
op1=>operation: 更新本地 “块”
st->op->cond1
cond1(yes, left)->cond3(yes, left)->cond4(yes, left)->op1->e
cond1(no)->op1->e
cond3(no)->op1->e
cond4(no)->op1->e
```

### 处理在确认块状态下，24小时，未发送给owner执行者交易信息

```flow
st=>start: 开始
e=>end: 返回数据
op=>operation: 查询数据库状态为:确认块且超时的数据信息
cond3=>condition: 是否通知以太网络/ug网络 owner执行者
cond4=>condition: 是否更新数据库中status字段修改为 通知owner状态成功
st->op->cond3
cond3(yes, left)->cond4(yes, left)->e
cond3(no)->e
cond4(no)->e
```

### 监听owner转账

```flow
st=>start: 开始
e=>end: 返回数据
op=>operation: 根据初始化块,循环扫链上"块"交易信息
cond1=>condition: "块"交易含有data.dfreeze标识,且地址&amount一致
cond4=>condition: 是否更新数据库中status字段修改为 owner转账成功 & owner_txid
op1=>operation: 更新本地 “块”
st->op->cond1
cond1(yes, left)->cond4(yes, left)->op1->e
cond1(no)->op1->e
cond4(no)->e
```

