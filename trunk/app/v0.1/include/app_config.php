<?php
/**
 * 项目配置
 *
 * @file            app_config.php
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-12-24 11:42:44
 * @lastmodify      $Date$ $Author$
 */

//use Yap\Func;

//核心配置
define('DS'                 , '/');      //路径分割符

define('ADMIN_ID'           , 1);        //不可删除站长id
define('ADMIN_ROLE_ID'      , 1);        //不可删除，不可编辑权限站长角色id

define('ALLOW_AUTO_OPERATION_FUNCTION'  , ',time,get_client_ip,get_user_id,');//自动填充允许使用函数
define('ALLOW_AUTO_VALIDATE_FUNCTION'   , ',validate_dir,');                  //自动验证允许使用函数

//项目路径定义
define('APP_EXT'            , 'class.php');//类库文件后缀，不包括.
define('VIEW_EXT'           , 'phtml');    //模板文件后缀，不包括.
define('SESSION_PATH'       , SYS_APP_PATH . 'sessions/');   //session保存目录
define('LOG_PATH'           , SYS_APP_PATH . 'logs/');       //日志目录
define('APP_PATH'           , SYS_APP_PATH . 'modules/' . APP_NAME . DS);//项目目录
define('BOOTSTRAP_FILE'     , APP_PATH . 'Bootstrap.' . APP_EXT);//ini文件
define('CONF_FILE'          , INCLUDE_PATH . '/application.ini');//ini文件
define('CACHE_PATH'         , SYS_APP_PATH . 'caches/');     //缓存目录
define('MODULE_CACHE_PATH'  , CACHE_PATH . 'modules/');     //系统模块信息缓存目录
define('LANG_PATH'          , APP_PATH . 'languages/');     //项目语言包目录
define('VIEW_PATH'          , APP_PATH . 'views/');         //模板目录
define('APP_FORWARD'        , 'APP_FORWARD');               //Yaf_Controller_Abstract::forward标识

//博客设置
define('YABLOG_APP_PATH'    , SYS_APP_PATH . 'modules/yablog/');//模块app路径
define('YABLOG_FRONT_MODULE_NAME', 'yablog');//前台模块名称
define('BLOG_HTML_PATH'     , WWWROOT . 'blog/' . date('Ymd/'));//博客html路径

//网站配置定义
define('SESSION_ADMIN_KEY'     , '__admin__');//管理员session key
define('PAGE_SIZE'             , 20);//列表每页默认显示数
define('JSONP_CALLBACK'        , 'jsonpcallback');//jsonp 请求名称
define('DEFAULT_LANG'          , 'zh_cn');//默认语言
define('DEFAULT_TIMEZONE'      , 'asia/shanghai');//默认语言

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