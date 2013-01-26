<?php
/**
 * app_config.php           项目配置
 *
 * @author                  mrmsl <msl-138@163.com>
 * @date                    2012-12-24 11:42:44
 * @lastmodify              2013-01-22 17:40:21 by mrmsl
 */

//核心配置 by mrmsl on 2012-09-07 14:09:41
define('ADMIN_ID'              , 1);        //不可删除站长id
define('ADMIN_ROLE_ID'         , 1);        //不可删除，不可编辑权限站长角色id

define('ALLOW_AUTO_OPERATION_FUNCTION'  , ',time,get_client_ip,get_user_id,');//自动验证允许使用函数
define('ALLOW_AUTO_VALIDATE_FUNCTION'   , ',validate_dir,');                  //自动填充允许使用函数

//项目路径定义
define('APP_PATH'           , dir(__DIR__) . '/');//项目目录
define('SESSION_PATH'       , APP_PATH . 'sessions/');   //session保存目录
define('LOG_PATH'           , APP_PATH . 'logs/');       //日志目录
define('CACHE_PATH'         , APP_PATH . 'caches/');     //缓存目录
define('MODULE_CACHE_PATH'  , CACHE_PATH . 'modules/');     //系统模块信息缓存目录
define('LANG_PATH'          , APP_PATH . 'languages/');     //项目语言包目录
define('VIEW_PATH'          , APP_PATH . 'views/');         //模板目录
define('APP_FORWARD'        , 'APP_FORWARD');               //Yaf_Controller_Abstract::forward标识

//网站配置定义
define('SESSION_ADMIN_KEY'     , '__admin__');//管理员session key
define('WEB_DOMAIN'            , sys_config('sys_base_domain'));//网站域名
define('WEB_DOMAIN_SCOPE'      , sys_config('sys_base_domain_scope'));//域名作用域
//cookie domain
define('WEB_COOKIE_DOMAIN'     , '@domain' == ($cookie_domain = sys_config('sys_cookie_domain')) ? WEB_DOMAIN_SCOPE : $cookie_domain);
//session cookie domain
define('WEB_SESSION_COOKIE_DOMAIN' , '@domain' == ($session_cookie_domain = sys_config('sys_session_cookie_domain')) ? WEB_DOMAIN_SCOPE : $session_cookie_domain);
define('WEB_HTTP_PROTOCOL'     , sys_config('sys_base_http_protocol'));//http协议
define('SITE_URL'              , WEB_HTTP_PROTOCOL . '://' . WEB_DOMAIN);//网站网址，不以/结束
define('WEB_SITE_URL'          , SITE_URL . '/');//网站网址，以/结束
define('WEB_JS_PATH'           , WWWROOT . sys_config('sys_base_js_path'));//js物理路径
define('WEB_LANG_PATH'         , WEB_JS_PATH . 'lang/');//js语言包物理路径
define('WEB_CSS_PATH'          , WWWROOT . sys_config('sys_base_css_path'));//css物理路径
define('IMGCACHE_JS'           , sys_config('sys_base_js_url'));//js url
define('IMGCACHE_CSS'          , sys_config('sys_base_css_url'));//css url
define('IMGCACHE_IMG'          , sys_config('sys_base_img_url'));//img url
define('PAGE_SIZE'             , 20);//列表每页默认显示数
define('JSONP_CALLBACK'        , 'jsonpcallback');//jsonp 请求名称
define('DEFAULT_LANG'          , 'zh-cn');//默认语言

//session,cookie设置
define('SESSION_PREFIX'        , sys_config('sys_session_prefix'));  //session前缀
define('COOKIE_EXPIRE'         , sys_config('sys_cookie_expire'));   //过期时间
define('COOKIE_DOMAIN'         , WEB_COOKIE_DOMAIN);                 //作用域
define('COOKIE_PATH'           , sys_config('sys_cookie_path'));     //路径
define('COOKIE_PREFIX'         , sys_config('sys_cookie_prefix'));   //前缀,避免冲突

//自定义错误类型
define('E_APP_EXCEPTION'      , 'E_APP_EXCEPTION');//异常
define('E_APP_INFO'           , 'E_APP_INFO');     //信息
define('E_APP_DEBUG'          , 'E_APP_DEBUG');    //调试
define('E_APP_SQL'            , 'E_APP_SQL');      //SQL
define('E_APP_ROLLBACK_SQL'   , 'E_APP_ROLLBACK_SQL');      //事务回滚SQL

//日志类型
define('LOG_TYPE_ALL'                 , -1);//所有日志
define('LOG_TYPE_SQL_ERROR'           , 0); //sql错误
define('LOG_TYPE_SYSTEM_ERROR'        , 1); //系统错误
define('LOG_TYPE_ADMIN_OPERATE'       , 2); //管理员操作日志
define('LOG_TYPE_NO_PERMISSION'       , 3); //无权限操作
define('LOG_TYPE_ADMIN_LOGIN_INFO'    , 4); //后台登陆日志
define('LOG_TYPE_INVALID_PARAM'       , 5); //非法参数
define('LOG_TYPE_CRONTAB'             , 6); //定时任务
define('LOG_TYPE_VALIDATE_FORM_ERROR' , 7); //验证表单错误
define('LOG_TYPE_VERIFYCODE_ERROR'    , 8); //验证码错误
define('LOG_TYPE_LOAD_SCRIPT_TIME'    , 9); //css及js加载时间
define('LOG_TYPE_SLOWQUERY'           , 10);//慢查询
define('LOG_TYPE_ROLLBACK_SQL'        , 11);//事务回滚sql

//状态码
define('HTTP_STATUS_UNLOGIN'          , 401);//未登陆
define('HTTP_STATUS_NO_PRIV'          , 403);//没有权限
define('HTTP_STATUS_SERVER_ERROR'     , 500);//服务器错误

//验证码类型
define('VERIFY_CODE_TYPE_LETTERS'       , 0);//大小写字母(a-zA-Z)
define('VERIFY_CODE_TYPE_LETTERS_UPPER' , 1);//大写字母(A-Z)
define('VERIFY_CODE_TYPE_LETTERS_LOWER' , 2);//小写字母(a-z)
define('VERIFY_CODE_TYPE_NUMERIC'       , 3);//数字(0-9)');
define('VERIFY_CODE_TYPE_ALPHANUMERIC'  , 4);//字母与数字(a-xA-Z0-9)
define('VERIFY_CODE_TYPE_ALPHANUMERIC_EXTEND'  , 5);//字母与数字(a-xA-Z0-9)，排除容易混淆的字符oOLl和数字01


//杂项定义
define('REQUEST_TIME_MICRO'    , microtime(true));          //开始执行时间
define('EOL_CR'                , "\r");                       //回车
define('EOL_LF'                , "\n");                       //换行
define('EOL_CRLF'              , EOL_CR . EOL_LF);            //回车换行
define('SESSION_VERIFY_CODE'   , 'verify_code');//验证码session key值
define('AUTO_CREATE_COMMENT'   , "//后台自动生成，请毋修改\n//最后更新时间:%s" . EOL_LF);//后台生成缓存文件注释说明
define('__GET'                 , isset($_GET['__get']) && APP_DEBUG);//调试模式下，通过$_GET获取_POST数据