<?php
/**
 * ssi服务器端包含控制器类
 *
 * @file            Ssi.class.php
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-10 08:32:06
 * @lastmodify      $Date$ $Author$
 */
class SsiController extends CommonController {
    /**
     * 分类导航
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 09:18:19
     *
     * @return void 无返回值
     */
    private function _categoryNav($parent_id = 0) {
        static $cate_arr = null;

        if (null === $cate_arr) {
            $cate_arr = $this->_getCache(0, 'Category');
        }

        $html      = '';

        foreach($cate_arr as $cate_id => $item) {

            if ($parent_id == $item['parent_id'] && $item['is_show']) {
                $a = sprintf('<li@class><a href="%s">%s</a>', $item['link_url'], $item['cate_name']);
                $b = $this->_categoryNav($cate_id);
                $a = str_replace('@class', $b ? ' class="dropdown-submenu"' : '', $a);

                $html .= $a . $b . '</li>';

                unset($cate_arr[$cate_id]);
            }
        }

        return $html ? '<ul class="dropdown-menu">' . $html . '</ul>' : '';
    }//end _categoryNav

    /**
     * 全部
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:40:15
     *
     * @return void 无返回值
     */
    public function allAction() {
        $this->newCommentsAction();
        $this->navbarAction();
        $this->tagsAction();
        $this->hotBlogsAction();
        $this->footerAction();
    }

    /**
     * 底部
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 13:30:10
     *
     * @return void 无返回值
     */
    public function footerAction() {
        $this->_getViewTemplate('build_html')->assign('footer', sys_config('sys_base_copyright'));
        $this->_buildHtml(SSI_PATH . 'footer' . C('HTML_SUFFIX'), $this->_fetch(null, 'footer'));
    }

    /**
     * 热门网文
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:59:29
     *
     * @return void 无返回值
     */
    public function hotBlogsAction() {
        $blogs = $this->_model
        ->table(TB_BLOG)
        ->order('hits DESC')
        ->where('is_issue=1 AND is_delete=0')
        ->field('link_url,title')
        ->limit(10)
        ->select();
        $this->_getViewTemplate('build_html')->assign('blogs', $blogs);
        $this->_buildHtml(SSI_PATH . 'hot_blogs' . C('HTML_SUFFIX'), $this->_fetch(null, 'hot_blogs'));
    }

    /**
     * 导航条
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 08:33:53
     *
     * @return void 无返回值
     */
    public function navbarAction() {
        $this->_getViewTemplate('build_html')->assign('category_html', $this->_categoryNav());
        $this->_buildHtml(SSI_PATH . 'navbar' . C('HTML_SUFFIX'), $this->_fetch(null, 'navbar'));
    }

    /**
     * 最新评论
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:38:39
     *
     * @return void 无返回值
     */
    public function newCommentsAction() {
        $comments = $this->_model
        ->table(TB_COMMENTS)
        ->alias('c')
        ->join(' LEFT JOIN ' . TB_BLOG . ' AS b ON c.blog_id=b.blog_id AND b.is_issue=1 AND b.is_delete=0')
        ->where('c.status=1 AND c.type!=2')
        ->order('c.comment_id DESC')
        ->field('c.*,b.link_url,b.title')
        ->limit(10)
        ->select();
        $this->_getViewTemplate('build_html')->assign('comments', $comments);
        $this->_buildHtml(SSI_PATH . 'new_comments' . C('HTML_SUFFIX'), $this->_fetch(null, 'new_comments'));
    }

    /**
     * 标签云
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:38:39
     *
     * @return void 无返回值
     */
    public function tagsAction() {
        $tags = $this->_model
        ->table(TB_TAG)
        ->order('searches DESC')
        ->field('DISTINCT `tag`')
        ->limit(50)
        ->select();
        $this->_getViewTemplate('build_html')->assign('tags', $tags);
        $this->_buildHtml(SSI_PATH . 'tags' . C('HTML_SUFFIX'), $this->_fetch(null, 'tags'));
    }
}