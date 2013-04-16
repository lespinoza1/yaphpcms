<?php
/**
 * 底层控制器类。摘自{@link http://www.thinkphp.cn thinkphp}，已对源码进行修改
 *
 * @file            Base.class.php
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          liu21st <liu21st@gmail.com>
 * @date            2012-12-24 17:22:00
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap\Module\Admin\Controller;

/**
 * 底层控制器类。摘自{@link http://www.thinkphp.cn thinkphp}，已对源码进行修改
 *
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          liu21st <liu21st@gmail.com>
 * @date            2012-12-24 17:22:00
 * @lastmodify      $Date$ $Author$
 */

class BaseController extends Yaf_Controller_Abstract {
    /**
     * @var object $_view_template 模板编译对象。默认null
     */
    protected $_view_template           = null;
    /**
     * @var array $_admin_info 管理员信息
     */
    protected $_admin_info           = array();
    /**
     * @var bool $_auto_check_priv true自动检测权限。默认true
     */
    protected $_auto_check_priv      = true;
    /**
     * @var bool $_init_model true实例对应模型。默认true
     */
    protected $_init_model           = true;
    /**
     * @var object $_model 对应模型实例。默认null
     */
    protected $_model                = null;
    /**
     * @var array $_no_need_priv_action 不需要验证权限方法
     */
    protected $_no_need_priv_action  = array();
    /**
     * @var array $_no_need_priv_module 不需要验证权限模块
     */
    protected $_no_need_priv_module  = array();
    /**
     * @var array $_role_info 角色信息，实时验证管理员权限
     */
    protected $_role_info            = array();
    /**
     * @var array $_controller_name 控制器名称。默认null
     */
    protected $_controller_name      = null;
    /**
     * @var bool $_init_model true 将array_unshift()增加如'请选择'选项
     */
    protected $_unshift;
    /**
     * @var string $_pk_field 数据表主键字段。默认null
     */
    protected $_pk_field             = null;
    /**
     * @var array $_priv_map 权限映射，如'delete' => 'add'删除权限映射至添加权限
     */
    protected $_priv_map             = array(
        'info'         => 'add',//获取信息
    );

    /**
     * 设置某一字段值后置操作，如博客，设置发布后，生博客静态页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-12 14:34:45
     *
     * @param string $field  字段名
     * @param string $valule 字段值
     * @param string $pk_id  主键值
     *
     * @return void 无返回值
     */
    protected function _afterSetField($field, $value, $pk_id) {
    }

    /**
     * 生成静态页
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-12 15:01:54
     *
     * @param string $dir      保存路径
     * @param string $filename 文件名
     * @param string $content  文件内容
     *
     * @return void 无返回值
     */
    protected function _buildHtml($dir, $filename, $content) {
        !is_dir($dir) && mkdir($dir, 0755, true);

        file_put_contents($dir . $filename, $content);
    }

