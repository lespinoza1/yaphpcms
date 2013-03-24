/**
 * 数据库结构
 *
 * @file            db_yaphpcms_v0.1_structure.sql
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-17 13:58:23
 * @lastmodify      $Date$ $Author$
 */


/*
DROP DATABASE IF EXISTS `db_yaphpcms_v0.1`;
CREATE DATABASE `db_yaphpcms_v0.1` DEFAULT CHARACTER SET gbk;
*/
USE `db_yaphpcms_v0.1`;

/*tb_admin管理员表*/
CREATE TABLE `tb_admin` (
  `admin_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '所属角色id',
  `username` char(15) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `realname` char(30) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登陆时间',
  `last_login_ip` char(15) NOT NULL DEFAULT '' COMMENT '最后登陆ip地址',
  `login_num` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `is_restrict` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否限制登陆地点',
  `mac_address` char(20) NOT NULL DEFAULT '' COMMENT '用户网卡信息',
  `lock_start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '锁定开始时间',
  `lock_end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '锁定结束时间',
  `lock_memo` char(60) NOT NULL DEFAULT '' COMMENT '锁定备注',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='系统管理员表 by mashanling on 2012-12-27 11:28:17';

/*tb_admin_login_history管理员登陆历史表*/
CREATE TABLE `tb_admin_login_history` (
  `login_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆时间',
  `login_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆ip,ip2long',
  PRIMARY KEY (`login_id`),
  KEY `admin_id` (`admin_id`),
  KEY `login_time` (`login_time`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='系统管理员登陆历史表 by mashanling on 2012-12-27 11:30:35';

/*tb_admin_role系统管理角色权限表*/
DROP TABLE IF EXISTS `tb_admin_role`;
CREATE TABLE `tb_admin_role` (
  `role_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `role_name` char(30) NOT NULL DEFAULT '' COMMENT '系统组名称',
  `memo` char(60) NOT NULL DEFAULT '' COMMENT '备注',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='系统管理角色表 by mashanling on 2012-12-27 11:31:22';

/*tb_admin_role_priv系统管理角色权限表*/
CREATE TABLE `tb_admin_role_priv` (
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `menu_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '菜单id',
  KEY `role_id` (`role_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='系统管理角色权限表 by mashanling on 2012-12-27 11:34:47';

/*tb_area国家地区表*/
CREATE TABLE `tb_area` (
  `area_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `area_name` char(50) NOT NULL DEFAULT '' COMMENT '地区名称',
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `area_code` char(15) NOT NULL DEFAULT '' COMMENT '地区简码,代号',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort_order` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `node` char(20) NOT NULL DEFAULT '' COMMENT '节点',
  PRIMARY KEY (`area_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='国家地区表 by mashanling on 2012-12-27 11:35:41';

/*tb_blog博客表*/
CREATE TABLE `tb_blog` (
  `blog_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '标题',
  `cate_id` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
   update_time int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
   status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0;未发布;1已发布;2已删除',
  `sort_order` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序，越小越靠前。默认其id',
  `hits` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  seo_keyword varchar(180) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  seo_description varchar(300) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY(`title`),
  FOREIGN KEY (`cate_id`) REFERENCES `tb_category` (`cate_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='博客表 by mashanling on 2013-03-22 15:56:41';

/*tb_blog_comments博客评论表*/
CREATE TABLE `tb_blog_comments` (
  `blog_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '微博id',
  `comment_id` smallint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'tb_comments comment_id',
  FOREIGN KEY (`comment_id`) REFERENCES `tb_comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`blog_id`) REFERENCES `tb_blog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='博客评论表 by mashanling on 2013-03-22 17:34:52';

/*tb_category博客分类表*/
CREATE TABLE `tb_category` (
  `cate_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `cate_name` varchar(30) NOT NULL DEFAULT '' COMMENT '分类名称',
  `en_name` varchar(15) NOT NULL DEFAULT '' COMMENT 'url英文名',
  `parent_id` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1显示;0不显示。默认1',
  `sort_order` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序，越小越靠前。默认其id',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `node` varchar(20) NOT NULL DEFAULT '' COMMENT '节点',
  seo_keyword varchar(180) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  seo_description varchar(300) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  PRIMARY KEY (`cate_id`),
  UNIQUE KEY(`cate_name`),
  UNIQUE KEY(`en_name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='博客分类表 by mashanling on 2013-03-18 15:09:25';

/*tb_comments留言评论表*/
CREATE TABLE `tb_comments` (
  `comment_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ip,ip2long',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
   last_reply_time int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后回复时间',
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0管理员;1用户.默认0',
   status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0;未处理;1已通过;2未通过',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `node` varchar(24) NOT NULL DEFAULT '' COMMENT '节点',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`comment_id`),
  KEY (parent_id),
  KEY (status),
  KEY (`last_reply_time`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='留言评论表 by mashanling on 2013-02-27 11:48:16';

/*tb_field表单域表*/
CREATE TABLE `tb_field` (
  `field_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `menu_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '所属菜单',
  `field_name` varchar(50) NOT NULL DEFAULT '' COMMENT '字段名',
  `field_code` text NOT NULL COMMENT '字段js代码',
  `validate_rule` varchar(300) NOT NULL DEFAULT 'string' COMMENT '输入框值 by mashanling on 2012-08-31 15:26:18',
  `auto_operation` varchar(300) NOT NULL DEFAULT '' COMMENT '输入框值 by mashanling on 2012-09-07 12:37:32',
  `input_name` varchar(50) NOT NULL DEFAULT '' COMMENT '输入框名称',
  `input_value` text NOT NULL COMMENT '输入框值 by mashanling on 2012-08-29 16:28:49',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用;0,不启用;1启用',
  `sort_order` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序,排序越小越靠前',
  `memo` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `customize_1` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自定义字段1(系统设置:0,不写js;1则写) by mashanling on 2012-09-04 18:07:32',
  PRIMARY KEY (`field_id`),
  UNIQUE KEY `menu_id` (`menu_id`,`input_name`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='表单域表 by mashanling on 2012-12-27 11:37:21';

/*tb_guestbook留言表*/
CREATE TABLE `tb_guestbook` (
  `comment_id` smallint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'tb_comments comment_id'
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='留言表 by mashanling on 2013-02-26 16:02:11';

/*tb_log系统日志表*/
CREATE TABLE `tb_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `content` text NOT NULL COMMENT '日志内容',
  `log_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '日志类型,0:sql错误;1:系统错误;2:管理员操作日志;3:无权限操作;4:后台登陆日志;5:非法参数;6:定时任务',
  `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'sql时间',
  `page_url` varchar(300) NOT NULL DEFAULT '' COMMENT '日志页面',
  `referer_url` varchar(300) NOT NULL DEFAULT '' COMMENT '来路页面',
  `user_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ip,ip2long',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `admin_name` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员姓名',
  PRIMARY KEY (`log_id`),
  KEY `log_type` (`log_type`,`log_time`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='系统日志表 by mashanling on 2012-12-27 11:38:36';

/*tb_menu菜单表*/
CREATE TABLE `tb_menu` (
  `menu_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `menu_name` char(30) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `controller` char(20) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` char(20) NOT NULL DEFAULT '' COMMENT '操作',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort_order` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `node` char(20) NOT NULL DEFAULT '' COMMENT '节点',
  `memo` char(60) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`menu_id`),
  KEY `controller` (`controller`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='菜单表 by mashanling on 2012-12-27 12:44:04';

/*tb_miniblog微博表*/
CREATE TABLE `tb_miniblog` (
  `blog_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `hits` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='微博表 by mashanling on 2013-03-22 17:10:34';

/*tb_miniblog_comments微博评论表*/
CREATE TABLE `tb_miniblog_comments` (
  `blog_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '微博id',
  `comment_id` smallint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'tb_comments comment_id',
  FOREIGN KEY (`comment_id`) REFERENCES `tb_comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`blog_id`) REFERENCES `tb_miniblog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='微博评论表 by mashanling on 2013-02-26 16:02:11';

/*tb_session session表*/
CREATE TABLE `tb_session` (
  `session_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'session id',
  `data` mediumtext NOT NULL COMMENT 'session数据',
  `controller` varchar(20) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(20) NOT NULL DEFAULT '' COMMENT '操作方法',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后活跃时间',
  `page_url` varchar(300) NOT NULL DEFAULT '' COMMENT '日志页面',
  `referer_url` varchar(300) NOT NULL DEFAULT '' COMMENT '来路页面',
  `user_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `user_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ip',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  PRIMARY KEY (`session_id`),
  KEY `last_time` (`last_time`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='session管理表 by mashanling on 2012-09-18 14:50:30';

/*tb_tags标签表*/
CREATE TABLE `tb_tag` (
  `tag_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `blog_id` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '博客id',
  tag char(20) NOT NULL DEFAULT '' COMMENT '标签',
  PRIMARY KEY(tag_id),
  FOREIGN KEY (`blog_id`) REFERENCES `tb_blog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='标签表 by mashanling on 2013-03-22 17:07:22';


/*外键约束*/

/*管理员表role_id系统角色id*/
ALTER TABLE `tb_admin`
ADD CONSTRAINT `tb_admin_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `tb_admin_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*管理员登陆历史表admin_id管理员id*/
ALTER TABLE `tb_admin_login_history`
ADD CONSTRAINT `tb_admin_login_history_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tb_admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*系统角色权限表role_id系统角色id及menu_id菜单id*/
ALTER TABLE `tb_admin_role_priv`
ADD CONSTRAINT `tb_admin_role_priv_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `tb_admin_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tb_admin_role_priv_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `tb_menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*表单域表menu_id菜单id*/
ALTER TABLE `tb_field`
ADD CONSTRAINT `tb_field_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `tb_menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*留言表comment_id 留言评论id*/
ALTER TABLE `tb_guestbook`
ADD CONSTRAINT `tb_guestbook_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `tb_comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE;