<?php
/**
 * 数据库配置
 *
 * @file            db_config.php
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-12-24 14:57:15
 * @lastmodify      $Date$ $Author$
 */

define('DB_TYPE'               , 'pdo');          //数据库类型
define('DB_HOST'               , 'localhost');    //数据库主机名
define('DB_PORT'               , '');             //数据库端口
define('DB_NAME'               , 'db_yaphpcms');  //数据库名
define('DB_USER'               , 'root');         //数据库用户名
define('DB_PWD'                , '');             //数据库密码
define('DB_PREFIX'             , 'tb_');          //数据表前缀
define('DB_CHARSET'            , 'utf8');         //数据库编码
define('DB_DSN'                , 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME);//DSN

define('TB_PREFIX'             , DB_PREFIX);                    //数据表前缀
define('TB_ADMIN'              , TB_PREFIX . 'admin');          //管理员表
define('TB_ADMIN_LOGIN_HISTORY', TB_PREFIX . 'admin_login_history');     //管理员登陆历史表 by mrmsl on 2012-06-30 09:52:54
define('TB_ADMIN_ROLE'         , TB_PREFIX . 'admin_role');     //管理员角色表
define('TB_ADMIN_ROLE_PRIV'    , TB_PREFIX . 'admin_role_priv');//管理员角色权限表
define('TB_AREA'               , TB_PREFIX . 'area');           //国家地区表
define('TB_MENU'               , TB_PREFIX . 'menu');           //后台菜单表 by mrmsl on 21:59 2012-7-18
define('TB_LOG'                , TB_PREFIX . 'log');            //系统日志表
define('TB_FIELD'              , TB_PREFIX . 'field');          //表单域表 by mrmsl on 2012-08-01 17:15:55
define('TB_SESSION'            , TB_PREFIX . 'session');        //session表 by mrmsl on 2012-09-18 16:16:56