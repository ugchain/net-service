##红包数据库表结构
###红包主题表
```
DROP TABLE IF EXISTS `ug_red_packet_theme`;

 CREATE TABLE `ug_red_packet_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `img` varchar(255) NOT NULL COMMENT '主题图片地址',
  `title` varchar(255) NOT NULL COMMENT '主题标题',
  `thumb_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题缩略图地址',
  `share_img` varchar(255) NOT NULL DEFAULT '' COMMENT '分享缩略图地址',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT "红包主题表"
```
--

###红包表
```
 DROP TABLE IF EXISTS `ug_red_packet`;

 CREATE TABLE `ug_red_packet` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(50) NOT NULL COMMENT '红包标题',
  `address` varchar(42) NOT NULL COMMENT '发红包账户',
  `amount` varchar(100) NOT NULL COMMENT '红包总额',
  `quantity` int(10) NOT NULL DEFAULT '1' COMMENT '红包总个数',
  `theme_id` int(11) NOT NULL DEFAULT '1' COMMENT '红包主题id',
  `txid` varchar(50) NOT NULL DEFAULT '' COMMENT 'txid',
  `theme_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题图片地址',
  `theme_thumb_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题缩略图地址',
  `theme_share_img` varchar(255) NOT NULL DEFAULT '' COMMENT '分享缩略图地址',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '红包类型；0等额红包；1随机红包',  
  `back_amount` varchar(100) NOT NULL DEFAULT '0' COMMENT '过期退还红包金额',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '红包类型；0创建红包;1链上失败;2创建成功;3:已领光;4:已过期',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  `fail_time` int(11) NOT NULL DEFAULT '0'  COMMENT '链上失败失败',
  `create_succ_time` int(11) NOT NULL DEFAULT '0'  COMMENT '创建成功',
  `finish_time` int(11) NOT NULL DEFAULT '0'  COMMENT '领光时间',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT "红包表"

```
--

###红包活动离线签名表
```
 DROP TABLE IF EXISTS `ug_packet_offline_signature`;

 CREATE TABLE `ug_packet_offline_signature` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `packet_id` int(11) NOT NULL DEFAULT '1' COMMENT '红包id',
  `address` varchar(42) NOT NULL DEFAULT '' COMMENT '发红包账户',
  `raw_transaction` varchar(255) NOT NULL DEFAULT '' COMMENT '离线签名',
  `type` tinyint(1) DEFAULT '0' COMMENT '签名类型；0发红包；1拆红包',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT "红包活动离线签名表"

```
--

###红包记录表
```
 DROP TABLE IF EXISTS `ug_red_packet_record`;

CREATE TABLE `ug_red_packet_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `rid` int(11) NOT NULL COMMENT '红包关联id',
  `openid` varchar(100) DEFAULT '' COMMENT '微信唯一标识id',
  `wx_name` varchar(50) NOT NULL DEFAULT '' COMMENT '维信昵称',
  `wx_avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '微信头像',
  `amount` varchar(50) NOT NULL DEFAULT '0' COMMENT '领取金额',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '兑换码',
  `txid` varchar(50) NOT NULL DEFAULT '' COMMENT 'txid',
  `from_address` varchar(42) DEFAULT '' COMMENT '发放账户',
  `to_address` varchar(42) DEFAULT '' COMMENT '领取账户',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '领取状态；1已领取；2兑换中；3兑换失败 4兑换成功 5已过期',
  `addtime` int(11) NOT NULL COMMENT '领取时间',
  `exchange_time` int(11) NOT NULL COMMENT '兑换时间',
  `expire_time` int(11) NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT "红包记录表"

```
--

###ug_trade增加type字段
```
`type` tinyint(1) DEFAULT '0' COMMENT '记录类型；0内部交易转账；1红包交易转账',
alter table ug_trade add `type` tinyint(1) DEFAULT '0' COMMENT '记录类型；0内部交易转账；1拆红包交易转账；2创建红包交易转账；3退还红包交易转账'
```

