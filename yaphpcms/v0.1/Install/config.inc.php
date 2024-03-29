<?php
/**
 * config.inc.php           默认配置
 *
 * @author                  mrmsl <msl-138@163.com>
 * @date                    2012-12-24 15:52:30
 * @lastmodify              2013-01-22 17:47:42 by mrmsl
 */

return array(
    //项目设置
    'DEFAULT_AJAX_RETURN'   => 'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...

    //数据库配置
    'DB_TYPE'               => DB_TYPE,      //数据库类型
    'DB_HOST'               => DB_HOST,      //服务器地址
    'DB_NAME'               => DB_NAME,      //数据库名
    'DB_USER'               => DB_USER,      //用户名
    'DB_PWD'                => DB_PWD,       //密码
    'DB_PORT'               => DB_PORT,      //端口
    'DB_PREFIX'             => DB_PREFIX,    //表前缀
    'DB_DSN'                => DB_DSN,       //DSN
    'LOG_SQL'               => false,        //是否记录sql语句

    //日志配置
    'LOG_TYPE'              => 3,       //文件
    'LOG_FILE_SUFFIX'       => '.log',  //日志文件名后缀

    'JSONP_CALLBACK'        => JSONP_CALLBACK,//jsonp 回调参数名

    //模板设置
    'TEMPLATE_SUFFIX'       => VIEW_EXT,//模板后缀
    'TMPL_EXCEPTION_FILE'   => VIEW_PATH . 'error/error.' VIEW_EXT,//错误模板
    //smarty 模板设置
    'SMARTY_CONFIG'         => array(
        'left_delimiter'    => '<!--{',
        'right_delimiter'   => '}-->',
        'template_dir'      => APP_PATH . '/views/',
        'compile_dir'       => APP_PATH . '/views/templates_c/',
        'cache_dir'         => APP_PATH . '/views/templates_d/',
    ),
);