    /**
     * 检测登陆
     *
     * @author          mrmsl
     * @date            2012-06-20 15:54:57
     * @lastmodify      2013-01-21 16:04:15 by mrmsl
     *
     * @return void 无返回值
     */
    private function _checkLogined() {

        if (!$this->_admin_info) {
            send_http_status(HTTP_STATUS_UNLOGIN);//401 验证用户登陆
            $this->_model && $this->_model->addLog(L('NOT_HAS,LOGIN') . '.' . get_class($this) . '->' . __FUNCTION__ . '().' . $this->_getControllerName() . ':' . ACTION_NAME, LOG_TYPE_NO_PERMISSION);
            $this->_ajaxReturn(false);
        }
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

            if (null !== $config) {//属性

                if ('build_html' === $config && IS_LOCAL) {//生成静态页
                    $config = array(
                        '_caching'          => true,
                        '_force_compile'    => false,
                    );
                }

                foreach($config as $k => $v) {
                    $this->_view_template->$k = $v;

                }
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
     * 获取具体信息
     *
     * @param mixed $field   选取字段，如果为false，则取缓存
     * @param mixed $exclude 排除，如果$field为false，为缓存文件名。如果为true，将排除$field字段
     *
     * @return void 无返回值
     */
     protected function _info($field = '*', $exclude = false) {
        $id  = Filter::int($this->_pk_field, 'get');
        $msg = L('GET,MODULE_NAME,INFO,FAILURE');

        if ($id) {
            $info = true === $field ? $this->_getCache($id, false === $exclude ? $this->_getControllerName() : $exclude) : $this->_model->field($field, $exclude)->find($id);

            if ($info) {
                $this->_infoCallback($info);

                if (isset($_GET['clone'])) {//复制
                    $info[$this->_pk_field] = 0;
                }

                $this->_ajaxReturn(true, '', $info);
            }


            $this->_model->addLog($msg . '<br />' . L("INVALID_PARAM,%:,MODULE_NAME,%id({$id}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, L('MODULE_NAME') . L('NOT_EXIST'));
        }

        $this->_model->addLog($msg . '<br />' . L("INVALID_PARAM,%:,MODULE_NAME,%{$this->_pk_field},IS_EMPTY"), LOG_TYPE_INVALID_PARAM);
        $this->_ajaxReturn(false, $msg);
    }

    /**
     * 获取信息回调
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-07 22:27:03
     * @lastmodify      2013-01-21 15:43:15 by mrmsl
     *
     * @param array $data 数据
     *
     * @return void 无返回值
     */
    protected function _infoCallback(&$data) {
    }

    /**
     * 在执行操作数据之前，获取操作日志，缓存数据
     *
     * @param array  $pk_id 主键值数组
     * @param string $log   日志内容
     *
     * @return mixed 如果类设置了$_name_column，返回缓存数组，否则返回null
     */
    protected function _beforeExec(&$pk_id, &$log) {
        if (($args_length = count($args = func_get_args())) > 2) {//大于两个参数，适用于更新某个字段值，写只修改指定缓存

            if ('Admin' == MODULE_NAME && 'lock' == ACTION_NAME) {//管理锁定与不锁定
                $is_admin_lock = true;
                $field         = 'lock_start_time';
                $value         = $args[3];
            }
            else {
                $field = $args[2];//更新字段
                $value = $args[3];//值
            }
        }

        if (!empty($this->_get_children_ids)) {//获取所有孩子
            $all_pk_id  = array();

            foreach($pk_id as $id) {//
                $all_pk_id = array_merge($all_pk_id, $this->_getChildrenIds($id, true, true));
            }

            $pk_id = array_unique($all_pk_id);
        }

        if (!empty($this->_name_column)) {//名称字段
            $data = $this->_getCache();//数据缓存

            foreach($pk_id as $id) {//获取操作日志与删除(更新)指定缓存

                if (isset($data[$id])) {
                    $info = $data[$id];
                    $log .= "{$info[$this->_name_column]}({$info[$this->_pk_field]}), ";

                    if (isset($field)) {//更新
                        $data[$id][$field] = $value;
                    }
                    else {//删除
                        unset($data[$id]);
                    }
                }
            }

            if (isset($is_admin_lock)) {//管理员锁定与不锁定
                $data[$id] = array_merge($data[$id], $args[2]);
            }

            $log = trim($log, ', ');

            return $data;
        }

        return null;
    }//end _beforeExec

    /**
     * 冒泡查找，以组装成树结构
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-15 11:33:33
     * @lastmodify      2013-01-21 16:18:34 by mrmsl
     *
     * @param mixed $pk_value 主键值
     *
     * @return array 树结构数据
     */
    protected function _bubble($pk_value) {
        $pk_value  = is_array($pk_value) ? $pk_value : explode(',', $pk_value);
        $data      = array();
        $data_arr  = $this->_getCache();

        foreach ($pk_value as $v1) {
            $arr = explode(',', $data_arr[$v1]['node']);

            foreach ($arr as $v2) {

                if (!isset($data[$v2]) || $v2 != $v1) {

                    if ($v2 != $v1) {//父级节点展开
                        $data_arr[$v2]['expanded'] = true;
                    }

                    $data[$v2] = $data_arr[$v2];
                }
            }
        }

        return Tree::array2tree($data, $this->_pk_field);
    }

    /**
     * 根据不同匹配模式组装查询SQL
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-07-28 18:02:36
     * @lastmodify      2013-01-21 16:20:12 by mrmsl
     *
     * @param string $column     搜索字段
     * @param string $keyword    关键字。默认''
     * @param string $match_mode 匹配格式.eq:完全匹配;leq:左匹配;req:右匹配;like:模糊匹配。默认eq
     * @param mixed  $int_field  整形字段。默认''
     *
     * @return array 条件数组信息
     */
    protected function _buildMatchQuery($column, $keyword = '', $match_mode = 'eq', $int_field = '') {

        if ($keyword === '') {//搜索关键字为空，直接返回 by mrmsl on 2012-09-15 12:24:39
            return array();
        }

        $mode_arr   = array('eq' => '%s', 'leq' => '%s%%', 'req' => '%%%s', 'like' => '%%%s%%');
        $match_mode = strtolower($match_mode);
        $match_mode = array_key_exists($match_mode, $mode_arr) ? $match_mode : 'eq';
        $int_field  = $int_field ? (is_array($int_field) ? $int_field : explode(',', $int_field)) : array();

        if (in_array($column, $int_field)) {//整形
            return array('IN', map_int($keyword));
        }

        return array($match_mode == 'eq' ? $match_mode : 'LIKE', sprintf($mode_arr[$match_mode], $keyword));
    }

    /**
     * 验证管理员是否有权限执行指定操作
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-06-20 16:00:58
     * @lastmodify      2013-01-21 16:21:34 by mrmsl
     *
     * @param string $module      模块，即控制器。默认null，当前模块
     * @param string $action      操作方法。默认ACTION_NAME
     * @param bool   $just_return true返回是否有仅限。默认false
     *
     * @return bool true有权限，否则false
     */
    protected function _checkAdminPriv($module = null, $action = ACTION_NAME, $just_return = false) {
        $this->_checkLogined();//登陆

        $module   = $module ? $module : $this->_getControllerName();
        $_module  = $module;
        $module   = strtolower($module);
        $_action  = $action;
        $action   = strtolower($action);
        $priv_str = $module . $action;//权限字符串

        if (ADMIN_ROLE_ID == $this->_admin_info['role_id']) {//站长角色
            $checked = true;
        }
        //公用方法
        elseif(0 === strpos($action, 'public')) {
            $checked = true;
        }
        //动态设置不需要验证操作，适合跨模块操作
        elseif (($no_priv_action = C('NO_PRIV_ACTION')) && in_array($priv_str, array_map('strtolower', is_array($no_priv_action) ? $no_priv_action : explode(',', $no_priv_action)))) {
            $checked = true;
        }
        //不需要验证操作
        elseif (!empty($this->_no_need_priv_action) && in_array($action, array_map('strtolower', $this->_no_need_priv_action))) {
            $checked = true;
        }
        //不需要验证模块
        elseif (!empty($this->_no_need_priv_module) && in_array($module, array_map('strtolower', $this->_no_need_priv_module))) {
            $checked = true;
        }
        else {

            if(!empty($this->_priv_map) && isset($this->_priv_map, $action)) {//权限映射
                $priv_str = $module . $this->_priv_map[$action];
            }

            $checked  = in_array($priv_str, $this->_role_info['priv']);//检测权限

            if ($checked) {//判断父级菜单权限
                $menu_arr  = $this->_getCache(0, 'Menu');
                $menu_id   = array_search($priv_str, $this->_role_info['priv'], true);
                $menu_node = $menu_arr[$menu_id]['node'];

                if ($menu_node != $menu_id) {//2 => 1,2
                    $node_arr = explode(',', $menu_node);
                    array_pop($node_arr);

                    foreach ($node_arr as $menu_id) {

                        if (!isset($this->_role_info['priv'], $menu_id)) {
                            $checked = false;
                            break;
                        }
                    }
                }

                unset($menu_arr, $menu_info);
            }
        }

        !$checked && $this->_model && $this->_model->addLog(L('NOT_HAS,PERMISSION') . '.' . get_class($this) . '->' . __FUNCTION__ . ".{$_module}:" . $_action, LOG_TYPE_NO_PERMISSION);

        if ($just_return) {//仅仅返回是否有权限
            return $checked;
        }
        elseif (!$checked) {
            send_http_status(HTTP_STATUS_NO_PRIV);
            $this->_ajaxReturn(false);
        }

        return true;
    }//end _checkAdminPriv

    /**
     * 检测是否是叶，即是否有子节点
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-15 10:52:46
     * @lastmodify      2013-01-21 16:23:10 by mrmsl
     *
     * @param int $pk_value 主键值
     *
     * @return bool false有子节点，否则true
     */
    protected function _checkIsLeaf($pk_value) {
        static $data   = null;

        $data = null === $data ? $this->_getCache() : $data;

        if (!$data) {
            return true;
        }

        foreach ($data as $pk => $item) {

            if ($item['parent_id'] == $pk_value) {
                return false;
            }
        }

        return true;
    }

    /**
     * 添加树形式数据，适用于层级关系，有parent_id,node,level字段，如分类，菜单，地区等
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-17 15:22:45
     * @lastmodify      2013-01-21 16:24:27 by mrmsl
     *
     * @param string $diff_key    需要比较差异写日志字段
     * @param string $name_column 模块字段名称。默认''，小写模块名+_name
     *
     * @return void 无返回值
     */
    protected function _commonAddTreeData($diff_key, $name_column = '') {
        $check = $this->_model->startTrans()->checkCreate();//自动创建数据

        true !== $check && $this->_ajaxReturn(false, $check);//未通过验证

        $name_column = $name_column ? $name_column : strtolower(MODULE_NAME) . '_name';//名称字段
        $module_key  = 'MODULE_NAME_' . MODULE_NAME;
        $pk_field    = $this->_pk_field;
        $pk_value    = $this->_model->$pk_field;
        $parent_id   = $this->_model->parent_id;

        if ('Menu' == MODULE_NAME) {//菜单
            $this->_model->_priv_id = map_int($this->_model->_priv_id, true, ADMIN_ROLE_ID);//权限
            $priv_id     = $this->_model->_priv_id;
            $is_menu     = true;
        }

        $data        = $this->_model->getProperty('_data');//数据，$model->data 在save()或add()后被重置为array()
        $msg         = L($pk_value ? 'EDIT' : 'ADD');//添加或编辑
        $log_msg     = $msg . L($module_key . ',FAILURE');//错误日志
        $error_msg   = $msg . L('FAILURE');//错误提示信息
        $cache_data  = $this->_getCache();//缓存数据
        $parent_info = $parent_id && isset($cache_data[$parent_id]) ? $cache_data[$parent_id] : false;

        //父类不存在
        if ($parent_id && !$parent_info) {
            $this->_model->addLog($log_msg . '<br />' . L('INVALID_PARAM,%:,PARENT_' . MODULE_NAME . ",%{$pk_field}({$parent_id}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, $error_msg);
        }

        $data['parent_name'] = $parent_info ? $parent_info[$name_column] : L('TOP_LEVEL_' . MODULE_NAME);//父类

        if ($pk_value) {//编辑

            if (!isset($cache_data[$pk_value]) || (!$info = $cache_data[$pk_value])) {//编辑信息不存在
                $this->_model->addLog($log_msg . '<br />' . L('INVALID_PARAM,%:,' . $module_key . ",%{$pk_field}({$pk_value}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
                $this->_ajaxReturn(false, $error_msg);
            }

            if ($parent_id) {
                $node_arr = explode(',', $parent_info['node']);
                in_array($pk_value, $node_arr) && $this->_ajaxReturn(false, L('SAME_AS_SELF'));//不能作为自己的子类
            }

            if ($this->_model->save() === false) {//更新出错
                $this->_sqlErrorExit($msg . L($module_key) . "{$info[$name_column]}({$pk_value})" . L('FAILURE'), $error_msg);
            }

            $info['parent_name'] = isset($cache_data[$info['parent_id']]) ? $cache_data[$info['parent_id']][$name_column] : L('TOP_LEVEL_' . MODULE_NAME);//父类

            if (isset($is_menu)) {//菜单
                $diff_priv = $this->diffMenuPriv(array_keys($info['priv']), $priv_id);//权限差异
                $diff      = $this->_dataDiff($info, $data, $diff_key) . ($diff_priv['msg'] ? 'priv => ' . $diff_priv['msg'] : '');//差异

                $diff_priv['msg'] && $this->_model->setMenuPriv($pk_value, $priv_id);//权限有变更
            }
            else {
                $diff      = $this->_dataDiff($info, $data, $diff_key);//差异
            }

            $log_msg   = $msg . L($module_key) . "{$info[$name_column]}({$pk_value})." . $diff . L('SUCCESS');//日志信息
            $insert_id = 0;
        }
        else {
            $extra_diff = '';

            if (isset($is_menu)) {//菜单
                $priv = $this->_getMenuPriv($priv_id);
                $extra_diff = ($priv['msg'] ? 'priv => ' . $priv['msg'] : '');

            }

            unset($data[$pk_field]);
            $data = $this->_dataDiff($data, false, $diff_key) . $extra_diff;//数据

            if (($insert_id = $this->_model->add()) === false) {//插入出错
                $this->_sqlErrorExit($msg . L($module_key) . $data . L('FAILURE'), $error_msg);
            }

            $log_msg = $msg . L($module_key) . $data . L('SUCCESS');

            //菜单专有，权限
            isset($is_menu) && $priv_id && $this->_model->setMenuPriv($insert_id, $priv_id);
        }

        $this->_model->setProperty('_data', array('parent_id' => $parent_id, $pk_field => $pk_value, 'insert_id' => $insert_id));
        unset($data);
        $this->_setLevelAndNode();
        $this->_model->commit()->addLog($log_msg, LOG_TYPE_ADMIN_OPERATE);//日志信息
        $this->createAction()->_ajaxReturn(true, $msg . L('SUCCESS'));
    }//end _commonAddTreeData

    /**
     * 比较两个数据之间的差异，适用于修改数据时，记录变化
     *
     * @param array  $old_data 老数据
     * @param array  $new_data 新数据。默认array()
     * @param string $key      比较字段。默认''
     *
     * @return string 差异
     */
    protected function _dataDiff($old_data, $new_data = array(), $key = '') {
        $diff = empty($new_data) ? $old_data : array_udiff_uassoc($old_data, $new_data, create_function('$a, $b', 'return $a == $b ? 0 : 1;'), create_function('$a, $b', 'return $a === $b ? 0 : 1;'));
        $key  = $key ? explode(',', $key) : array_keys($diff);

        foreach($old_data as $k => $v) {

            if (in_array($k, $key) && isset($diff[$k])) {

                if (in_array($k, array('lock_start_time', 'lock_end_time'))) {//时间戳转化为时间格式 by mrmsl on 2012-09-11 08:30:02
                    $diff[$k] = empty($diff[$k]) ? '' : new_date(null, $diff[$k]);
                    $diff[$k] .= '[to]' . (empty($new_data[$k]) ? 'NULL' : new_date(null, $new_data[$k]));
                }
                else {
                    $diff[$k] .= '[to]' . (isset($new_data[$k]) ? ($k == 'password' ? '******' : $new_data[$k]) : 'NULL');
                }
            }
            else {
                unset($diff[$k]);
            }
        }

        return $diff ? preg_replace('/ +/', ' ', stripslashes(str_replace("\n", '', var_export($diff, true)))) : '';
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
     * 获取表字段
     *
     * @return mixed 获取成功，将返回包含字段名的数组，否则false
     */
    protected function _getDbFields() {
        return $this->_model->getDbFields();
    }

    /**
     * 获取菜单树数据
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-15 11:38:13
     * @lastmodify      2013-01-21 16:28:38 by mrmsl
     *
     * @param int  $pk_value      主键值，比如加载某个节点。默认null，$_GET['node']获取
     * @param bool $checked       null树不带复选框。默认null
     * @param bool $include_total true包含总数total键。默认false
     *
     * @return array 菜单树数据
     */
    protected function _getTreeData($pk_value = null, $checked = null, $include_total = false) {
        $pk_value = $pk_value === null ? Filter::int('node', 'get') : $pk_value;

        $data     = array();
        $data_arr = $this->_getCache();
        $total    = 0;

        foreach ($data_arr as $item) {

            if ($item['parent_id'] == $pk_value) {
                $total++;
                $item['checked'] = $checked;
                $item['leaf']    = $this->_checkIsLeaf($item[$this->_pk_field], $data_arr);
                $data[$item[$this->_pk_field]] = $item;
            }
        }

        $data = Tree::array2tree($data, $this->_pk_field);

        return $include_total ? array('data' => $data, 'total' => $total) : $data;
    }

    /**
     * 通过读取缓存，获取具体信息
     *
     *  @return void $this->_info()结果
     */
    function infoAction() {
        return $this->_info(true);
    }

    /**
     * 记录加载css,js时间
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-09-06 17:29:01
     * @lastmodify      2013-01-21 16:42:56 by mrmsl
     *
     * @param string $varname js变量名
     *
     * @return string  script脚本
     */
    protected function _loadTimeScript($varname) {
        return js('var ' . $varname . ' = new Date().getTime();', 'script');
    }

    /**
     * 树查询
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-15 12:31:34
     * @lastmodify      2013-01-21 16:43:17 by mrmsl
     *
     * @param string $column    查询字段
     * @param string $keyword   关键字
     * @param mixed  $int_field 整形字段。默认''
     *
     * @return void 无返回值
     */
     protected function _queryTree($column, $keyword, $int_field = '') {
        $where = $this->_buildMatchQuery($column, $keyword, Filter::string('match_mode', 'get'), $int_field);
        $where = $where ? array($column => $where) : $where;
        $where = !empty($this->_queryTreeWhere) ? array_merge($where, $this->_queryTreeWhere) : $where;//额外条件
        $where && $this->_model->where($where);
        $data  = $this->_model->field($this->_pk_field)->key_column($this->_pk_field)->select();

        method_exists($this, '_queryTreeCallback') && $this->_queryTreeCallback($data);//回调

        $data  = $data ? $this->_bubble(array_keys($data)) : array();

        $this->_ajaxReturn(true, '', $data, $this->_model->getDb()->getProperty('_num_rows'));
    }

    /**
     * 设置表数据缓存
     *
     * @param array  $data  手工设置缓存数据
     * @param string $name  文件名。默认模块名称
     * @param mixed  $model 模型。默认null，当前模型
     * @param string $path  缓存路径。默认MODULE_CACHE_PATH
     *
     * @return object this
     */
    protected function _setCache($data = array(), $name = null, $model = null, $path = MODULE_CACHE_PATH) {
        $name = $name ? $name : $this->_getControllerName();

        if (!$data && $name == $this->_getControllerName() && method_exists($this, '_setCacheData')) {
            $data = $this->_setCacheData();
        }

        if (null === $model) {
            $model = $this->_model;
        }
        else {
            $model = is_string($model) ? D($model) : $model;
        }

        F($name ? $name : $this->_getControllerName(), $data ? $data : $model->key_column($model->getPk())->select(), $path);

        return $this;
    }

    /**
     * 更新某一字段值
     *
     * @param string $field     字段
     * @param mixed  $value     值
     * @param string $msg       操作
     * @param string $extra_log 额外日志
     *
     * @return void 无返回值
     */
    protected function _setField($field, $value, $msg, $extra_log = '') {
        $pk_id = Filter::string($this->_pk_field);//管理员id
        $pk_id = map_int($pk_id, true);//转化成整数

        if (!empty($this->_exclude_setField_id)) {//不能更新id
            $contain_exclude_setField_id = '';
            $this->_exclude_setField_id   = is_array($this->_exclude_setField_id) ? $this->_exclude_setField_id : explode(',', $this->_exclude_setField_id);

            foreach ($pk_id as $k => $v) {

                if (in_array($v, $this->_exclude_setField_id)) {
                    unset($pk_id[$k]);
                    $contain_exclude_setField_id .= ', ' . $v;
                }
            }

            $contain_exclude_setField_id && $this->_model->addLog(L('TRY') . $msg . L('MODULE_NAME') . trim($contain_exclude_setField_id, ', ') . ":{$field}={$value}", LOG_TYPE_INVALID_PARAM);
        }

        $error_msg   = $msg . L('FAILURE');//错误提示信息

        if ($pk_id) {
            $log  = '';//操作日志
            $data = $this->_beforeExec($pk_id, $log, $field, $value);
            $log  = $msg . L('MODULE_NAME') . $log . $extra_log;

            //更新出错
            if ($this->_model->where(array($this->_pk_field => array('IN', $pk_id)))->setField($field, $value) === false) {
                $this->_sqlErrorExit($log . L('FAILURE'), $error_msg);
            }

            $this->_model->addLog($log . L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);//管理员操作日志

            if (!empty($this->_after_exec_cache) && isset($data)) {
                $this->_setCache($data);//生成缓存
            }

            method_exists($this, '_afterSetField') && $this->_afterSetField($field, $value, $pk_id);

            $this->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
        else {//非法参数
            empty($contain_exclude_setField_id) && $this->_model->addLog($msg . L('MODULE_NAME,FAILURE') . '<br />' . L("INVALID_PARAM,%: {$this->_pk_field},IS_EMPTY"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, $error_msg);
        }
    }//end _setField

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
     * 设置字段值为1或0
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-24 14:41:18
     * @lastmodify      2013-03-28 12:16:42 by mrmsl
     *
     * @param string $field 字段。默认is_show
     *
     * @return void 无返回值
     */
    protected function _setOneOrZero($field = 'is_show') {
        $field      = 'isDelete' == $field ? 'is_delete' : $field;
        $status_arr = array(
            'is_show'     => array(0 => 'HIDE', 1 => 'SHOW'),//显示与隐藏
            'is_enable'   => array(0 => 'DISABLED', 1 => 'ENABLE'),//启用与禁用
            'is_delete'   => array(0 => 'CN_WEI,DELETE', 1 => 'CN_YI,DELETE'),//删除与未删除
            'is_issue'    => array(0 => 'CN_WEI,ISSUE',  1 => 'CN_YI,ISSUE'),//发布与未发布
        );

        $value = Filter::int($field) ? 1 : 0;
        $this->_setField($field, $value, L($status_arr[$field][$value]));
    }

    /**
     * 查询数据库出错并exit
     *
     * @param string $content  错误信息。默认''，自动获取最后出错sql
     * @param string $msg      返回错误提示信息。默认''，取$content
     * @param string $db_sql   sql语句。默认''，取当前模型最后sql
     * @param string $db_error sql错误。默认''，取当前db错误
     *
     * @return void 无返回值
     */
    protected function _sqlErrorExit($content = '', $msg = '', $db_sql = '', $db_error = '') {
        $db_sql   = $db_sql ? $sql : $this->_model->getLastSql();
        $db_error = $db_error ? $db_error : $this->_model->getDbError();
        $error = '<br />' . $db_sql . '<br />' . $db_error;

        $this->_ajaxReturn(false, ($msg ? $msg : $content) . (APP_DEBUG ? $error : ''));
    }

    /**
     * 获取树形式具体信息回调，如菜单，地区，分类等
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-17 16:26:19
     * @lastmodify      2013-01-21 16:50:52 by mrmsl
     *
     * @param array $info         具体信息
     * @param string $name_column 模块字段名称。默认''，小写模块名+_name
     *
     * @return void 无返回值
     */
    protected function _treeInfoCallback(&$info, $name_column = '') {
        $data = $this->_getCache();
        $pid  = $info['parent_id'];

        if ('Menu' == MODULE_NAME) {
            $info['_priv_id'] = join(',', array_keys($info['priv']));
            $info['priv'] = join(',', $info['priv']);
        }
        else {
            $info['node'] = substr($info['node'], 0, -strlen($info[$this->_pk_field]) - 1);
        }

        if ($pid && isset($data[$pid])) {
            $info['parent_name'] = $data[$pid][$name_column ? $name_column : strtolower(MODULE_NAME) . '_name'];
        }
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
            $this->_model = D($this->_getControllerName());//模型
            $this->_model->setProperty('_module', $this);
            $this->_pk_field = $this->_model->getPk();//主键字段
        }

        if (defined('APP_INIT')) {//跨模块，直接返回
            return true;
        }

        define('APP_INIT' , true);   //跨模块调用时，不再往下

        $this->setViewpath(THEME_PATH);


        $this->_unshift    = isset($_GET['unshift']);
        $this->_admin_info = Yaf_Registry::get(SESSION_ADMIN_KEY);//管理员信息

        if ($this->_admin_info) {
            //角色信息
            $this->_role_info = $this->_getCache($this->_admin_info['role_id'], 'Role');
        }

        if ($this->_auto_check_priv && (!APP_DEBUG || !__GET)) {//自动检测权限
            $this->_checkAdminPriv();//权限
        }

        L('MODULE_NAME', L('MODULE_NAME_' .  $this->_getControllerName()));//C => L

        return true;
    }//end init

    /**
     * 删除
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-12-28 10:59:38
     * @lastmodify      2013-01-21 16:52:07 by mrmsl
     *
     * @return void 无返回值
     */
    public function deleteAction() {
        $pk_id      = Filter::string($this->_pk_field);//主键id
        $pk_id      = map_int($pk_id, true);//转化成整形

        if (!empty($this->_exclude_delete_id)) {//不能删除id
            $contain_exclude_delete_id = '';
            $this->_exclude_delete_id   = is_array($this->_exclude_delete_id) ? $this->_exclude_delete_id : explode(',', $this->_exclude_delete_id);

            foreach ($pk_id as $k => $v) {

                if (in_array($v, $this->_exclude_delete_id)) {
                    unset($pk_id[$k]);
                    $contain_exclude_delete_id .= ', ' . $v;
                }
            }

            $contain_exclude_delete_id && $this->_model->addLog(L("TRY,DELETE,MODULE_NAME,%{$this->_pk_field}: ") . trim($contain_exclude_delete_id, ', '), LOG_TYPE_INVALID_PARAM);
        }

        $this->_model->startTrans();

        if ($pk_id) {
            $log  = '';//操作日志
            $data = $this->_beforeExec($pk_id, $log);
            $log  = L('DELETE,MODULE_NAME') . $log;

            //删除出错
            if ($this->_model->where(array($this->_pk_field => array('IN', $pk_id)))->delete() === false) {
                $this->_sqlErrorExit($log . L('FAILURE'), L('DELETE,FAILURE'));
            }

            if (!empty($this->_after_exec_cache) && isset($data)) {
                $this->_setCache($data);//生成缓存
            }

            method_exists($this, '_afterDelete') && $this->_afterDelete($pk_id);

            $this->_model->addLog($log . L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);//管理员操作日志

            $this->_ajaxReturn(true, L('DELETE,SUCCESS'));
        }
        else {
            //非法参数
            empty($contain_exclude_delete_id) && $this->_model->addLog(L('DELETE,MODULE_NAME,FAILURE') . '<br />' . L("INVALID_PARAM,%: {$this->_pk_field},IS_EMPTY"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, L('DELETE,FAILURE'));
        }
    }//end deleteAction

    /**
     * 记录加载css,js时间
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-09-06 17:15:16
     * @lastmodify      2013-01-21 16:52:19 by mrmsl
     *
     * @return object this
     */
    public function logLoadTimeAction() {

        if (!$this->_admin_info) {
            $this->_model->addLog(get_class($this) . '->' . __FUNCTION__ . '. admin_info is empty', LOG_TYPE_INVALID_PARAM);
            return $this;
        }

        $load_css_time   = Filter::float('load_css_time');//加载css样式时间
        $load_ext_time   = Filter::float('load_ext_time');//加载extjs.js时间
        $load_js_time    = Filter::float('load_js_time');//加载其它js时间
        $app_launch_time = Filter::float('app_launch_time');//创建应用程序时间
        $total_time      = $load_css_time + $load_ext_time + $load_js_time + $app_launch_time;
        $app_launch_time = $app_launch_time ? ', app_launch_time => ' . $app_launch_time : '';//管理中心才会有

        $this->_model->addLog("total_time => {$total_time}{$app_launch_time}, load_ext_time => {$load_ext_time}, load_css_time => {$load_css_time}, load_js_time => {$load_js_time}", LOG_TYPE_LOAD_SCRIPT_TIME);

        return $this;
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
    public function nav($id, $name_field, $filename = null, $separator = '»') {
        $nav  = array();
        $data = $this->_getCache(0, $filename ? $filename : $this->_getControllerName());
        $info = $data[$id];

        foreach(explode(',', $info['node']) as $item) {
            $nav[] = $data[$item][$name_field];
        }

        return join($separator, $nav);
    }

    /**
     * 设置管理员session
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-06-20 15:58:42
     * @lastmodify      2013-01-21 16:53:20 by mrmsl
     *
     * @param array $admin_info 管理员信息
     *
     * @return bool true设置成功，否则false
     */
    public function setAdminSession($admin_info = array()) {

        if (!$admin_info) {
            return false;
        }

        session(SESSION_ADMIN_KEY, $admin_info);
        Yaf_Registry::set(SESSION_ADMIN_KEY, $admin_info);

        return true;
    }

    /**
     * 生成缓存
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-12-28 11:39:15
     * @lastmodify      2013-01-21 16:54:27 by mrmsl
     *
     * @return void 无返回值
     */
    public function setCacheAction() {
        $this->_setCache();
    }
}