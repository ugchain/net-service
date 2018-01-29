## 流程图
# 流程图
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



