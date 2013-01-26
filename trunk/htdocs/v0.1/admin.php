<?php
/**
 * Yaphpcms后台管理入口文件
 *
 * @file            admin.php
 * @package         Yap\Admin
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-01-23 11:11:49
 * @lastmodify      $Date$ $Author$
 */

define('SYS_VERSION'    , '0.1');//系统版本号
define('VERSION_PATH'   , 'v' . SYS_VERSION . '/');//版本目录
define('WWWROOT'        , __DIR__ . '/');//网站根目录
define('APP_NAME'       , 'admin');//项目名称

require(WWWROOT . 'bootstrap.php');