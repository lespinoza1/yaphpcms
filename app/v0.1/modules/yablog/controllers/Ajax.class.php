<?php
/**
 * ajax异步请求控制器类
 *
 * @file            Ajax.class.php
 * @package         Yap\Module\Home\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-02 09:02:56
 * @lastmodify      $Date$ $Author$
 */

class AjaxController extends BaseController {
    /**
     * @var bool $_init_model true实例对应模型。默认false
     */
    protected $_init_model      = true;

    /**
     * 获取博客,微博元数据,包括点击量,评论数等
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-02 16:19:49
     *
     * @param string $type  类型。blog:博客；1:微博
     * @param array  $data  id及时间数组
     *
     * @return array 元数据
     */
    private function _getMetaInfo($type = 'blog', $data) {

        if (!$data) {
            return array();
        }


        $table_arr = array(
            'blog'      => TB_BLOG,
            'miniblog'  => TB_MINIBLOG,
        );
        $blog_arr = array();

        foreach($data as $k => $v) {
            $v_arr = explode('|', $v);

            if (isset($v_arr[0], $v_arr[1]) && ($id = intval($v_arr[0])) && ($add_time = intval($v_arr[1]))) {
                $blog_arr[$id] = $add_time;
            }
        }

        if (!$blog_arr) {
            C('LOG_FILENAME', 'ajax');
            trigger_error($log = __METHOD__ . ',' . L('INVALID_PARAM') . var_export($data, true), E_USER_ERROR);
            $this->addLog($log, LOG_TYPE_INVALID_PARAM);

            return array();
        }

        $add_time_arr   = array_values($blog_arr);
        $blog_id_arr    = array_keys($blog_arr);
        $data           = $this->_model->table($table_arr[$type])
        ->where(array('blog_id' => array('IN', $blog_id_arr), 'add_time' => array('IN', $add_time_arr)))
        ->field('blog_id,add_time,hits,comments,diggs')// - 1 AS `add_time`
        ->key_column('blog_id')
        ->select();
        $un_match       = $type;

        foreach($data as $k => $v) {

            if ($blog_arr[$k] != $v['add_time']) {//id与时间不匹配
                $un_match .= ",{$k}({$blog_arr[$k]}) => {$k}({$v['add_time']})";
                unset($data[$k]);
            }
            else {
                unset($data[$k]['blog_id'], $data[$k]['add_time']);
            }
        }

        if ($un_match != $type) {
            C('LOG_FILENAME', 'ajax');
            trigger_error(__METHOD__ . ',' . $un_match, E_USER_WARNING);
        }

        return $data;
    }//end _getMetaInfo

    /**
     * 统计点击量
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-03 08:42:51
     *
     * @return void 无返回值
     */
    private function _updateHits() {
        $hits   = Filter::string('hits');//blog,id,add_time | miniblog,id,add_time

        if ($hits) {
            $hits_arr = explode(',', $hits);
            $valid    = false;

            if (3 == count($hits_arr) && in_array($hits_arr[0], array('miniblog', 'blog')) && ($id = intval($hits_arr[1])) && ($add_time = intval($hits_arr[2]))) {
                $this->_model->execute('UPDATE ' . DB_PREFIX . $hits_arr[0] . " SET hits=hits+1 WHERE blog_id={$id} AND add_time={$add_time}");
                $valid = $this->_model->getDb()->getProperty('_num_rows');

            }

            if (!$valid) {
                C('LOG_FILENAME', 'ajax');
                trigger_error($log = __METHOD__ . ',' . L('INVALID_PARAM') . var_export($hits_arr, true), E_USER_ERROR);
                $this->addLog($log, LOG_TYPE_INVALID_PARAM);
            }
        }
    }

    /**
     * ajax异步获取博客,微博元数据,包括点击量,评论数等
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-02 16:21:34
     * @lastmodify      2013-05-03 08:41:05 by mrmsl
     *
     * @return void 无返回值
     */
    public function metaInfoAction() {

        /*foreach (array(TB_BLOG, TB_MINIBLOG) as $table) {

            foreach($this->_model->table($table)->select() as $v) {
                $sql = sprintf('UPDATE %s SET hits=%d,comments=%d,diggs=%d WHERE blog_id=%d', $table, rand(1, 1000), rand(1, 50), rand(1, 20), $v['blog_id']);
                $this->_model->execute($sql);
            }
        }*/

        $this->_updateHits();//统计点击

        $blog       = Filter::string('blog');
        $miniblog   = Filter::string('miniblog');

        if (!$blog && !$miniblog) {//空数据
            C('LOG_FILENAME', 'ajax');
            trigger_error($log = __METHOD__ . ',' . L('INVALID_PARAM'), E_USER_ERROR);
            $this->addLog($log, LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false);
        }
        $blog           = 0 === strpos($blog, ',') ? substr($blog, 1) : $blog;
        $blog_arr       = $blog ? explode(',', $blog) : array();
        $miniblog       = 0 === strpos($miniblog, ',') ? substr($miniblog, 1) : $miniblog;
        $miniblog_arr   = $miniblog ? explode(',', $miniblog) : array();

        if (($len_1 = count($blog_arr)) > PAGE_SIZE || ($len_2 = count($miniblog_arr)) >PAGE_SIZE) {//长度限制判断
            C('LOG_FILENAME', 'ajax');
            trigger_error(__METHOD__ . ',' . $len_1 . (isset($len_2) ? ',' . $len_2 : '') . ' > ' . PAGE_SIZE, E_USER_ERROR);
            $this->_ajaxReturn(false);
        }

        $miniblog_data  = $this->_getMetaInfo('miniblog', $miniblog_arr);
        $blog_data      = $this->_getMetaInfo('blog', $blog_arr);

        $this->_ajaxReturn(array('blog' => $blog_data, 'miniblog' => $miniblog_data, 'success' => true));
    }//end metaInfoAction
}