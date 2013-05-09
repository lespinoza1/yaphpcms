<?php
/**
 * 底层通用控制器类。摘自{@link http://www.thinkphp.cn thinkphp}，已对源码进行修改
 *
 * @file            Common.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          liu21st <liu21st@gmail.com>
 * @date            2013-02-17 15:04:18
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap\Module\Home\Controller;

/**
 * 底层控制器类。摘自{@link http://www.thinkphp.cn thinkphp}，已对源码进行修改
 *
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          liu21st <liu21st@gmail.com>
 * @date            2013-02-17 15:04:18
 * @lastmodify      $Date$ $Author$
 */

class CommonController extends BaseController {

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
    private function _getReplyComments($comment_id) {
        $data = $this->_model
        ->table(TB_COMMENTS)
        ->where('status=1 AND parent_id=' . $comment_id)
        ->order('comment_id')
        ->select();

        return $this->_getRecurrsiveComments($data);
    }

    /**
     * 循环获取评论
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-28 12:47:13
     *
     * @param array $comments 评论数组
     *
     * @return string 评论html
     */
    protected function _getRecurrsiveComments($comments) {

        if (!$comments) {
            return '';
        }

        $html = '';

        foreach ($comments as $item) {
            $html .= '
            <div class="panel-list media panel-miniblog comments-detail" id="comment-' . $item['comment_id'] . '">
                <img class="media-object pull-left avatar avatar-level-' . $item['level'] . '" alt="" src="' . ($item['user_pic'] ? $item['user_pic'] : COMMON_IMGCACHE . 'images/guest.png') . '" />
                <div class="media-body">
                    <div class="popover right">
                        <div class="arrow"></div>
                        <div class="popover-content">
                            <p class="muted">
                                <a href="#base-' . $item['comment_id'] . '" rel="nofollow" class="muted pull-right hide reply"><span class="icon-share-alt icon-gray"></span>' . L('REPLY') . '</a>
                                <span class="name-' . $item['comment_id'] . '">';

            if ($item['user_homepage']) {
                $html .= '          <a href="' . $item['user_homepage'] . '" rel="nofollow">' . $item['username'] . '</a>';
            }
            else {
                $html .=                $item['username'];
            }

            $html .= '          </span> | <span class="time-axis" data-time="' . $item['add_time'] . '">' . new_date(null, $item['add_time']) . '</span>';
            $html .= '      </p>';
            $html .= $item['content'];
            $html .= '<span id="base-' . $item['comment_id'] . '"></span>';

            if ($item['last_reply_time'] > $item['add_time'] && $item['level'] < 5) {
                $html .= $this->_getReplyComments($item['comment_id']);
            }

            $html .= '  </div>
                    </div>
                </div>
            </div>';
        }

        return $html;
    }//end _getRecurrsiveComments

    /**
     * 提示信息
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-23 13:40:58
     *
     * @param mixed $message     提示信息。三种格式：null(取C('MSG_CONTENT'))；string(提示字符串)；数组(array('msg_content' => '提示信息', ...)
     * @param array $link_url    显示链接数组。格式:array(array(text,link)...)或text,link
     * @param int   $status_code http状态码。默认null
     *
     * @return void 无返回值
     */
    protected function _showMessage($message, $link_url = array(), $status_code = null) {
        null !== $status_code && send_http_status($status_code);

        $template = $this->_getViewTemplate();

        if (is_string($link_url)) {//text,link
            $template->assign('link_url', explode(',', $link_url));
        }
        else {
            $template->assign('link_url', $link_url ? $link_url : array());
        }

        if (null === $message && is_array($v = C('MSG_CONTENT'))) {
            $template->assign($v);
        }
        elseif (is_string($message)) {
            $template->assign(array('msg_content' => $message));
        }
        elseif (is_array($message)) {
            $template->assign($message);
        }

        $this->_display('Msg', 'msg');
        exit();
    }

    /**
     *
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-23 13:40:58
     *
     * @param mixed $message     提示信息。三种格式：null(取C('MSG_CONTENT'))；string(提示字符串)；数组(array('msg_content' => '提示信息', ...)
     * @param array $link_url    显示链接数组。格式:array(array(text,link)...)或text,link
     * @param int   $status_code http状态码。默认null
     *
     * @return void 无返回值
     */
    public function tags($tags, $return_tags_array = false) {
        $html = '';

        if ($tags = trim($tags)) {
            $arr    = explode(strpos($tags, ' ') ? ' ' : ',', $tags);
            $arr    = array_unique($arr);

            if ($return_tags_array) {
                return $arr;
            }

            foreach ($arr as $v) {
                $html .= sprintf(',<a href="%s.shtml">%s</a>', BASE_SITE_URL . 'tag/' . urlencode($v), $v);
            }
        }

        return $html ? substr($html, 1) : '';
    }
}