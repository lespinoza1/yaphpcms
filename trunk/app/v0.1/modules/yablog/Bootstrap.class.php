<?php
/**
 * 前台yaf引导文件
 *
 * @file            Bootstrap.class.php
 * @package         Yap\Home
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-01-23 15:46:03
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap\Module\Home\Bootstrap;

class Bootstrap extends Yaf_Bootstrap_Abstract {

    /**
     * 预编译Smarty必须包含文件
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-18 17:56:36
     *
     * @return void 无返回值
     */
    private function _compileSmarty() {
        $filesize       = 0;
        $compile        = "<?php\n!defined('YAP_PATH') && exit('Access Denied');";//编译内容
        $compile_file   = dirname(RUNTIME_FILE) . '/~smarty.php';
        $require_files  = array(
            SMARTY_SYSPLUGINS_DIR . 'smarty_internal_data.php',
            SMARTY_SYSPLUGINS_DIR . 'smarty_internal_templatebase.php',
            SMARTY_SYSPLUGINS_DIR . 'smarty_internal_template.php',
            SMARTY_SYSPLUGINS_DIR . 'smarty_resource.php',
            SMARTY_SYSPLUGINS_DIR . 'smarty_internal_resource_file.php',
            SMARTY_SYSPLUGINS_DIR . 'smarty_cacheresource.php',
            SMARTY_SYSPLUGINS_DIR . 'smarty_internal_cacheresource_file.php',
            SMARTY_DIR . 'Adapter.php',
            SMARTY_DIR . 'Smarty.class.php',
        );

        //加载核心文件，用空间换时间
        if (APP_DEBUG) {//调试

            is_file($compile_file) && unlink($compile_file);

             foreach ($require_files as $file) {
                require($file);
            }
        }
        else {

            if (is_file($compile_file)) {
                require($compile_file);
            }
            else {

                foreach ($require_files as $file) {
                    require($file);
                    $filesize += filesize($file);
                    $compile  .= compileFile($file);
                }

                file_put_contents($compile_file, $compile);
                $size = filesize($compile_file);//编译后大小
                file_put_contents(LOG_PATH. 'compile_smarty.log', new_date() . '(' . format_size($filesize) . ' => ' . format_size($size) . ')' . EOL_LF, FILE_APPEND);
            }
        }
    }//end _compileSmarty

    /**
     * 初始化Smarty
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 17:11:31
     *
     * @param object $dispatcher Yaf_Dispatcher实例
     *
     * @return void 无返回值
     */
    private function _initSmarty(Yaf_Dispatcher $dispatcher) {

        if ('POST' == $dispatcher->getRequest()->getMethod()) {
            $dispatcher->disableView();
        }
        else {
            ini_set('yaf.use_spl_autoload', 'on');
            set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_DIR . 'sysplugins/');
            $this->_compileSmarty();
            $smarty = new Smarty_Adapter(null);
            $dispatcher->setView($smarty);
            Yaf_Registry::set('smarty', true);
        }
    }
}