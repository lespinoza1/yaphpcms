<?php
/**
 * Yaphpcms类
 *
 * @file            Yaphpcms.class.php
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-01-22 14:21:03
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap;

//use Yap\Module\Admin\Bootstrap\Bootstrap as Bootstrap;
//use Yap\Module\Admin\Model;

/**
 * Yaphpcms类
 *
 * @package         Yap
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-01-22 14:21:03
 * @lastmodify      $Date$ $Author$
 */
class Yaphpcms {
    /**
     * @var array $_require_files 加载核心核心文件
     */
    private $_require_files = array();

    /**
     * 创建运行时文件
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-01-22 15:45:33
     * @lastmodify      2013-02-18 17:03:00 by mrmsl
     *
     * @return void 无返回值
     */
    private function _buildRuntimeFile() {
        $filesize = 0;//加载文件大小
        $compile  = "<?php\n!defined('YAP_PATH') && exit('Access Denied');";//编译内容

        //加载核心文件
        foreach ($this->_require_files as $file) {
            require($file);

            if (defined('APP_DEBUG') && !APP_DEBUG) {
                $filesize += filesize($file);
                $compile  .= compileFile($file);
            }
        }

        $this->_defineConstants(false);

        $require_files = array(
            YAP_PATH . 'Plugin/Bootstrap.' . APP_EXT,//启动插件类
            YAP_PATH . 'Filter/Filter.' . APP_EXT,//参数验证及过滤类
            YAP_PATH . 'Db/Db.' . APP_EXT,//Db类
            YAP_PATH . 'Db/Db' . ucfirst(DB_TYPE) . '.' . APP_EXT,//数据库驱动类
            YAP_PATH . 'Model/Model.' . APP_EXT,//模型类
            YAP_PATH . 'Log/Logger.' . APP_EXT,//日志类
        );

        if (is_file($filename = APP_PATH . 'controllers/Base.' . APP_EXT)) {//项目底层控制器类
            $require_files[] = $filename;
        }

        if (is_file($filename = APP_PATH . 'models/Base.' . APP_EXT)) {//项目底层模型类
            $require_files[] = $filename;
        }

        //加载核心文件，用空间换时间
        if (APP_DEBUG) {//调试

             foreach ($require_files as $file) {
                require($file);
            }
        }
        else{

            foreach ($require_files as $file) {
                require($file);
                $filesize += filesize($file);
                $compile  .= compileFile($file);
            }

            file_put_contents(RUNTIME_FILE, $compile);
            $size = filesize(RUNTIME_FILE);//编译后大小
            file_put_contents(LOG_PATH. 'compile_runtime_file.log', new_date() . '(' . format_size($filesize) . ' => ' . format_size($size) . ')' . EOL_LF, FILE_APPEND);

        }

    }//end _buildRuntimeFile

    /**
     * 检查运行环境，必须要满足：1、加载yaf扩展；2、yaf.use//namespace=on；3、PHP版本大于5.3
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-01-22 14:38:13
     *
     * @return void 无返回值
     */
    private function _checkRuntimeRequirements() {
        !extension_loaded('yaf') && exit('yaf extension required!');
        //!ini_get('yaf.use_//namespace') && exit('yaf.use_//namespace=on required!');
        !version_compare(PHP_VERSION, '5.3', '>') && exit('php5.3 or higher required!');
    }

    /**
     * 定义常量
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-01-22 14:38:13
     *
     * @param bool $is_yap true定义yap常量。默认true
     *
     * @return void 无返回值
     */
    private function _defineConstants($is_yap = true) {

        if ($is_yap) {
            define('Yap\VERSION'            , '0.1');         //yaphpcms版本
            define('Yap\RELEASE'            , '20130122');      //yaphpcms版本发布日期
            define('YAP_PATH'               , __DIR__ . '/');  //yaphpcms框架路径
        }
        else {
            !defined($v = 'BOOTSTRAP_FILE')   && define($v, YAP_PATH . 'Bootstrap.php');//运行时文件
            !defined($v = 'CONF_FILE')      && define($v, YAP_PATH . 'Conf/application.ini');//ini文件
            !defined($v = 'APP_PATH')       && define($v, dirname($_SERVER['SCRIPT_FILENAME']) . DS);//项目目录
            !defined($v = 'APP_EXT')        && define($v, 'class.php');//类库文件后缀，不包括.
            !defined($v = 'VIEW_EXT')       && define($v, 'phtml');//模板文件后缀，不包括.
        }
    }

    /**
     * 构造函数
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-01-22 15:05:51
     *
     * @param array $require_files 预加载核心文件
     *
     * @return void 无返回值
     */
    public function __construct($require_files) {
        $this->_checkRuntimeRequirements();//运行环境检查
        $this->_defineConstants();//常量定义
        $this->_require_files[] = YAP_PATH . 'Function/functions.php';//函数库

        if ($require_files) {
            $this->_require_files = array_merge($this->_require_files, $require_files);//合并核心文件
        }

        //$this->_buildRuntimeFile();
    }

    /**
     * 启动程序
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-01-22 15:06:16
     *
     * @return void 无返回值
     */
    public function bootstrap() {
        ob_get_level() != 0 && ob_end_clean();
        header('content-type: text/html; charset=utf-8');

        if (APP_DEBUG || !is_file(RUNTIME_FILE)) {
            $this->_buildRuntimeFile();

            if (APP_DEBUG && is_file(RUNTIME_FILE)) {
                unlink(RUNTIME_FILE);
            }
        }
        else {
            require(RUNTIME_FILE);
        }

        $app = new Yaf_Application(CONF_FILE);
        $app->getDispatcher()->registerPlugin(new BootstrapPlugin());
        $app->bootstrap()->run();
    }
}