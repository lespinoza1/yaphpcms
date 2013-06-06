﻿/**
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

/*tb_blog博客表
ALTER TABLE tb_blog
MODIFY status is_issue tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0;未发布;1已发布',
ADD COLUMN is_delete tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0未删除;1已删除' AFTER is_issue,
ADD COLUMN from_name varchar(200) NOT NULL DEFAULT '' COMMENT '来源名称',
ADD COLUMN from_url varchar(200) NOT NULL DEFAULT '' COMMENT '来源url',
ADD COLUMN link_url varchar(150) NOT NULL DEFAULT '' COMMENT '博客链接',
ADD COLUMN summary text COMMENT '摘要'
ADD INDEX issue_delete(is_issue, is_delete)
ADD COLUMN `diggs` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顶数' AFTER hits
ADD COLUMN `total_comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '总评论数' AFTER comments;
*/

CREATE TABLE `tb_blog` (
  `blog_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` varchar(90) NOT NULL DEFAULT '' COMMENT '标题',
  `cate_id` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
   update_time int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
   is_issue tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0;未发布;1已发布',
   is_delete tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0未删除;1已删除',
  `sort_order` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序，越小越靠前。默认其id',
  `hits` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `diggs` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顶数'
  `comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `total_comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '总评论数' AFTER comments,
   seo_keyword varchar(180) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
   seo_description varchar(300) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `content` text COMMENT '内容',
  summary text COMMENT '摘要',
  from_name varchar(200) NOT NULL DEFAULT '' COMMENT '来源名称',
  from_url varchar(200) NOT NULL DEFAULT '' COMMENT '来源url',
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY(`title`),
  FOREIGN KEY (`cate_id`) REFERENCES `tb_category` (`cate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY issue_delete(is_issue, is_delete)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='博客表 by mashanling on 2013-03-22 15:56:41';

/*tb_category博客分类表
ALTER TABLE tb_category
ADD COLUMN link_url varchar(150) NOT NULL DEFAULT '' COMMENT '分类链接'
*/
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
  link_url varchar(150) NOT NULL DEFAULT '' COMMENT '分类链接',
  PRIMARY KEY (`cate_id`),
  UNIQUE KEY(`cate_name`),
  UNIQUE KEY(`en_name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='博客分类表 by mashanling on 2013-03-18 15:09:25';

/*tb_comments留言评论表
ALTER TABLE tb_comments
ADD COLUMN user_homepage varchar(50) NOT NULL DEFAULT '' COMMENT '用户主页url' AFTER user_ip
ADD COLUMN user_pic varchar(50) NOT NULL DEFAULT '' COMMENT '用户头像url' AFTER user_ip,
ADD COLUMN `blog_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '博客id 或者 微博id',
ADD COLUMN type tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0留言;1博客评论;2微博评论.默认0',
ADD COLUMN email varchar(50) NOT NULL DEFAULT '' COMMENT '用户email' AFTER username,
ADD COLUMN at_email tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '有回复时是否发送emil;0否;1是'
ADD COLUMN province varchar(20) NOT NULL DEFAULT '' COMMENT '用户省份' AFTER user_ip,
ADD COLUMN city varchar(20) NOT NULL DEFAULT '' COMMENT '用户城市' AFTER province,
ADD COLUMN real_parent_id smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '回复超出最大回复层级时,实际回复id' AFTER parent_id
DROP INDEX status,
ADD INDEX(blog_id),
ADD INDEX(type,status),
ADD INDEX(node)
*/
CREATE TABLE `tb_comments` (
  `comment_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  real_parent_id smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '回复超出最大回复层级时,实际回复id',
 type tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0留言;1博客评论;2微博评论.默认0'
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  email varchar(50) NOT NULL DEFAULT '' COMMENT '用户email'
  `user_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ip,ip2long',
  province varchar(20) NOT NULL DEFAULT '' COMMENT '用户省份',
  city varchar(20) NOT NULL DEFAULT '' COMMENT '用户城市'
  user_homepage varchar(50) NOT NULL DEFAULT '' COMMENT '用户主页url',
  user_pic varchar(50) NOT NULL DEFAULT '' COMMENT '用户头像url',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
   last_reply_time int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后回复时间',
  `admin_reply_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '管理员回复类型,0默认,1已经回复,2属于管理员回复',
   status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态;0;未处理;1已通过;2未通过',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `node` varchar(24) NOT NULL DEFAULT '' COMMENT '节点',
  `content` text NOT NULL COMMENT '内容',
`blog_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '博客id 或者 微博id',
at_email tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '有回复时是否发送emil;0否;1是;2已经发送',
  PRIMARY KEY (`comment_id`),
  KEY `parent_id` (`parent_id`),
  KEY `last_reply_time` (`last_reply_time`),
  KEY `type` (`type`,`status`),
  KEY `blog_id` (`blog_id`)
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

/*tb_html 生成静态页管理表*/
CREATE TABLE `tb_html` (
  `html_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `tpl_name` char(30) NOT NULL DEFAULT '' COMMENT '模板文件名，相对前台模板路径,格式: 目录/模板文件名,不包括后缀',
  `html_name` char(30) NOT NULL DEFAULT '' COMMENT '生成静态页文件名，相对网站根目录,不包括后缀',
  `last_build_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后生成时间',
  `sort_order` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序,越小越靠前',
  `memo` char(60) NOT NULL DEFAULT '' COMMENT '锁定备注',
  PRIMARY KEY (`html_id`),
  UNIQUE KEY `tpl_name` (`tpl_name`),
  UNIQUE KEY `html_name` (`html_name`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='生成静态页管理表 by mashanling on 2013-05-18 09:39:52';

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

/*tb_mail_template邮件模板表*/
CREATE TABLE `tb_mail_template` (
  `template_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `template_name` varchar(20) NOT NULL DEFAULT '' COMMENT '',
  `sort_order` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序,越小越靠前',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登陆时间',
  `memo` varchar(60) NOT NULL DEFAULT '' COMMENT '备注',
  `subject` varchar(150) NOT NULL DEFAULT '' COMMENT '',
  `content` text NOT NULL COMMENT '',
  PRIMARY KEY (`template_id`),
  UNIQUE KEY (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='邮件模板表 by mashanling on 2013-06-06 10:00:44';

/*tb_mail_history邮件发送历史表*/
CREATE TABLE `tb_mail_history` (
  `history_id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `template_id` tinyint(2) unsigned NOT NULL DEFAULT 0 COMMENT '邮件模板id',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '发送邮箱',
  `subject` varchar(150) NOT NULL DEFAULT '' COMMENT '邮件主题',
  `content` text NOT NULL COMMENT '邮件内容',
  PRIMARY KEY (`history_id`),
  KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='邮件模板表 by mashanling on 2013-06-06 11:00:50';

/*tb_mail_template邮件模板表*/
CREATE TABLE `tb_mail_template` (
  `template_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `template_name` varchar(20) NOT NULL DEFAULT '' COMMENT '模板名称',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `memo` varchar(60) NOT NULL DEFAULT '' COMMENT '备注',
  `subject` varchar(150) NOT NULL DEFAULT '' COMMENT '邮件主题',
  `content` text NOT NULL COMMENT '模板内容',
  PRIMARY KEY (`template_id`),
  UNIQUE KEY (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='邮件模板表 by mashanling on 2013-06-06 10:00:44';

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

/*tb_miniblog微博表
ALTER TABLE tb_miniblog
ADD COLUMN link_url varchar(100) NOT NULL DEFAULT '' COMMENT '微博链接'
ADD COLUMN `diggs` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顶数' AFTER hits
ADD COLUMN `total_comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '总评论数' AFTER comments;
*/
CREATE TABLE `tb_miniblog` (
  `blog_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `hits` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `diggs` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顶数'
  `comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `total_comments` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '总评论数' AFTER comments,
  `content` text NOT NULL COMMENT '内容',
  link_url varchar(100) NOT NULL DEFAULT '' COMMENT '微博链接'
  PRIMARY KEY (`blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='微博表 by mashanling on 2013-03-22 17:10:34';

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

/*tb_tag标签表
ALTER TABLE tb_tag
ADD COLUMN `searches` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '搜索次数'
*/
CREATE TABLE `tb_tag` (
  `blog_id` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '博客id',
  tag char(20) NOT NULL DEFAULT '' COMMENT '标签',
  `searches` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '搜索次数',
  PRIMARY KEY(tag,blog_id),
  KEY(searches),
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