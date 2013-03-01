<?php
/**
 * 留言控制器类
 *
 * @file            Guestbook.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-21 13:43:37
 * @lastmodify      $Date$ $Author$
 */

class GuestbookController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = true;

    /**
     * 首页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 13:44:11
     *
     * @return void 无返回值
     */
    public function indexAction() {
    }

    /**
     * 添加留言
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-26 17:44:43
     *
     * @return void 无返回值
     */
    public function addAction() {
        $check = $this->_model->checkCreate();//自动创建数据

        true !== $check && $this->_ajaxReturn(false, $check);//未通过验证

        $this->_model->startTrans()->add();

        return false;
    }
}