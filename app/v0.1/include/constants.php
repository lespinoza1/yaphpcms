<?php
/**
 * 后台修改网站基本信息，项目常量定义模板
 * 后台自动生成，请毋修改。最后更新时间: 2013-05-03 15:43:26
 *
 * @file            constants.tpl
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-03 13:35:52
 * @lastmodify      2013-05-03 15:43:26
 */

//网站配置定义
define('WEB_DOMAIN'            , 'www.yaphpcms.com');//网站域名
define('WEB_DOMAIN_SCOPE'      , '.yaphpcms.com');//域名作用域
define('WEB_COOKIE_DOMAIN'     , WEB_DOMAIN_SCOPE);//cookie domain
define('WEB_SESSION_COOKIE_DOMAIN' , WEB_DOMAIN_SCOPE);//session cookie domain
define('WEB_HTTP_PROTOCOL'     , 'http');//http协议
define('WEB_BASE_PATH'         , 'v0.1/');////网站相对根目录
define('SITE_URL'              , WEB_HTTP_PROTOCOL . '://' . WEB_DOMAIN);//网站网址，不以/结束
define('WEB_SITE_URL'          , SITE_URL . '/');//网站网址，以/结束
define('BASE_SITE_URL'         , WEB_SITE_URL . WEB_BASE_PATH);//网站网址，包括网站根目录
define('WEB_ADMIN_ENTRY'       , BASE_SITE_URL . 'admin.php');//管理员入口
define('WEB_JS_PATH'           , WWWROOT . 'static/js/');//js物理路径
define('WEB_JS_LANG_PATH'      , WEB_JS_PATH . 'lang/');//js语言包物理路径
define('WEB_CSS_PATH'          , WWWROOT . 'static/css/');//css物理路径
define('COMMON_IMGCACHE'       , 'http://imgcache.yaphpcms.com/common/');//imgcache
define('ADMIN_IMGCACHE'        , 'http://imgcache.yaphpcms.com/v0.1/admin/');//后台imgcache
define('IMGCACHE_JS'           , 'http://imgcache.yaphpcms.com/v0.1/yablog/js/');//js url
define('IMGCACHE_CSS'          , 'http://imgcache.yaphpcms.com/v0.1/yablog/css/');//css url
define('IMGCACHE_IMG'          , 'http://imgcache.yaphpcms.com/v0.1/yablog/images/');//img url

//session,cookie设置
define('SESSION_PREFIX'        , 'mrmsl');  //session前缀
define('COOKIE_EXPIRE'         , '0');   //过期时间
define('COOKIE_DOMAIN'         , WEB_COOKIE_DOMAIN);                 //作用域
define('COOKIE_PATH'           , '/');     //路径
define('COOKIE_PREFIX'         , 'mrmsl');   //前缀,避免冲突