<?php
/**
 * 微博控制器类
 *
 * @file            Miniblog.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-21 13:30:42
 * @lastmodify      $Date$ $Author$
 */

class MiniblogController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认true
     */
    protected $_init_model = true;

    /**
     * 首页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 13:30:55
     *
     * @return void 无返回值
     */
    public function indexAction() {
        $this->_display();
    }

    /**
     * 详请
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 13:47:40
     * @lastmodify      2013-04-26 23:13:01
     *
     * @return void 无返回值
     */
    public function detailAction() {
        $blog_id = Filter::int('id', 'get');
        $date    = Filter::int('date', 'get');

        if (!$blog_id || !$date) {//非法参数
            Logger::record(L('INVALID_PARAM') . "date=({$date}),id=($blog_id)", CONTROLLER_NAME);
            $this->_showMessage('error' . $blog_id . $date, null, 404);
        }

        if ($blog_info = $this->_model->find($blog_id)) {

            if (date('Ymd', $blog_info['add_time']) != $date) {//日期与id不匹配
                Logger::record(L('INVALID_PARAM') . "date=({$date}),id=($blog_id)", CONTROLLER_NAME);
                $this->_showMessage('error' . $blog_id . $date, null, 404);
            }

            $filename = str_replace(BASE_SITE_URL, WWWROOT, $blog_info['link_url']);
            new_mkdir(dirname($filename));

            $o = $this->_getViewTemplate('build_html')
            ->assign('blog_info', $blog_info)//微博内容
            ->assign(array(
                'web_title'         => L('MINIBLOG,DETAIL') . ' | ' . L('MINIBLOG'),
                //'seo_keywords'      => $blog_info['seo_keyword'],
                //'seo_description'   => $blog_info['seo_description'],
                //'tags'              => $this->tags($blog_info['seo_keyword']),
                //'relative_blog'     => $this->_getRelativeBlog($blog_id, $blog_info['seo_keyword']),
            ));

            $content = $o->fetch(CONTROLLER_NAME, 'detail');
            file_put_contents($filename, $content);
            echo $content;

        }
        else {//微博不存在
            $this->_showMessage(L('MINIBLOG,NOT_EXIST'), null, 404);
        }
    }
}