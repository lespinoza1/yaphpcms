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
     * @var bool $_init_model true实例对应模型。默认true
     */
    protected $_init_model = true;
    /**
     * @var string $_model_name 对应模型名称。默认Base
     */
    protected $_model_name = 'Base';

    /**
     * 获取首页博客
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-26 22:57:37
     *
     * @return array 博客数组
     */
    private function _getBlogs() {
        $blog_arr   = $this->_model
        ->table(TB_BLOG)
        //->where($where)
        ->order('blog_id')
        ->limit(10)
        ->field('title,link_url,add_time,summary')
        ->select();

        return $blog_arr;
    }

    /**
     * 获取最新一条微博信息
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-26 21:21:44
     *
     * @return array 微博信息
     */
    private function _getLatesttMiniblog() {
        $miniblog = $this->_model
        ->table(TB_MINIBLOG)
        ->field('link_url,add_time,content')
        ->order('blog_id DESC')
        ->find();

        return $miniblog;
    }

    /**
     * 首页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-25 21:39:23
     *
     * @return void 无返回值
     */
    public function indexAction() {
        $blog_arr = $this->_getBlogs();
        $miniblog = $this->_getLatesttMiniblog();
        $this->_getViewTemplate()
        ->assign('miniblog', $miniblog)
        ->assign('blog_arr', $blog_arr);
        $this->_display();
    }//end indexAction

}