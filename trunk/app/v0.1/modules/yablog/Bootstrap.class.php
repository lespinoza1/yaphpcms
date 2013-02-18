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
            require(SMARTY_DIR . 'Adapter.php');
            $smarty = new Smarty_Adapter(null);
            $dispatcher->setView($smarty);
            Yaf_Registry::set('smarty', true);
        }
    }
}