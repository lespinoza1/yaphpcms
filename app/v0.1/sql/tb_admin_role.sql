/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;

DROP TABLE IF EXISTS `tb_admin_role`;
CREATE TABLE `tb_admin_role` (
  `role_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `role_name` char(30) NOT NULL DEFAULT '' COMMENT '系统组名称',
  `memo` char(60) NOT NULL DEFAULT '' COMMENT '备注',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=gbk COMMENT='系统管理角色表 by mashanling on 2012-12-27 11:31:22';

INSERT INTO `tb_admin_role` VALUES (1,'站长','最高权限',0);
INSERT INTO `tb_admin_role` VALUES (3,'录入员','部分模块权限',0);
INSERT INTO `tb_admin_role` VALUES (5,'售后专员','',0);
INSERT INTO `tb_admin_role` VALUES (6,'维护员2','维护员',0);
INSERT INTO `tb_admin_role` VALUES (7,'信息管理员','',0);
INSERT INTO `tb_admin_role` VALUES (8,'运营管理员','',0);
INSERT INTO `tb_admin_role` VALUES (9,'高级信息管理','李小江',0);
INSERT INTO `tb_admin_role` VALUES (11,'SEO信息分析','',0);
INSERT INTO `tb_admin_role` VALUES (13,'affiliate 管理','',0);
INSERT INTO `tb_admin_role` VALUES (14,'产品评论与咨询','',0);
INSERT INTO `tb_admin_role` VALUES (15,'产品统计员','产品销售统计',0);
INSERT INTO `tb_admin_role` VALUES (16,'财务管理','地区配送方式设置',0);
INSERT INTO `tb_admin_role` VALUES (17,'产品管理','',0);
INSERT INTO `tb_admin_role` VALUES (18,'会员管理人','主要针对设置VIP及修改密码',0);
INSERT INTO `tb_admin_role` VALUES (19,'facebook专员','',0);
INSERT INTO `tb_admin_role` VALUES (30,'mrmsl','',30);

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
