<?php
/**
 * 博客控制器类
 *
 * @file            Blog.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-19 14:23:35
 * @lastmodify      $Date$ $Author$
 */

class BlogController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = true;

    /**
     * 管理中心。如果未登陆，跳转至登陆页
     *
     * @author          mrmsl
     * @date            2012-07-02 11:12:49
     * @lastmodify      2013-01-22 10:34:14 by mrmsl
     *
     * @return void 无返回值。如果未登陆跳转至登陆页
     */
    public function indexAction() {
    }
    public function pagenotfoundAction() {
        var_dump('Page Not Found');
        return false;
    }

    /**
     * 详请
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 15:26:00
     *
     * @return void 无返回值
     */
    public function detailAction() {
        $blog_id = Filter::int($this->_pk_field, 'get');

        if ($blog_id && ($blog_info = $this->_model->find($blog_id))) {
            $filename = str_replace(BASE_SITE_URL, WWWROOT, $blog_info['link_url']);
            new_mkdir(dirname($filename));
            $o = $this->_getViewTemplate('build_html');
            $o->assign('blog_info', $blog_info);
            $content = $o->fetch(CONTROLLER_NAME, 'detail');
            file_put_contents($filename, $content);
            echo $content;

        }
        else {
        }
    }
}