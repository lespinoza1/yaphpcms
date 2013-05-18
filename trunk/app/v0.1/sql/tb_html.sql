/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;

DROP TABLE IF EXISTS `tb_html`;
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

INSERT INTO `tb_html` VALUES (3,'ssi/footer','ssi/footer',1368858603,0,'ssi网站底部');
INSERT INTO `tb_html` VALUES (4,'ssi/navbar','ssi/navbar',1368858603,0,'ssi导航栏');
INSERT INTO `tb_html` VALUES (5,'ssi/hot_blogs','ssi/hot_blogs',1368858603,0,'ssi热门网文');
INSERT INTO `tb_html` VALUES (6,'ssi/new_comments','ssi/new_comments',1368858603,0,'ssi最新评论留言');
INSERT INTO `tb_html` VALUES (7,'ssi/tags','ssi/tags',1368858603,0,'ssi标签云');
INSERT INTO `tb_html` VALUES (8,'html/page_not_found','404',1368858603,0,'404页面');
INSERT INTO `tb_html` VALUES (9,'html/msg','msg',1368858603,0,'系统提示页面');
INSERT INTO `tb_html` VALUES (10,'index/index','index',0,10,'首页');
INSERT INTO `tb_html` VALUES (11,'category/index','category',0,11,'网文首页');
INSERT INTO `tb_html` VALUES (12,'guestbook/index','guestbook',0,12,'留言首页');
INSERT INTO `tb_html` VALUES (13,'miniblog/index','miniblog',0,13,'微博首页');

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
