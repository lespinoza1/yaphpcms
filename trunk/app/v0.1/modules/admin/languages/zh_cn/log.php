<?php
/**
 * 系统日志模块语言中文包
 *
 * @file            log.php
 * @package         Yap\Module\Admin\Language
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-06-13 10:23:58
 * @lastmodify      $Date$ $Author$
 */

return array(
    'ADMIN_LOG'         => '管理员操作日志',
    'ADMIN_LOGIN_LOG'   => '后台登陆日志',
    'CRONTAB'           => '定时任务',
    'LOG_PAGE'          => '日志页面',
    'MODULE_NAME_LOG'   => '系统日志',
    'REFERER_PAGE'      => '来路页面',
    'SQL_ERROR'         => 'sql错误',
    'VALIDATE_FORM_ERROR' => '验证表单错误',
    'VERIFYCODE_ERROR'  => '验证码错误',
    'LOG_TYPE_LOAD_SCRIPT_TIME' => 'css及js加载时间记录',//by mrmsl on 2012-09-07 08:21:35
    'SLOWQUERY' => 'SQL慢查询',//by mrmsl on 2012-09-13 13:04:00
    'LOG_TYPE_ALL'                  => LOG_TYPE_ALL,
    'LOG_TYPE_SQL_ERROR'            => LOG_TYPE_SQL_ERROR,
    'LOG_TYPE_SYSTEM_ERROR'         => LOG_TYPE_SYSTEM_ERROR,
    'LOG_TYPE_ADMIN_OPERATE'        => LOG_TYPE_ADMIN_OPERATE,
    'LOG_TYPE_NO_PERMISSION'        => LOG_TYPE_NO_PERMISSION,
    'LOG_TYPE_ADMIN_LOGIN_INFO'     => LOG_TYPE_ADMIN_LOGIN_INFO,
    'LOG_TYPE_INVALID_PARAM'        => LOG_TYPE_INVALID_PARAM,
    'LOG_TYPE_CRONTAB'              => LOG_TYPE_CRONTAB,
    'LOG_TYPE_VALIDATE_FORM_ERROR'  => LOG_TYPE_VALIDATE_FORM_ERROR,
    'LOG_TYPE_VERIFYCODE_ERROR'     => LOG_TYPE_VERIFYCODE_ERROR,
    'LOG_TYPE_SCRIPT_TIME'          => LOG_TYPE_LOAD_SCRIPT_TIME,
    'LOG_TYPE_SLOWQUERY'            => LOG_TYPE_SLOWQUERY,
    'LOG_TYPE_ROLLBACK_SQL'         => LOG_TYPE_ROLLBACK_SQL,
    'SYSTEM_LOG_ARR'    => '[
     [' . LOG_TYPE_ALL . ', lang("ALL,LOG")],
     [' . LOG_TYPE_ADMIN_OPERATE . ', lang("ADMIN_LOG")],
     [' . LOG_TYPE_SQL_ERROR . ', lang("SQL_ERROR")],
     [' . LOG_TYPE_SYSTEM_ERROR . ', lang("SYSTEM,ERROR")],
     [' . LOG_TYPE_NO_PERMISSION . ', lang("NOT_HAS,PERMISSION")],
     [' . LOG_TYPE_INVALID_PARAM . ', lang("INVALID_PARAM")],
     [' . LOG_TYPE_ADMIN_LOGIN_INFO . ', lang("ADMIN_LOGIN_LOG")],
     [' . LOG_TYPE_CRONTAB . ', lang("CRONTAB")],
     [' . LOG_TYPE_VALIDATE_FORM_ERROR . ', lang("VALIDATE_FORM_ERROR")],
     [' . LOG_TYPE_VERIFYCODE_ERROR . ', lang("VERIFYCODE_ERROR")],
     [' . LOG_TYPE_LOAD_SCRIPT_TIME . ', lang("LOG_TYPE_LOAD_SCRIPT_TIME")],
     [' . LOG_TYPE_SLOWQUERY . ', lang("SLOWQUERY")],
     [' . LOG_TYPE_SLOWQUERY . ', lang("ROLLBACK_SQL")],
 ]',
);