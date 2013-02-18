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
     * @var bool $_init_model true实例对应模型。默认true
     */
    protected $_init_model           = false;
    /**
     * @var object $_model 对应模型实例。默认null
     */
    protected $_model                = null;
    /**
     * @var array $_controller_name 控制器名称。默认null
     */
    protected $_controller_name      = null;
    /**
     * @var string $_pk_field 数据表主键字段。默认null
     */
    protected $_pk_field             = null;

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
     * 获取表字段
     *
     * @return mixed 获取成功，将返回包含字段名的数组，否则false
     */
    protected function _getDbFields() {
        return $this->_model->getDbFields();
    }

    /**
     * 设置子节点层次以及节点关系
     *
     * @param string $level_field     层次字段。默认level
     * @param string $node_field      节点字段。默认node
     * @param string $parent_id_field 父id字段。默认paretn_id
     *
     * @return bool true成功设置，否则false
     */
    protected function _setLevelAndNode($level_field = 'level', $node_field = 'node', $parent_id_field = 'parent_id') {
        $cache_data   = $this->_getCache();
        $pk_field     = $this->_model->getPk();//主键
        $pk_value     = $this->_model->$pk_field;//主键值
        $parent_id    = $this->_model->$parent_id_field;//所属父类id
        $insert_id    = $this->_model->insert_id;//最后插入id

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
            $this->_model = D($this->_getControllerName());//模型
            $this->_model->setProperty('_module', $this);
            $this->_pk_field = $this->_model->getPk();//主键字段
        }

        if (defined('APP_INIT')) {//跨模块，直接返回
            return true;
        }

        define('APP_INIT' , true);   //跨模块调用时，不再往下

        Yaf_Registry::has('smarty') && $this->getView()->setConfig(C('SMARTY_CONFIG'));

        L('MODULE_NAME', L('MODULE_NAME_' .  $this->_getControllerName()));//C => L

        return true;
    }//end init

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
    public function nav($id, $name_field, $filename = null, $separator = '»') {
        $nav  = array();
        $data = $this->_getCache(0, $filename ? $filename : $this->_getControllerName());
        $info = $data[$id];

        foreach(explode(',', $info['node']) as $item) {
            $nav[] = $data[$item][$name_field];
        }

        return join($separator, $nav);
    }
}