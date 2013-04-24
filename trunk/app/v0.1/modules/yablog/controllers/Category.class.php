<?php
/**
 * 博客分类控制器类
 *
 * @file            Blog.class.php
 * @package         Yap\Module\Yablog\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-18 11:50:33
 * @lastmodify      $Date: 2013-04-17 18:23:16 +0800 (周三, 17 四月 2013) $ $Author: msl-138@163.com $
 */

class CategoryController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = true;

    /**
     * 博客列表
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-18 11:50:55
     *
     * @return void 无返回值
     */
    public function indexAction() {
        $cate_name = Filter::string('name', 'get');
        $cate_arr  = $this->_getCache();

        if (!$cate_arr) {
            $this->_showMessage('no arr', null, 404);
        }

        foreach($cate_arr as $v) {

            if ($v['en_name'] == $cate_name) {
                $cate_info = $v;
                break;
            }
        }

        if (!isset($cate_info)) {
            Logger::record($cate_name . ' ' . L('NOT_EXIST'), CONTROLLER_NAME);
            $this->_showMessage($cate_name . ' ' . L('NOT_EXIST'), null, 404);
        }
        $cate_id    = $cate_info['cate_id'];
        $where      = array('cate_id' => array('IN', $this->_getChildrenIds($cate_id)));
        $total      = $this->_model
        ->table(TB_BLOG)
        ->where($where)
        ->count();
        $page       = Filter::int('page', 'get', 1);
        $page_info  = Filter::page($total, 'page', PAGE_SIZE);
        $blog_arr   = $this->_model
        ->table(TB_BLOG)
        ->where($where)
        ->order('blog_id')
        ->limit($page_info['limit'])
        ->field('title,link_url,cate_id,add_time,summary,seo_keyword')
        ->select();

        $o = $this->_getViewTemplate($page ? null : 'build_html')
        ->assign(array(
            'web_title' => $this->nav($cate_id, 'cate_name', null, ' | '),
            'blog_arr'  => $blog_arr,
            'cate_arr'  => $cate_arr,
            'cate_info' => $cate_info,
            'tag'       => ''
        ));
        $content = $o->fetch(MODULE_NAME, ACTION_NAME, $cate_id . '-' . $page);

        if ($page < 2) {
            $filename = str_replace(BASE_SITE_URL, WWWROOT, $cate_info['link_url']);
            new_mkdir(dirname($filename));
            //file_put_contents($filename, $content);
        }

        echo $content;
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
        $blog_id = Filter::int('id', 'get');

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
            exit('not exists');
        }
    }
}