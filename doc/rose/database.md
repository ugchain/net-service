# 数据库
##玫瑰
###玫瑰表
    DROP TABLE IF EXISTS `ug_rose`;
    
    CREATE TABLE `ug_rose` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `theme_id` int(11) NOT NULL DEFAULT '1' COMMENT '主题ID,跟app端定义',
      `token_id` varchar(256) NOT NULL DEFAULT '' COMMENT '唯一ID',
      `theme_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题图片',
      `theme_thumb_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主题缩略图片',
      `rose_name` varchar(50) NOT NULL COMMENT '玫瑰名称',
      `theme_name` varchar(255) NOT NULL DEFAULT '' COMMENT '刻字',
      `material_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:钻石2:水晶3:金质4:银质5:铜质',
      `amount` varchar(10) NOT NULL DEFAULT '0' COMMENT '价格',
      `address` varchar(42) NOT NULL COMMENT '持有者地址',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:铸造中 1:成功 2:失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '转赠时间',
      PRIMARY KEY (`id`),
      UNIQUE KEY `token_id` (`token_id`),
      KEY `address` (`address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='勋章表';
###玫瑰赠送记录表
    DROP TABLE IF EXISTS `ug_rose_give`;
    
    CREATE TABLE `ug_rose_give` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `rose_id` int(11) NOT NULL DEFAULT '0' COMMENT '勋章ID',
      `from_address` varchar(42) NOT NULL COMMENT '转增者地址',
      `to_address` varchar(42) NOT NULL DEFAULT '' COMMENT '接收者地址',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:转赠中 1:成功 2:失败',
      `addtime` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`),
      KEY `rose_id` (`rose_id`),
      KEY `history_give` (`from_address`,`to_address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='玫瑰赠送记录表';
    
    
### 玫瑰主题表
    DROP TABLE IF EXISTS `ug_rose_theme`;
    CREATE TABLE `ug_rose_theme` (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `img` varchar(255) NOT NULL COMMENT '主题图片地址',
      `title` varchar(255) NOT NULL COMMENT '主题标题',
      `content` varchar(255) NOT NULL COMMENT '内容',
      `thumb_img` varchar(255) NOT NULL COMMENT '主题缩略图地址',
      `addtime` int(11) NOT NULL COMMENT '创建时间',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='玫瑰主题表';
    