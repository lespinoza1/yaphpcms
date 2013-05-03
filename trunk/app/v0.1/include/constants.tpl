<?php
/**
 * 后台修改网站基本信息，项目常量定义模板
 * @AUTO_CREATE_COMMENT
 *
 * @file            constants.tpl
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-03 13:35:52
 * @lastmodify      @@lastmodify
 */

//网站配置定义
define('WEB_DOMAIN'            , sys_config('sys_base_domain'));//网站域名
define('WEB_DOMAIN_SCOPE'      , sys_config('sys_base_domain_scope'));//域名作用域
define('WEB_COOKIE_DOMAIN'     , @WEB_COOKIE_DOMAIN);//cookie domain
define('WEB_SESSION_COOKIE_DOMAIN' , @WEB_SESSION_COOKIE_DOMAIN);//session cookie domain
define('WEB_HTTP_PROTOCOL'     , sys_config('sys_base_http_protocol'));//http协议
define('WEB_BASE_PATH'         , sys_config('sys_base_wwwroot'));////网站相对根目录
define('SITE_URL'              , WEB_HTTP_PROTOCOL . '://' . WEB_DOMAIN);//网站网址，不以/结束
define('WEB_SITE_URL'          , SITE_URL . '/');//网站网址，以/结束
define('BASE_SITE_URL'         , WEB_SITE_URL . WEB_BASE_PATH);//网站网址，包括网站根目录
define('WEB_ADMIN_ENTRY'       , @WEB_ADMIN_ENTRY);//管理员入口
define('WEB_JS_PATH'           , WWWROOT . sys_config('sys_base_js_path'));//js物理路径
define('WEB_JS_LANG_PATH'      , WEB_JS_PATH . 'lang/');//js语言包物理路径
define('WEB_CSS_PATH'          , WWWROOT . sys_config('sys_base_css_path'));//css物理路径
define('COMMON_IMGCACHE'       , sys_config('sys_base_common_imgcache'));//imgcache
define('ADMIN_IMGCACHE'        , sys_config('sys_base_admin_imgcache'));//后台imgcache
define('IMGCACHE_JS'           , sys_config('sys_base_js_url'));//js url
define('IMGCACHE_CSS'          , sys_config('sys_base_css_url'));//css url
define('IMGCACHE_IMG'          , sys_config('sys_base_img_url'));//img url

//session,cookie设置
define('SESSION_PREFIX'        , sys_config('sys_session_prefix'));  //session前缀
define('COOKIE_EXPIRE'         , sys_config('sys_cookie_expire'));   //过期时间
define('COOKIE_DOMAIN'         , WEB_COOKIE_DOMAIN);                 //作用域
define('COOKIE_PATH'           , sys_config('sys_cookie_path'));     //路径
define('COOKIE_PREFIX'         , sys_config('sys_cookie_prefix'));   //前缀,避免冲突