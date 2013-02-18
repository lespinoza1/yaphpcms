<?php
/**
 * 前台首页控制器类
 *
 * @file            Index.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-06-15 14:38:28
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap\Module\Home\Controller;

/**
 * 前台首页控制器类
 *
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-06-15 14:38:28
 * @lastmodify      $Date$ $Author$
 */

class IndexController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = false;

    /**
     * 管理中心。如果未登陆，跳转至登陆页
     *
     * @author          mrmsl
     * @date            2012-07-02 11:12:49
     * @lastmodify      2013-01-22 10:34:14 by mrmsl
     *
     * @return void 无返回值。如果未登陆跳转至登陆页
     */
    function indexAction() {
        $this->getView()->assign('name', 'mrmsl');
    }//end indexAction
}