﻿/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;


INSERT INTO `tb_field` VALUES (4,76,'安全设置','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','string','','sys_security_setting','安全设置',0,0,'',0);
INSERT INTO `tb_field` VALUES (7,43,'网站域名','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_base_domain','www.yaphpcms.com',1,0,'',1);
INSERT INTO `tb_field` VALUES (8,43,'网站根目录','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\'],\n    lang(\'END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','string\nvalidate_path','','sys_base_wwwroot','v0.1/',1,2,'',0);
INSERT INTO `tb_field` VALUES (9,43,'网站名称','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\', {size: 30})','string\nnotblank','','sys_base_web_title','yablog',1,3,'',1);
INSERT INTO `tb_field` VALUES (10,43,'网站首页标题','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\', {size: 60})','string\nnotblank','','sys_base_web_index_title','yablog首页',1,10,'',0);
INSERT INTO `tb_field` VALUES (11,43,'网站版权信息','extField.textarea(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\', {width: 700, height: 120})','raw\nnotblank','','sys_base_copyright','Copyright &copy; 2013 yablog 版权所有',1,99,'',0);
INSERT INTO `tb_field` VALUES (12,44,'seo关键字','extField.textarea(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\', {width: 850, height: 40})','string\nnotblank','','sys_seo_keyword','mrmsl',1,12,'',0);
INSERT INTO `tb_field` VALUES (13,43,'网站http协议','extField.textField(\'@input_name\', \'PLEASE_SELECT,%@field_name\',\'%@fieldLabel\', \'@value\', {size: 10,editable: false, store: [\n    [\'\', lang(\'PLEASE_SELECT\')],\n    [\'http\', \'http\'],\n    [\'https\', \'https\']\n]})','string\n#{%PLEASE_SELECT,@field_name}#MUST_VALIDATE#notblank\nhttp,https#{%@field_name,CAN_ONLY_BE,http OR https}#VALUE_VALIDATE#in','','sys_base_http_protocol','http',1,1,'',0);
INSERT INTO `tb_field` VALUES (15,43,'网站标题','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\', {size: 60})','string\nnotblank','','sys_base_web_name','yablog',1,9,'',1);
INSERT INTO `tb_field` VALUES (16,43,'js脚本url地址','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {width: 300}],\n    lang(\'END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','url\n#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#MUST_VALIDATE#notblank\nvalidate_path','','sys_base_js_url','http://imgcache.yaphpcms.com/v0.1/yablog/js/',1,16,'',1);
INSERT INTO `tb_field` VALUES (17,43,'css样式地址','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {width: 300}],\n    lang(\'END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','url\n#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#MUST_VALIDATE#notblank\nvalidate_path','','sys_base_css_url','http://imgcache.yaphpcms.com/v0.1/yablog/css/',1,17,'',1);
INSERT INTO `tb_field` VALUES (18,43,'js脚本路径','extField.fieldContainer([\'%@fieldLabel\', [\r\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\'],\r\n    lang(\'RELATIVE,WEBSITE,WWWROOT,%。,CAN_NOT,START_WITH\').replace(\'%s\',\'<span class=\"font-red\">/</span>\') + lang(\'%，,MUST,END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\r\n]])','string\nnotblank\nvalidate_dir','','sys_base_js_path','static/js/',1,11,'',0);
INSERT INTO `tb_field` VALUES (19,43,'css样式路径','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\'],\n    lang(\'RELATIVE,WEBSITE,WWWROOT,%。,CAN_NOT,START_WITH\').replace(\'%s\',\'<span class=\"font-red\">/</span>\') + lang(\'%，,MUST,END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','string\nnotblank\nvalidate_dir','','sys_base_css_path','static/css/',1,11,'',0);
INSERT INTO `tb_field` VALUES (20,43,'img图片url地址','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {width: 300}],\n    lang(\'END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','url\n#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#MUST_VALIDATE#notblank\nvalidate_path','','sys_base_img_url','http://imgcache.yaphpcms.com/v0.1/yablog/images/',1,20,'',1);
INSERT INTO `tb_field` VALUES (21,44,'seo描述','extField.textarea(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\', {width: 850, height: 60})','string\nnotblank','','sys_seo_description','seo描述',1,21,'',0);
INSERT INTO `tb_field` VALUES (22,43,'网站是否关闭','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\')','int','_getCheckboxValue','sys_base_closed','1',1,22,'',0);
INSERT INTO `tb_field` VALUES (23,43,'网站关闭原因','extField.textarea(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\', {width: 700, height: 120})','raw\nreturn','','sys_base_closed_reason','<div style=\"color: red\">网站关闭了</div>',1,23,'',0);
INSERT INTO `tb_field` VALUES (24,69,'标准时间相差','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\'],\n    lang(\'UNIT,%：,SECOND\')\n]])','int\nnotblank','','sys_timezone_timediff','28800',1,24,'',1);
INSERT INTO `tb_field` VALUES (25,69,'网站默认时区','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\')','string\nnotblank\n_timezone#{%@field_name,NOT_EXIST}#VALUE_VALIDATE#callback','','sys_timezone_default_timezone','asia/shanghai',1,25,'',0);
INSERT INTO `tb_field` VALUES (26,69,'长时间格式','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_timezone_datetime_format','Y-m-d H:i:s',1,26,'',1);
INSERT INTO `tb_field` VALUES (27,69,'日期格式','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_timezone_date_format','Y-m-d',1,27,'',1);
INSERT INTO `tb_field` VALUES (28,69,'时间格式','extField.textField(\'@input_name\', \'PLEASE_ENTER,%@field_name\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_timezone_time_format','H:i:s',1,28,'',1);
INSERT INTO `tb_field` VALUES (30,71,'记录日志级别','extField.fieldContainer(\'%@fieldLabel\',[\n    extField.checkbox(\'@input_name[]\', \'\', \'\', \'%\' + lang(\'INFO\') + TEXT.gray(lang(\'SYS_LOG_LEVEL_INFO\')), \'E_APP_INFO\'),\n    extField.checkbox(\'@input_name[]\', \'\', \'\', \'%\' + lang(\'DEBUG\') + TEXT.gray(lang(\'SYS_LOG_LEVEL_DEBUG\')), \'E_APP_DEBUG\'),\n    extField.checkbox(\'@input_name[]\', \'\', \'\', \'%SQL\' + TEXT.gray(lang(\'SYS_LOG_LEVEL_SQL\')), \'E_APP_SQL\'),\n    extField.checkbox(\'@input_name[]\', \'\', \'\', \'%\' + lang(\'ROLLBACK_SQL\') + TEXT.gray(lang(\'SYS_LOG_LEVEL_ROOLBACK_SQL\')), \'E_APP_ROLLBACK_SQL\')\n], true, {\nxtype: \'checkboxgroup\',\nvalue: \'@value\' ? {\'@input_name[]\': \'@value\'.split(\',\')} : false,\ncolumns: 1,\nvertical: true,\nname: \'@input_name\'\n})','_array,post,string\n#{%@field_name,DATA,INVALID}#EXISTS_VALIDATE#return','_getCheckboxValue#,','sys_log_level','E_APP_SQL,E_APP_ROLLBACK_SQL',1,30,'',0);
INSERT INTO `tb_field` VALUES (31,71,'记录慢查询','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {size: 4, minValue: 0, maxValue: 10}],\n    lang(\'UNIT,%：,SECOND,%。0,MEAN,NO,RECORD\')\n]])','int','','sys_log_slowquery','2',1,31,'',0);
INSERT INTO `tb_field` VALUES (32,77,'cookie域名','extField.fieldContainer([\'%@fieldLabel\', [\n    [null, \'@input_name\', \'\', \'\', \'@value\'],\n    \'@cookie=\' + System.sys_base_domain_scope\n], true])','string\nreturn','','sys_cookie_domain','@domain',1,32,'',1);
INSERT INTO `tb_field` VALUES (33,77,'cookie过期时间','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {minValue: 0}],\n    lang(\'UNIT,%：,SECOND,%。0,MEAN,WITH_BROWSER_PROCESS\')\n]])','int\nunsigned','','sys_cookie_expire','0',1,33,'',1);
INSERT INTO `tb_field` VALUES (34,77,'cookie路径','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','string\n#{%PLEASE_ENTER,@field_name}#MUST_VALIDATE#return','','sys_cookie_path','/',1,32,'',1);
INSERT INTO `tb_field` VALUES (35,77,'cookie前缀','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'\', \'\', \'@value\'],\n    lang(\'AVOID,CONFLICT\')\n], true])','string\nreturn','','sys_cookie_prefix','mrmsl',1,35,'',1);
INSERT INTO `tb_field` VALUES (36,71,'系统发生错误是否入库','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\', \'%\' + TEXT.gray(lang(\'FOR_EXAMPLE,%：,MODULE,NOT_EXIST,%。,SUGGEST,TURN_ON\')))','int','_getCheckboxValue','sys_log_systemerror','1',1,36,'',0);
INSERT INTO `tb_field` VALUES (37,71,'SQL查询错误是否入库','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\', \'%\' + TEXT.gray(lang(\'SUGGEST,TURN_ON\')))','int','_getCheckboxValue','sys_log_sqlerror','1',1,37,'',0);
INSERT INTO `tb_field` VALUES (38,71,'日志文件大小','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\',\'\', \'@value\', {minValue: 0}],\n    lang(\'UNIT,%：KB\')\n]])','int\nunsigned#10','','sys_log_filesize','1024',1,29,'',0);
INSERT INTO `tb_field` VALUES (39,70,'后台皮肤样式','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_template_admin_style','',1,39,'',0);
INSERT INTO `tb_field` VALUES (40,76,'session.save_handler','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\', \'PLEASE_SELECT,%@field_name\', \'\', \'@value\', {editable: false, size: 12, store: [\n        [\'files\', \'files\'],\n        [\'mysql\', \'mysql\'],\n        [\'memcache\', \'memcache\']\n    ]}],\n    lang(\'SESSION_SAVE_HANDLER_TIP\')\n]])','string\n#{%PLEASE_SELECT,@field_name}#MUST_VALIDATE#notblank\nfiles,mysql,files#{%@field_name,CAN_ONLY_BE,files or mysql or memcache}#VALUE_VALIDATE#in','','sys_session_save_handler','files',1,31,'',0);
INSERT INTO `tb_field` VALUES (41,76,'session.gc_maxlifetime','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {minValue: 0}],\n    lang(\'SESSION_LIFETIME_TIP\')\n]])','int\nunsigned#60','','sys_session_gc_maxlifetime','1800',1,31,'',0);
INSERT INTO `tb_field` VALUES (43,76,'session.save_path','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'\', \'\', \'@value\'],\n    lang(\'SESSION_SAVE_PATH_TIP\')\n], true])','string\nreturn\nvalidate_dir#SESSION_PATH|1|1','','sys_session_save_path','/',1,31,'',0);
INSERT INTO `tb_field` VALUES (44,76,'session.use_trans_id','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\',  \'%\' + TEXT.gray(lang(\'SESSION_USE_TRANS_ID_TIP\')))','int','_getCheckboxValue','sys_session_use_trans_id','0',1,31,'',0);
INSERT INTO `tb_field` VALUES (45,76,'session.use_cookies','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\',  \'%\' + TEXT.gray(lang(\'SESSION_USE_COOKIE_TIP\')))','int','_getCheckboxValue','sys_session_use_cookies','1',1,31,'',0);
INSERT INTO `tb_field` VALUES (46,76,'session.use_only_cookies','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\',  \'%\' + TEXT.gray(lang(\'SESSION_USE_ONLY_COOKIES_TIP\')))','int','_getCheckboxValue','sys_session_use_only_cookies','1',1,31,'',0);
INSERT INTO `tb_field` VALUES (47,76,'session.cookie_secure','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\',  \'%\' + TEXT.gray(lang(\'SESSION_COOKIE_SECURE_TIP\')))','int','_getCheckboxValue','sys_session_cookie_secure','0',1,31,'',0);
INSERT INTO `tb_field` VALUES (48,76,'session.cookie_httponly','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\', \'%\' + TEXT.gray(lang(\'SESSION_COOKIE_HTTPONLY_TIP\')))','int','_getCheckboxValue','sys_session_cookie_httponly','0',1,31,'',0);
INSERT INTO `tb_field` VALUES (49,76,'session.cookie_domain','extField.fieldContainer([\'%@fieldLabel\', [\n    [null, \'@input_name\', \'\', \'\', \'@value\'],\n    \'@cookie=\' + System.sys_base_domain_scope\n], true])','string\nreturn','','sys_session_cookie_domain','@domain',1,31,'',0);
INSERT INTO `tb_field` VALUES (50,76,'session.cookie_lifetime','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {minValue: 0}],\n    \'session cookie\' + lang(\'OVERDUE,TIME,UNIT,%：,SECOND,%。0,MEAN,WITH_BROWSER_PROCESS\')\n]])','int\nunsigned','','sys_session_cookie_lifetime','0',1,31,'',0);
INSERT INTO `tb_field` VALUES (51,76,'session.cookie_path','extField.fieldContainer([\'%@fieldLabel\', [\n    [null, \'@input_name\', \'\', \'\', \'@value\'],\n    (\'session cookie\' + lang(\'SAVE,PATH,%。,NOT_FILL,WILL,CN_QU,SYSTEM,DEFAULT,VALUE,%，,USUALLY,FOR\') + \'/\')\n], true])','string\nreturn','','sys_session_cookie_path','/',1,31,'',0);
INSERT INTO `tb_field` VALUES (52,76,'session前缀','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'\', \'\', \'@value\'],\n    lang(\'AVOID,CONFLICT\')\n], true])','string\nreturn','','sys_session_prefix','mrmsl',1,30,'',0);
INSERT INTO `tb_field` VALUES (53,76,'session.name','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'\', \'\', \'@value\'],\n    lang(\'SESSION_NAME_TIP\')\n], true])','string\nreturn\nenglish#{%@field_name,CAN_BUT,CN_YOU,LETTER,MAKEUP}#VALUE_VALIDATE','','sys_session_name','PHPSESSID',1,30,'',0);
INSERT INTO `tb_field` VALUES (54,75,'memcache服务器ip','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_memcache_host','127.0.0.1',1,54,'',0);
INSERT INTO `tb_field` VALUES (55,75,'memcache服务器端口','extField.numberField(\'@input_name\',\'\', \'%@fieldLabel\', \'@value\', {minValue:0})','int\nunsigned','','sys_memcache_port','11211',1,55,'',0);
INSERT INTO `tb_field` VALUES (56,75,'sphinx全文索引ip','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_sphinx_host','127.0.0.1',1,56,'',0);
INSERT INTO `tb_field` VALUES (57,75,'sphinx全文索引端口','extField.numberField(\'@input_name\',\'\', \'%@fieldLabel\', \'@value\', {minValue:0})','int\nunsigned','','sys_sphinx_port','3312',1,57,'',0);
INSERT INTO `tb_field` VALUES (58,78,'是否开启验证码','verifycode_enable','int','_getCheckboxValue','sys_verifycode_enable','1',1,58,'',0);
INSERT INTO `tb_field` VALUES (59,78,'验证码宽度','verifycode_width','int\nunsigned','','sys_verifycode_width','40',1,59,'',0);
INSERT INTO `tb_field` VALUES (60,78,'验证码高度','verifycode_height','int\nunsigned','','sys_verifycode_height','20',1,60,'',0);
INSERT INTO `tb_field` VALUES (61,78,'验证码字母长度','verifycode_length','int\nunsigned','','sys_verifycode_length','4',1,61,'',0);
INSERT INTO `tb_field` VALUES (62,78,'验证码类型','verifycode_type','int\nunsigned#-1','_getCheckboxValue','sys_verifycode_type','5',1,63,'',0);
INSERT INTO `tb_field` VALUES (63,80,'后台登陆及修改密码开启验证码','verifycode_enable@','int','_getCheckboxValue','module_admin_verifycode_enable','1',1,63,'',0);
INSERT INTO `tb_field` VALUES (64,80,'验证码宽度','verifycode_width@','int\nunsigned','','module_admin_verifycode_width','40',1,64,'',0);
INSERT INTO `tb_field` VALUES (65,80,'验证码高度','verifycode_height@','int\nunsigned','','module_admin_verifycode_height','20',1,65,'',0);
INSERT INTO `tb_field` VALUES (66,80,'验证码字母长度','verifycode_length@','int\nunsigned','','module_admin_verifycode_length','5',1,66,'',0);
INSERT INTO `tb_field` VALUES (67,80,'验证码类型','verifycode_type@','int\nunsigned#-2','_getCheckboxValue','module_admin_verifycode_type','5',1,68,'',0);
INSERT INTO `tb_field` VALUES (68,80,'验证码是否区分大小写','verifycode_case@','int','_getCheckboxValue','module_admin_verifycode_case','1',1,67,'',0);
INSERT INTO `tb_field` VALUES (69,80,'验证码顺序','verifycode_order@','string\nreturn','','module_admin_verifycode_order','4312',1,66,'',0);
INSERT INTO `tb_field` VALUES (70,78,'验证码顺序','verifycode_order','string\nreturn','','sys_verifycode_order','0',1,61,'',0);
INSERT INTO `tb_field` VALUES (71,78,'验证码是否区分大小写','verifycode_case','int\nunsigned','_getCheckboxValue','sys_verifycode_case','',1,62,'',0);
INSERT INTO `tb_field` VALUES (72,78,'验证码刷新次数限制','verifycode_refresh_limit','string\n_checkExplodeNumericFormat#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#VALUE_VALIDATE#callback','','sys_verifycode_refresh_limit','5/10',1,61,'',0);
INSERT INTO `tb_field` VALUES (73,78,'验证码错误次数限制','verifycode_error_limit','string\n_checkExplodeNumericFormat#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#VALUE_VALIDATE#callback','','sys_verifycode_error_limit','5/10',1,61,'',0);
INSERT INTO `tb_field` VALUES (74,80,'验证码刷新次数限制','verifycode_refresh_limit@','string\n_checkExplodeNumericFormat#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#VALUE_VALIDATE#callback','','module_admin_verifycode_refresh_limit','',1,66,'',0);
INSERT INTO `tb_field` VALUES (75,80,'验证码错误次数限制','verifycode_error_limit@','string\n_checkExplodeNumericFormat#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#VALUE_VALIDATE#callback','','module_admin_verifycode_error_limit','',1,66,'',0);
INSERT INTO `tb_field` VALUES (76,71,'事务回滚SQL是否入库','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\', \'%\' + TEXT.gray(lang(\'SUGGEST,TURN_ON\')))','int','_getCheckboxValue','sys_log_rollback_sql','1',1,38,'',0);
INSERT INTO `tb_field` VALUES (77,43,'imgcache common地址','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {width: 300}],\n    lang(\'END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','url\n#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#MUST_VALIDATE#notblank\nvalidate_path','','sys_base_common_imgcache','http://imgcache.yaphpcms.com/common/',1,15,'',1);
INSERT INTO `tb_field` VALUES (78,43,'后台imgcache地址','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {width: 300}],\n    lang(\'END_WITH\').replace(\'%s\',\'\"<span class=\"font-red\">/</span>\"\')\n]])','url\n#{%PLEASE_ENTER,CORRECT,FORMAT,CN_DE,@field_name}#MUST_VALIDATE#notblank\nvalidate_path','','sys_base_admin_imgcache','http://imgcache.yaphpcms.com/v0.1/admin/',1,15,'',1);
INSERT INTO `tb_field` VALUES (79,43,'后台管理入口','extField.fieldContainer([\'%@fieldLabel\', [\n    [null,\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\'],\n    lang(\'RELATIVE,WEBSITE,WWWROOT,OR\') + \'http://\' + lang(\'ABSOLUTE,ADDRESS\')\n]])','string\nnotblank','','sys_base_admin_entry','admin.php',1,2,'',0);
INSERT INTO `tb_field` VALUES (80,84,'留言是否需要审核','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\')','int','_getCheckboxValue','module_guestbook_check','1',1,22,'',0);
INSERT INTO `tb_field` VALUES (82,99,'评论是否需要审核','extField.checkbox(\'@input_name\',\'@value\', \'%@fieldLabel\')','int','_getCheckboxValue','module_comments_check','1',1,82,'',0);
INSERT INTO `tb_field` VALUES (83,100,'标题分割符','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','string\nnotblank','','sys_show_title_separator','|',1,83,'',0);
INSERT INTO `tb_field` VALUES (84,100,'面包屑分割符','extField.textField(\'@input_name\', \'\', \'%@fieldLabel\', \'@value\')','raw\nnotblank','','sys_show_bread_separator','&raquo;',1,83,'',0);
INSERT INTO `tb_field` VALUES (85,84,'留言最大回复层级','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {size: 4, minValue: 0, maxValue: 10}],\n    lang(\'ZERO_UN_LIMIT\')\n]])','int','','module_guestbook_max_reply_level','5',1,85,'',0);
INSERT INTO `tb_field` VALUES (86,99,'评论最大回复层级','extField.fieldContainer([\'%@fieldLabel\', [\n    [\'numberField\',\'@input_name\',\'PLEASE_ENTER,%@field_name\', \'\', \'@value\', {minValue: 0, maxValue: 10}],\n    lang(\'ZERO_UN_LIMIT\')\n]])','int','','module_comments_max_reply_level','5',1,85,'',0);

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
