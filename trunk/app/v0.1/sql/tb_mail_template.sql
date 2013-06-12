/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;


INSERT INTO `tb_mail_template` VALUES (1,'comments_at_email',1,1371047999,1371047999,'留言评论，有人回复时，发邮件通知','您的{$comment_name}已经有了回复','您在<a href=\"{BASE_SITE_URL}\" target=\"_blank\">{$sys_base_web_name}</a>的{$comment_name}已经有了回复,赶紧去<a href=\"{$link_url}\" target=\"_blank\">看看</a>吧<br />您的{$comment_name}如下：{$content}');
INSERT INTO `tb_mail_template` VALUES (2,'at_emailf',0,1370530428,1370530428,'mrmsl f','mrmslf','mrmslf');

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
