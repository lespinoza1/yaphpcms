<?php
/**
 * 底层控制器类。摘自{@link http://www.thinkphp.cn thinkphp}，已对源码进行修改
 *
 * @file            Base.class.php
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

class BaseController extends Yaf_Controller_Abstract {
    /**
     * @var object $_view_template 模板编译对象。默认null
     */
    protected $_view_template = null;
    /**
     * @var bool $_init_model true实例对应模型。默认true
     */
    protected $_init_model = false;
    /**
     * @var string $_model_name 模型名称。默认null，对应控制器名
     */
    protected $_model_name         = null;
    /**
     * @var object $_model 对应模型实例。默认null
     */
    protected $_model = null;
    /**
     * @var array $_controller_name 控制器名称。默认null
     */
    protected $_controller_name = null;
    /**
     * @var string $_pk_field 数据表主键字段。默认null
     */
    protected $_pk_field = null;

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
     * ajax方式返回数据到客户端
     *
     * @author          liu21st <liu21st@gmail.com>
     * @lastmodify      2013-01-21 16:05:40 by mrmsl
     *
     * @param mixed  $success 返回状态或返回数组
     * @param string $msg     提示信息
     * @param mixed  $data    要返回的数据
     * @param mixed  $total   总数
     * @param string $type    ajax返回类型 JSON XML等
     *
     * @return void 无返回值
     */
    protected function _ajaxReturn($success = true, $msg = '', $data = null, $total = null, $type = '') {

        if (is_array($success)) {
            $result = $success;
        }
        else {
            $result = array(
                'success' => $success,
                'msg'     => $msg,
                'data'    => $data,
            );

            if (null !== $total) {
                $result['total'] = $total;
            }
        }

        $result['time'] = round(microtime(true) - REQUEST_TIME_MICRO, 6);

        //扩展ajax返回数据, 在Action中定义function ajaxAssign(&$result){} 方法 扩展ajax返回数据。
        method_exists($this, 'ajaxAssign') && $this->_ajaxAssign($result);

        $type = strtoupper($type ? $type : C('DEFAULT_AJAX_RETURN'));

        if ('JSON' == $type) {//返回JSON数据格式到客户端 包含状态信息
            if (__GET) {
                echo var_export($result, true); //调试模式下，不需要json_encode，以可读
            }
            else {
                header('Content-Type: application/json; charset=utf-8');

                $result = json_encode($result);
                $v      = C('JSONP_CALLBACK');

                if ($v && isset($_GET[$v])) {//jsonp
                    $result = $_GET[$v] . '(' . $result . ')';
                }

                echo $result;
            }

            if (!C(APP_FORWARD) || 'EXIT' === $data) {//无Yaf_Controller_Abstract::forward
                exit();
            }
        }
        elseif ('XML' == $type) {//返回xml格式数据
            header('Content-Type: text/xml; charset=utf-8');
            exit(xml_encode($result));
        }
        elseif ('EVAL' == $type) {//返回可执行的js脚本
            exit($data);
        }
    }//end _ajaxReturn

    /**
     * 渲染模板
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-06 17:32:39
     *
     * @param string $controller 控制器。默认MODULE_NAME
     * @param string $action     操作方法。默认MODULE_NAME
     * @param string $cache_id   缓存标识。默认''
     *
     * @return void 无返回值
     */
    protected function _display($controller = MODULE_NAME, $action = ACTION_NAME, $cache_id = '') {
        $this->_getViewTemplate()
        ->display($controller ? $controller : MODULE_NAME, $action ? $action : ACTION_NAME, $cache_id);
    }

    /**
     * 获取表数据缓存
     *
     * @param int    $id     数据id。默认0
     * @param string $name   文件名。默认null，模块名称
     * @param bool   $reload true重新加载。默认false
     * @param string $path   缓存路径。默认MODULE_CACHE_PATH
     *
     * @return mixed 如果不指定id，返回全部缓存，如果指定id并指定id缓存存在，返回指定id缓存，否则返回false
     */
    protected function _getCache($id = 0, $name = null, $reload = false, $path = MODULE_CACHE_PATH) {
        $name = $name ? $name : $this->_getControllerName();
        $data = F($name, '', $path, $reload);

        if ($id) {

            if (strpos($id, '.')) {//直接获取某一字段值
                list($id, $key) = explode('.', $id);
                return isset($data[$id][$key]) ? $data[$id][$key] : false;
            }
            else {
                return isset($data[$id]) ? $data[$id] : false;
            }
        }

        return $data;
    }

    /**
     * 获取指定分类下所有子类id
     *
     * @param int    $item_id      分类id
     * @param bool   $include_self true包含本身。默认true
     * @param bool   $return_array true返回数组形式。默认false
     * @param string $filename     缓存文件名。默认nulll，当前模块名
     * @param string $level_field  层次字段。默认level
     * @param string $node_field   节点字段。默认node
     *
     * @return string 所有子类id，如果没有子类，返回空字符串或空数组
     */
    protected function _getChildrenIds($item_id, $include_self = true, $return_array = false, $filename = null, $level_field = 'level', $node_field = 'node') {
        $filename      = $filename ? $filename : $this->_getControllerName();
        $cache_data    = $this->_getCache(0, $filename);

        if (!isset($cache_data[$item_id])) {
            return $return_array ? array() : '';
        }

        $item_info     = $cache_data[$item_id];
        $item_node     = $item_info[$node_field];
        $item_level    = $item_info[$level_field];
        $children_ids  = $include_self ? $item_id : '';

        foreach ($cache_data as $k => $v) {

            if (strpos($v[$node_field], $item_node . ',') === 0 && $v[$level_field] > $item_level && $k != $item_id) {
                $children_ids .= ',' . $k;
            }
        }

        $children_ids = trim($children_ids, ',');

        return $return_array ? explode(',', $children_ids) : $children_ids;
    }

    /**
     * 获取当前控制器名称
     *
     * @author            mrmsl <msl-138@163.com>
     * @data              2012-12-25 11:59:35
     * @lastmodify        2013-01-21 16:07:20 by mrmsl
     *
     * @return string 当前控制器名称
     */
    protected function _getControllerName() {
        $this->_controller_name = $this->_controller_name ? $this->_controller_name : substr(get_class($this), 0, -10);

        return $this->_controller_name;
    }

    /**
     * 获取表字段
     *
     * @return mixed 获取成功，将返回包含字段名的数组，否则false
     */
    protected function _getDbFields() {
        return $this->_model->getDbFields();
    }

    /**
     * 获取当前页面url
     *
     * @author          mrmsl <msl-138@163.com>
     * @lastmodify      2013-01-22 11:15:26 by mrmsl
     *
     * @return string 当前页面url
     */
    protected function _getPageUrl() {
        return REQUEST_METHOD . ' ' . SITE_URL . REQUEST_URI;
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

            $html .= '          </span> | ' . new_date(null, $item['add_time']);
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
     * 获取来路页面url
     *
     * @author          mrmsl <msl-138@163.com>
     * @lastmodify      2013-01-22 11:15:36 by mrmsl
     *
     * @return string 如果有来路，返回来路页面url，否则返回空字符串
     */
    protected function _getRefererUrl() {
        return REFERER_PAGER;
    }

    /**
     * 获取视图模板引擎实例
     *
     * @author            mrmsl <msl-138@163.com>
     * @data              2013-04-12 15:36:13
     * @lastmodify        2013-04-15 17:05:13 by mrmsl
     *
     * @param mixed $config 模板引擎配置。默认null.为build_html生成静态页时，$config = array('_caching' => true, '_force_compile' => false);
     *
     * @return object 视图模板引擎实例
     */
    protected function _getViewTemplate($config = null) {

        if (!$this->_view_template) {
            $this->_view_template = Template::getInstance();
            $this->_view_template->assign(sys_config())
            ->assign('L', L())
            ->assign('C', C())
            ->assign('me', $this)
            ->assign('nav_id', strtolower(CONTROLLER_NAME));
        }

        if (null !== $config) {//属性

            if ('build_html' === $config && IS_LOCAL) {//生成静态页
                $config = array(
                    '_caching'          => false,
                    '_force_compile'    => false,
                );
            }

            foreach($config as $k => $v) {
                $this->_view_template->$k = $v;

            }
        }

        return $this->_view_template;
    }

    /**
     * url跳转
     *
     * @author          mrmsl <msl-138@163.com>
     * @lastmodify      2013-01-21 16:13:40 by mrmsl
     *
     * @param string $url         跳转url。默认''，跳转到网站首页
     * @param string $base_url    基链接。默认null，相对网站根目录
     * @param int    $status_code 头部状态码。默认0，不发送头部状态码
     *
     * @return void 无返回值
     */
    protected function _redirect($url = '', $base_url = null, $status_code = 0) {
        $url = null === $base_url ? to_website_url('admin.php/') . $url : $base_url . $url;
        redirect($url, 0, '', $status_code);
    }

    /**
     * 设置子节点层次以及节点关系
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-01 13:46:01
     *
     * @param string $level_field     层次字段。默认level
     * @param string $node_field      节点字段。默认node
     * @param string $parent_id_field 父id字段。默认paretn_id
     *
     * @return bool true成功设置，否则false
     */
    protected function _setLevelAndNode($level_field = 'level', $node_field = 'node', $parent_id_field = 'parent_id') {
        $pk_field     = $this->_model->getPk();//主键
        $pk_value     = $this->_model->$pk_field;//主键值
        $parent_id    = $this->_model->$parent_id_field;//所属父类id
        $insert_id    = $this->_model->insert_id;//最后插入id
        //$parent_info  = $this->_model->find($pk_field = );

        if ($parent_id && !isset($cache_data[$parent_id])) {//未设置父类
            return false;
        }
        elseif (isset($cache_data[$parent_id])) {//父类
            $parent_item = $cache_data[$parent_id];
            $item_level  = $parent_item[$level_field] + 1;
            $item_node   = $parent_item[$node_field] . ',' . ($insert_id ? $insert_id : $pk_value);
            $data        = array($level_field => $item_level, $node_field => $item_node);//更新数据
        }

        $fields      = $this->_model->getProperty('_fields');//字段信息
        $this->_model->setProperty('_fields', array_merge($fields, array($level_field, $node_field)));


        if ($insert_id) {//新增

            if ($parent_id == 0) {
                $data = array($node_field => $insert_id);
            }

            $result = $this->_model->where($pk_field . '=' . $insert_id)->save($data);

            return $result;
        }

        if (!isset($cache_data[$pk_value])) {
            return false;
        }

        $item_info    = $cache_data[$pk_value];

        if ($parent_id != $item_info[$parent_id_field]) {//父id不相等

            if ($parent_id == 0) {//顶级
                $data = array($level_field => 1, $node_field => $pk_value);
                $item_level  = 1;
                $item_node   = $pk_value;
            }

            $result = $this->_model->where($pk_field . '=' . $pk_value)->save($data);

            /*
          * 所属分类不相同，修改其下子类节点及层级
          * 如将
          * pk_id level node
          * 1      1     1
          * 2      2     1,2
          * 10     2     1,10
          * 11     3     1,10,11
          *
          * pk_id=10移到pk_id=2下，新level,node关系为
          *
          * pk_id level node
          * 1      1     1
          * 2      2     1,2
          * 10     3     1,2,10
          * 11     4     1,2,10,11
          */
            if ($children_ids = $this->_getChildrenIds($pk_value, false, false, null, $level_field, $node_field)) {
                $data = array(
                    $level_field => array('exp', "{$level_field}+{$item_level}-{$item_info[$level_field]}", 'no_addslashes'),
                    $node_field  => array('exp', "CONCAT('{$item_node}', SUBSTR({$node_field}, LENGTH('{$item_info[$node_field]}') + 1))", 'no_addslashes'),
                );
                $result = $this->_model->where(array($pk_field => array('IN', $children_ids)))->save($data);//更新子类关系

                if (false === $result) {
                    $this->_ajaxReturn(false, L('SET_LEVEL_NODE,FAILURE'), 'EXIT');
                }
            }
        }

        return true;
    }//end _setLevelAndNode

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

    /**
     * 启动方法，Yaf自动调用
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-12-24 17:22:27
     * @lastmodify      2013-01-21 16:08:12 by mrmsl
     *
     * @return bool true
     */
    protected function init() {

        if ($this->_init_model) {//实例对应模型

            if (is_file(APP_PATH . 'models/' . $this->_getControllerName() . '.' . APP_EXT)) {
                $this->_model = D($this->_getControllerName());//模型
            }
            else {
                $this->_model = D(empty($this->_model_name) ? 'Base' : $this->_model_name);//模型
            }

            $this->_model->setProperty('_module', $this);
            $this->_pk_field = $this->_model->getPk();//主键字段
        }

        if (defined('APP_INIT')) {//跨模块，直接返回
            return true;
        }

        define('APP_INIT' , true);   //跨模块调用时，不再往下

        L('MODULE_NAME', L('MODULE_NAME_' .  $this->_getControllerName()));//C => L

        return true;
    }//end init

	   /**
     * 添加系统操作日志
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-26 16:27:09
     *
     * @param string $content   日志内容。默认''，取db最后执行sql
     * @param int    $log_type  日志类型。默认LOG_TYPE_SQL_ERROR，sql错误
     *
     * @return void 无返回值
     */
    public function addLog($content = '', $log_type = LOG_TYPE_SQL_ERROR) {
        $data = array(
            'content'  => LOG_TYPE_SQL_ERROR == $log_type && !$content ? $this->getLastSql() . '<br />' . $this->getDbError() : $content,
            'log_type' => $log_type,
        );

        $log_model = D('Log');
        $log_model->autoOperation($data, Model::MODEL_INSERT);
        $log_model->add($data);
        $log_model->commit();
    }

    /**
     * 获取不带链接的类似面包屑导航，如菜单管理»添加菜单
     *
     * @param int    $id         id字段值
     * @param string $name_field 名称字段
     * @param string $filename   缓存文件。默认null，当前模块名
     * @param string $separator  导航分割符。默认»
     *
     * @return string 面包屑导航
     */
    public function nav($id, $name_field, $filename = null, $separator = '&raquo;') {
        $nav  = array();
        $data = $this->_getCache(0, $filename ? $filename : $this->_getControllerName());
        $info = $data[$id];

        foreach(explode(',', $info['node']) as $item) {
            $nav[] = $data[$item][$name_field];
        }

        if (' | ' == $separator) {
            return join(' | ', array_reverse($nav));
        }

        return join($separator, $nav);
    }
}