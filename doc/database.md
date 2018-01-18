# 数据库
##中心服务
###监听eth/ug链服务
    DROP TABLE IF EXISTS `center_bridge`;
    
    CREATE TABLE `center_bridge` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `app_txid` varchar(100) NOT NULL DEFAULT '' COMMENT 'apptxid',
      `chain_txid` varchar(100) NOT NULL DEFAULT '0' COMMENT '链上txid',
      `address` varchar(42) NOT NULL  COMMENT '地址',
      `amount` varchar(255) NOT NULL  COMMENT '价格',
      `blocknumber` varchar(100) NOT NULL DEFAULT '0' COMMENT '链上块',
      `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:eth_ug 2:ug_eth',
      `gas_price` varchar(100) NOT NULL DEFAULT '0' COMMENT 'gas_price',
      `gas_used` varchar(100) NOT NULL DEFAULT '0' COMMENT 'gas_used',
      `owner_txid` varchar(100) NOT NULL DEFAULT '' COMMENT '执行者txid',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:待确认,1:块上成功,2:块上失败,3:发送给对方链成功,4:发送给对方链失败,5:监听owner成功,6:监听owner失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      `block_succ_time` int(11) NOT NULL DEFAULT '0' COMMENT '块上成功时间',
      `block_fall_time` int(11) NOT NULL DEFAULT '0' COMMENT '块上失败时间',
      `block_send_succ_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送给对方链成功时间',
      `block_send_fall_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送给对方链失败时间',
      `block_listen_succ_time` int(11) NOT NULL DEFAULT '0' COMMENT '监听owner成功时间',
      `block_listen_fall_time` int(11) NOT NULL DEFAULT '0' COMMENT '监听owner失败时间',
      PRIMARY KEY (`id`),
      KEY `chain_confirm` (`app_txid`,`type`,`status`),
      KEY `owner_confirm` (`address`,`type`,`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='中心服务表';
    
###ug链上内部转账表
    DROP TABLE IF EXISTS `ug_trade`;
    
    CREATE TABLE `ug_trade` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `app_txid` varchar(100) NOT NULL DEFAULT '' COMMENT 'apptxid',
      `ug_txid` varchar(100) NOT NULL DEFAULT '0' COMMENT 'ugtxid',
      `from_address` varchar(42) NOT NULL COMMENT '转账地址',
      `to_address` varchar(42) NOT NULL COMMENT '接收地址',
      `amount` varchar(255) NOT NULL  COMMENT '价格',
      `blocknumber` varchar(100) NOT NULL DEFAULT '0' COMMENT 'ug链上块',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:待确认,1:成功 2:失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`),
      KEY `chain_confirm` (`app_txid`,`status`),
      KEY `my_trade` (`from_address`,`to_address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ug链上内部转账表';
   
##数据资产管理
###钱包表(暂无使用)
    DROP TABLE IF EXISTS `address`;
    CREATE TABLE `address` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '钱包名称',
      `address` varchar(42) NOT NULL COMMENT '钱包地址',
      `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未删除1:已删除',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_address` (`address`),
      KEY `address` (`address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='钱包表';

##阿瓦隆勋章
###勋章表
    DROP TABLE IF EXISTS `ug_medal`;
    
    CREATE TABLE `ug_medal` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `theme_id` int(11) NOT NULL DEFAULT '1' COMMENT '主题ID,跟app端定义',
      `token_id` varchar(256) NOT NULL DEFAULT '' COMMENT '唯一ID',
      `theme_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题图片',
      `theme_thumb_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题缩略图片',
      `medal_name` varchar(50) NOT NULL COMMENT '勋章名称',
      `theme_name` varchar(255) NOT NULL DEFAULT '' COMMENT '刻字',
      `material_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:钻石2:水晶3:金质4:银质5:铜质',
      `amount` varchar(10) NOT NULL DEFAULT '0' COMMENT '价格',
      `address` varchar(42) NOT NULL COMMENT '持有者地址',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:铸造中 1:成功 2:失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`),
      UNIQUE KEY `token_id` (`token_id`),
      KEY `address` (`address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='勋章表';
###勋章赠送记录表
    DROP TABLE IF EXISTS `ug_medal_give`;
    
    CREATE TABLE `ug_medal_give` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `medal_id` int(11) NOT NULL DEFAULT '0' COMMENT '勋章ID',
      `from_address` varchar(42) NOT NULL COMMENT '转增者地址',
      `to_address` varchar(42) NOT NULL DEFAULT '' COMMENT '接收者地址',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:转赠中 1:成功 2:失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`),
      KEY `medal_id` (`medal_id`),
      KEY `history_give` (`from_address`,`to_address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='勋章赠送记录表';

###广告申请表
    DROP TABLE IF EXISTS `ug_advertise`;
    
    CREATE TABLE `ug_advertise` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `address` varchar(42) NOT NULL  COMMENT '地址',
      `phone` varchar(11) NOT NULL  COMMENT '手机号',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告申请表';