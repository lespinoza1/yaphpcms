/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;

CREATE TABLE `tb_ssi` (
  `ssi_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `tpl_name` char(30) NOT NULL DEFAULT '' COMMENT '模板文件名，不包括后缀',
  `ssi_name` char(30) NOT NULL DEFAULT '' COMMENT '生成ssi文件名，不包括后缀',
  `last_build_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后生成时间',
  `sort_order` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序,越小越靠前',
  `memo` char(60) NOT NULL DEFAULT '' COMMENT '锁定备注',
  PRIMARY KEY (`ssi_id`),
  UNIQUE KEY `tpl_name` (`tpl_name`),
  UNIQUE KEY `ssi_name` (`ssi_name`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk COMMENT='tb_ssi服务器端包含表 by mashanling on 2013-05-13 15:17:18';

INSERT INTO `tb_ssi` VALUES (1,'msl','msl',0,1,'');
INSERT INTO `tb_ssi` VALUES (2,'m00s0l','msl0',0,0,'');
INSERT INTO `tb_ssi` VALUES (3,'footer','footer',1368438842,0,'网站底部');
INSERT INTO `tb_ssi` VALUES (4,'navbar','navbar',1368438842,0,'导航栏');
INSERT INTO `tb_ssi` VALUES (5,'hot_blogs','hot_blogs',1368438842,0,'热门网文');
INSERT INTO `tb_ssi` VALUES (6,'new_comments','new_comments',1368438842,0,'最新评论留言');
INSERT INTO `tb_ssi` VALUES (7,'tags','tags',1368438842,0,'标签云');
INSERT INTO `tb_ssi` VALUES (8,'page_not_found','404',1368438925,0,'404页面');

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
