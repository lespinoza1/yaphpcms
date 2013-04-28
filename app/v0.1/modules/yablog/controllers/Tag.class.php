<?php
/**
 * 标签控制器类
 *
 * @file            Tag.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-25 09:54:41
 * @lastmodify      $Date$ $Author$
 */

class TagController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = true;

    /**
     * 首页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-28 17:14:18
     *
     * @return void 无返回值
     */
    public function indexAction() {
        $page_size  = 60;
        $total      = $this->_model
        ->table(TB_BLOG)
        ->alias('b')
        ->join(' JOIN ' . TB_TAG . ' AS t ON b.blog_id=t.blog_id')
        //->where($where)
        ->count('DISTINCT t.tag');
        $page_info      = Filter::page($total, 'page', $page_size);
        $page           = $page_info['page'];
        $page_one       = $page < 2;
        $tag_arr        = $this->_model
        ->table(TB_BLOG)
        ->alias('b')
        ->join(' JOIN ' . TB_TAG . ' AS t ON b.blog_id=t.blog_id')
        ->order('t.blog_id')
        ->field('DISTINCT t.tag')
        ->limit($page_info['limit'])
        ->select();

        $paging = new Paging(array(
            '_url_tpl'      => BASE_SITE_URL . 'tag/page/\\1.shtml',
            '_total_page'   => $page_info['total_page'],
            '_now_page'     => $page,
            '_page_size'    => $page_size,
        ));

        $o = $this->_getViewTemplate()
        ->assign(array(
            'web_title'     => L('TAG'),
            'tag_arr'       => $tag_arr,
            'paging'        => $paging->getHtml(),
            'page'          => $page_one ? '' : $page,
        ));

        $this->_display(null, null, $page);
    }//end indexAction
}