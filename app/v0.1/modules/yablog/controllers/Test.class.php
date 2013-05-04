<?php
/**
 * 测试控制器类
 *
 * @file            Test.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-04 10:03:10
 * @lastmodify      $Date$ $Author$
 */

!IS_LOCAL && exit('Access Denied');

class TestController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = true;

    /**
     * 添加留言
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-04 10:03:53
     *
     * @return void 无返回值
     */
    public function addGuestbookAction() {
        $rand_content = $this->_model->table(TB_COMMENTS)
        ->field('2224 AS `parent_id`,0 AS `type`, 6807 AS `blog_id`,username,content,user_homepage')->order('RAND()')->limit(1)->select();
        $this->_getViewTemplate()
        ->assign('guestbook', $rand_content);
        $this->_display(CONTROLLER_NAME, 'index');
    }
}