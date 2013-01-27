<?php
/**
 * Yaphpcms入口引导文件
 *
 * @file            bootstrap.php
 * @package         Yap\Admin
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-12-24 14:39:28
 * @lastmodify      $Date$ $Author$
 */

!defined('APP_NAME') && exit('Access Denied');

define('SYS_VERSION'    , '0.1');//系统版本号
define('VERSION_PATH'   , 'v' . SYS_VERSION . '/');//版本目录
define('WWWROOT'        , __DIR__ . '/');//网站根目录

define('IS_LOCAL'           , true);     //是否本地环境
define('APP_DEBUG'          , true);     //调试
define('RUNTIME_FILE'       , WWWROOT . '~runtime.php');//运行时文件

define('SYS_PATH'       , dirname(dirname(WWWROOT)) . '/');//系统目录
define('SYS_APP_PATH'   , SYS_PATH . 'app/' . VERSION_PATH);//系统应用目录
define('SYS_LANG_PATH'  , SYS_APP_PATH . 'languages/');//系统应用目录
define('INCLUDE_PATH'   , SYS_APP_PATH . 'include/');//include包含路径

require(SYS_PATH . 'yaphpcms/' . VERSION_PATH . 'yaphpcms.class.php');

$require_files = array(
    INCLUDE_PATH . 'app_config.php',
    INCLUDE_PATH . 'db_config.php',
);

$yaphpcms = new Yaphpcms($require_files);
$yaphpcms->bootstrap();