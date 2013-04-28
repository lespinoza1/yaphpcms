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
     * @var string $_model_name 对应模型名称。默认Comments
     */
    protected $_model_name = 'Comments';

    /**
     * 获取评论回复
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-28 12:47:13
     *
     * @param int $comment_id 评论id
     *
     * @return string $this->getRecurrsiveComments()返回html
     */
    protected function _getReplyComments($comment_id) {
        $data = $this->_model
        ->table(TB_COMMENTS)
        ->where('parent_id=' . $comment_id)
        ->order('comment_id')
        ->select();

        return $this->getRecurrsiveComments($data);
    }

    /**
     * 首页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 13:44:11
     *
     * @return void 无返回值
     */
    public function indexAction() {
        $total      = $this->_model
        ->table(TB_GUESTBOOK)
        ->alias('g')
        ->join(' JOIN ' . TB_COMMENTS . ' AS c ON g.comment_id=c.comment_id')
        ->where('c.parent_id=0')
        ->count();
        $page_info      = Filter::page($total, 'page', PAGE_SIZE);
        $page           = $page_info['page'];
        $page_one       = $page < 2;
        $guestbook_arr  = $this->_model
        ->table(TB_GUESTBOOK)
        ->alias('g')
        ->join(' JOIN ' . TB_COMMENTS . ' AS c ON g.comment_id=c.comment_id')
        ->where('c.parent_id=0')
        ->order('g.comment_id DESC')
        ->limit($page_info['limit'])
        ->select();

        $paging = new Paging(array(
            '_url_tpl'      => BASE_SITE_URL . 'guestbook/page/\\1.shtml',
            '_total_page'   => $page_info['total_page'],
            '_now_page'     => $page,
            '_page_size'    => PAGE_SIZE,
        ));

        $o = $this->_getViewTemplate($page_one ? 'build_html' : null)
        ->assign(array(
            'web_title'     => L('GUESTBOOK'),
            'guestbook_arr' => $guestbook_arr,
            'paging'        => $paging->getHtml(),
            'page'          => $page_one ? '' : $page,
        ));
        $content = $o->fetch(MODULE_NAME, ACTION_NAME, $page);

        if ($page_one) {
            $filename =  WWWROOT . 'guestbook.shtml';
            //file_put_contents($filename, $content);
        }

        echo $content;
    }//end indexAction

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