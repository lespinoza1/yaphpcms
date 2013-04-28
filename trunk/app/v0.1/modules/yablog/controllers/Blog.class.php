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
     * 根据指定博客id获取评论
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-28 14:56:35
     *
     * @param int $blog_id 当前博客id
     *
     * @return string 评论html
     */
    private function _getBlogComments($blog_id) {
        $comments = $this->_model
        ->table(TB_BLOG_COMMENTS)
        ->field('c.*')
        ->alias('b')
        ->join(' JOIN ' . TB_COMMENTS . ' AS c ON b.comment_id=c.comment_id')
        ->where('c.parent_id=0 AND b.blog_id=' . $blog_id)
        ->order('c.comment_id DESC')
        ->select();

        return $this->_getRecurrsiveComments($comments);
    }

    /**
     * 根据指定博客id获取上、下一篇博客标题及链接
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 15:26:00
     *
     * @param int $blog_id 当前博客id
     *
     * @return array 上、下一篇博客
     */
    private function _getNextAndPrevBlog($blog_id) {
        $next_id   = $blog_id + 1;
        $prev_id   = $blog_id - 1;
        $data      = $this->_model->field('blog_id,title,link_url')->key_column('blog_id')->select();

        return array(
            'next_blog' => isset($data[$next_id = $blog_id + 1]) ? $data[$next_id] : false,
            'prev_blog' => isset($data[$prev_id = $blog_id - 1]) ? $data[$prev_id] : false,
        );
    }

    /**
     * 根据指定博客id获取相关博客
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-24 11:34:18
     *
     * @param int $blog_id 当前博客id
     *
     * @return array 相关博客
     */
    private function _getRelativeBlog($blog_id, $tags) {
        $tags = $this->tags($tags, true);

        if (!$tags) {
            return array();
        }

        $t = '';

        foreach($tags as $v) {
            $t .= ",'" . addslashes($v) . "'";
        }

        $data = $this->_model
        ->alias('b')
        ->field('b.title,b.link_url')
        ->join('JOIN ' . TB_TAG . ' AS t ON b.blog_id=t.blog_id')
        ->where(array('t.tag' => array('IN', $tags), 't.blog_id' => array('NEQ', $blog_id)))
        ->limit(5)
        ->order('b.hits DESC')
        ->select();

        return $data;
    }

    /**
     * 详请
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-21 15:26:00
     * @lastmodify      2013-04-23 14:32:00 by mrmsl
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
            ->assign($this->_getNextAndPrevBlog($blog_id))//上下篇
            ->assign('blog_info', $blog_info)//博客内容
            ->assign(array(
                'web_title'         => $blog_info['title'] . ' | ' . $this->nav($blog_info['cate_id'], 'cate_name', 'Category', ' | '),
                'seo_keywords'      => $blog_info['seo_keyword'],
                'seo_description'   => $blog_info['seo_description'],
                'tags'              => $this->tags($blog_info['seo_keyword']),
                'relative_blog'     => $this->_getRelativeBlog($blog_id, $blog_info['seo_keyword']),
                'comments_html'     => $this->_getBlogComments($blog_id),
            ));

            $content = $o->fetch(CONTROLLER_NAME, 'detail');
            //file_put_contents($filename, $content);
            echo $content;

        }
        else {//博客不存在
            $this->_showMessage(L('BLOG,NOT_EXIST'), null, 404);
        }
    }
}