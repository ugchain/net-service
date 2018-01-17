# 数据库
##中心服务
###监听表
    DROP TABLE IF EXISTS `center_bridge`;
    CREATE TABLE `center_bridge` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `app_txid` varchar(50) NOT NULL DEFAULT '' COMMENT 'apptxid',
      `chain_txid` varchar(50) NOT NULL COMMENT '链上txid',
      `address` varchar(42) NOT NULL COMMENT '地址',
      `amount` varchar(32) NOT NULL COMMENT '价格',
      `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:eth_ug 2:ug_eth',
      `owner_txid` varchar(32) NOT NULL COMMENT '执行者txid',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:待确认,1:块上成功,2:块上失败,3:发送给ug成功,4:发送ug失败,5:监听owner确认成功,6:监听owner失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='center_bridge';
   
##数据资产管理
###钱包表
    DROP TABLE IF EXISTS `address`;
    CREATE TABLE `address` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '钱包名称',
      `address` varchar(42) NOT NULL COMMENT '钱包地址',
      `is_del` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:未删除1:已删除',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='钱包表';

##阿瓦隆勋章
###勋章表
    DROP TABLE IF EXISTS `ug_medal`;
    CREATE TABLE `ug_medal` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
        `theme_id` int(11) NOT NULL DEFAULT '1' COMMENT '主题ID,跟app端定义',
        `token_id` varchar(256) NOT NULL DEFAULT '0' COMMENT '唯一ID',
        `theme_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题图片',
        `theme_thumb_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题缩略图片',
        `medal_name` varchar(50) NOT NULL COMMENT '勋章名称',
        `theme_name` varchar(255) NOT NULL DEFAULT '' COMMENT '刻字',
        `material_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:钻石2:水晶3:金质4:银质5:铜质',
        `amount` varchar(10) NOT NULL DEFAULT '0' COMMENT '价格',
        `address` varchar(42) NOT NULL COMMENT '持有者地址',
        `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:铸造中 1:成功 2:失败',
        `addtime` int(11) NOT NULL COMMENT '添加时间',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='勋章表';
###勋章赠送记录表
    DROP TABLE IF EXISTS `ug_medal_give`;
    CREATE TABLE `ug_medal_give` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `medal_id` int(11) NOT NULL DEFAULT '0' COMMENT '勋章ID',
      `owner_address` varchar(42) NOT NULL  COMMENT '转增者地址',
      `recipient_address` varchar(42) NOT NULL DEFAULT ''  COMMENT '接收者地址',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:转赠中 1:成功 2:失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='勋章赠送记录表';
