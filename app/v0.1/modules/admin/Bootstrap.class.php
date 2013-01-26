<?php
/**
 * 后台yaf引导文件
 *
 * @file            Bootstrap.class.php
 * @package         Yap\Admin
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-01-23 15:46:03
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap\Module\Admin\Bootstrap;

class Bootstrap extends Yaf_Bootstrap_Abstract {
    /**
     * 启动方法
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-01-23 15:46:29
     *
     * @param object $dispatcher Yaf_Dispatcher实例
     *
     * @return void 无返回值
     */
    private function _initRun(Yaf_Dispatcher $dispatcher) {
        Yaf_Dispatcher::getInstance()->disableView();
    }
}