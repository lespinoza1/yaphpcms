<?php
/**
 * 表单域控制器类
 *
 * @file            Field.class.php
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-08-01 16:37:11
 * @lastmodify      $Date$ $Author$
 */

//namespace Yap\Module\Admin\Controller;

/**
 * 表单域控制器类
 *
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-08-01 16:37:11
 * @lastmodify      $Date$ $Author$
 */

class FieldController extends BaseController {
    /**
     * @var bool $_after_exec_cache true删除后调用BaseController->_setCache()生成缓存， BaseController->delete()会用到。默认true
     */
    protected $_after_exec_cache   = true;
    /**
     * @var bool $_get_children_ids true取所有子表单， BaseController->delete()会用到。默认false
     */
    protected $_get_children_ids   = false;
    /**
     * @var string $_name_column 名称字段 BaseController->delete()会用到。默认field_name
     */
    protected $_name_column        = 'field_name';
    /**
     * @var array $_priv_map 权限映射，如'delete' => 'add'删除权限映射至添加权限
     */
    protected $_priv_map           = array(
        'delete'   => 'add',//删除
        'info'     => 'add',//具体信息
        'enable'   => 'add',//显示隐藏
    );

    /**
     * 获取通用Extjs 表单域代码
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-09-22 14:54:38
     * @lastmodify      2013-01-22 10:21:03 by mrmsl
     *
     * @param string $key   代码类型，如verifycode_enable验证码是否启用
     * @param mixed  $extra 额外参数，默认''
     *
     * @return string Extjs 表单域代码
     */
    private function _fieldCode($key, $extra = '') {
        $default = "'<a class=\"a-font-000\" href=\"#' + this.getAction('system','verifycode') + '\">' + lang('SYSTEM,DEFAULT,VALUE') + '</a>'";

        $js_arr = array(
        //验证码启用
        'verifycode_enable' => "
         extField.fieldContainer('%@fieldLabel',
            [
             extField.checkbox('@input_name', '', '', 'ENABLE', 1, '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'DISABLED', 0, '', {xtype: 'radio'})
             " . ($extra ? ",extField.checkbox('@input_name', '', '', '%' + {$default}, -1, '', {xtype: 'radio'})" : '') . "
            ], true, {
             xtype: 'radiogroup',
                value: '@value' ? {'@input_name': '@value'} : false,
                columns: 1,
                vertical: true,
                name: '@input_name'
   })",
        //验证码宽度
        'verifycode_width'  => "extField.fieldContainer(['%@fieldLabel', [['numberField', '@input_name', 'PLEASE_ENTER,%@field_name', '', '@value', {minValue: 0, maxValue: 100}], lang('UNIT') + '：px' + @tip], true])",
        //验证码高度
        'verifycode_height' => "extField.fieldContainer(['%@fieldLabel', [['numberField', '@input_name', 'PLEASE_ENTER,%@field_name', '', '@value', {minValue: 0, maxValue: 50}], lang('UNIT') + '：px' + @tip], true])",
        //验证码长度
  'verifycode_length' => "extField.fieldContainer(['%@fieldLabel', [['numberField', '@input_name', 'PLEASE_ENTER,%@field_name', '', '@value', {minValue: 0, maxValue: 10}], lang('UNIT') + '：px' + @tip], true])",
        //验证码顺序
  'verifycode_order'  => "extField.fieldContainer(['%@fieldLabel', [[null, '@input_name', 'PLEASE_ENTER,%@field_name', '', '@value'], lang('VERIFY_CODE_ORDER_TIP')" . ($extra ? " + lang('%。-1,MEAN,CN_QU') + {$default}" : '') . "], true, {vertical: true}])",
        //验证码刷新限制
  'verifycode_refresh_limit'  => "
  extField.fieldContainer(['%@fieldLabel', [
            [null,'@input_name','', '', '@value', {size: 10}],
            lang('VERIFY_CODE_REFRESH_LIMIT_TIP')" . ($extra ? " + lang('%。,KEEP_BLANK,CN_QU') + {$default}" : '') . "
        ], true])",
        //验证码错误限制
  'verifycode_error_limit'  => "
  extField.fieldContainer(['%@fieldLabel', [
            [null,'@input_name','', '', '@value', {size: 10}],
            lang('VERIFY_CODE_ERROR_LIMIT_TIP')" . ($extra ? " + lang('%。,KEEP_BLANK,CN_QU') + {$default}" : '') . "
        ], true])",
        //验证码区分大小写
        'verifycode_case' => "
         extField.fieldContainer('%@fieldLabel',
            [
             extField.checkbox('@input_name', '', '', 'DIFFERENTIATE', 1, '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'NO,DIFFERENTIATE', 0, '', {xtype: 'radio'}),
             " . ($extra ? "extField.checkbox('@input_name', '', '', '%' + {$default}, -1, '', {xtype: 'radio'})" : '') . "
            ], true, {
             xtype: 'radiogroup',
                value: '@value' ? {'@input_name': '@value'} : false,
                columns: 1,
                vertical: true,
                name: '@input_name'
   })",
        //验证码类型
        'verifycode_type' => "
         extField.fieldContainer('%@fieldLabel',
            [
             extField.checkbox('@input_name', '', '', 'VERIFY_CODE_TYPE_LETTERS', lang('VERIFY_CODE_TYPE_LETTERS_VALUE'), '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'VERIFY_CODE_TYPE_LETTERS_UPPER', lang('VERIFY_CODE_TYPE_LETTERS_UPPER_VALUE'), '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'VERIFY_CODE_TYPE_LETTERS_LOWER', lang('VERIFY_CODE_TYPE_LETTERS_LOWER_VALUE'), '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'VERIFY_CODE_TYPE_NUMERIC', lang('VERIFY_CODE_TYPE_NUMERIC_VALUE'), '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'VERIFY_CODE_TYPE_ALPHANUMERIC', lang('VERIFY_CODE_TYPE_ALPHANUMERIC_VALUE'), '', {xtype: 'radio'}),
             extField.checkbox('@input_name', '', '', 'VERIFY_CODE_TYPE_ALPHANUMERIC_EXTEND', lang('VERIFY_CODE_TYPE_ALPHANUMERIC_EXTEND_VALUE'), '', {xtype: 'radio'})
             " . ($extra ? ",extField.checkbox('@input_name', '', '', '%' + {$default}, -1, '', {xtype: 'radio'})" : '') . "
            ], true, {
             xtype: 'radiogroup',
                value: '@value' ? {'@input_name': '@value'} : false,
                columns: 1,
                vertical: true,
                name: '@input_name'
   })"
        );

        return isset($js_arr[$key]) ? $js_arr[$key] : '';
    }//end _fieldCode

    /**
     * 保存模块设置回调
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-09-22 15:34:37
     * @lastmodify      2013-01-22 10:22:11 by mrmsl
     *
     * @param array $menu_info  菜单信息
     *
     * @return void 无返回值
     */
    private function _saveValueCallbackModule($menu_info) {
        $menu_info  = is_int($menu_info) ? $this->_getCache($menu_info, 'Menu') : $menu_info;
        $controller = $menu_info['controller'];//控制器
        $cache_key  = ucfirst($controller);
        $node_arr   = explode(',', $menu_info['node']);
        $parent_id  = $node_arr[count($node_arr) - 2];//父级菜单id
        $menu_ids   = $this->_getChildrenIds($parent_id, false, false, 'Menu');
        $data       = $this->_model->where("menu_id IN({$menu_ids}) AND is_enable=1")->getField('input_name,input_value');
        $this->_setCache($data, $cache_key);
    }

    /**
     * 保存系统设置回调
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-30 17:36:36
     * @lastmodify      2013-02-01 11:06:14 by mrmsl
     *
     * @param array $menu_info 菜单信息
     *
     * @return void 无返回值
     */
    private function _saveValueCallbackSystem($menu_info) {
        $menu_info  = is_int($menu_info) ? $this->_getCache($menu_info, 'Menu') : $menu_info;
        $controller = $menu_info['controller'];//控制器
        $cache_key  = ucfirst($controller);
        $node_arr   = explode(',', $menu_info['node']);
        $parent_id  = $node_arr[count($node_arr) - 2];//父级菜单id
        $menu_ids   = $this->_getChildrenIds($parent_id, false, true, 'Menu');
        //$data       = $this->_model->where("menu_id IN({$menu_ids}) AND is_enable=1")->field('input_name,input_value,customize_1,is_enable')->select();
        //走缓存 by mrmsl on 2012-09-10 09:49:03
        $menu_ids   = var_export($menu_ids, true);
        $data       = array_filter($this->_getCache(false, null, true), create_function('$v', 'return in_array($v["menu_id"],' . $menu_ids . ') && $v["is_enable"];'));

        if (empty($data)) {//空数据，不修改，直接返回
            return;
        }

        $system_data = array();
        $js_data     = array();

        foreach ($data as $item) {
            $input_name  = $item['input_name'];
            $input_value = $item['input_value'];
            $system_data[$input_name] = $input_value;

            if ($item['customize_1']) {//js数据
                $js_data[$input_name] = $input_value;
            }
        }

        $system_data['sys_base_domain_scope'] = substr($system_data['sys_base_domain'], strpos($system_data['sys_base_domain'], '.'));//
        $system_data['sys_base_website'] = $system_data['sys_base_http_protocol'] . '://' . $system_data['sys_base_domain'] . '/';//网站url

        $this->_setCache($system_data, $cache_key);

        $js_data['IS_LOCAL'] = IS_LOCAL;
        $js_data['sys_base_website'] = $system_data['sys_base_website'];//网站url
        $js_data['sys_base_admin_entry'] = $system_data['sys_base_http_protocol'] . '://' . $system_data['sys_base_domain'] . $system_data['sys_base_wwwroot'] . $system_data['sys_base_admin_entry'];//后台管理入口
        $js_data['sys_base_domain_scope'] = $system_data['sys_base_domain_scope'];//cookie作用域
        $js_data['sys_cookie_domain'] = $system_data['sys_cookie_domain'] == '@domain' ? $system_data['sys_base_domain_scope'] : $system_data['sys_cookie_domain'];//cookie域名
        array2js($js_data, $cache_key, WWWROOT . $system_data['sys_base_js_path'] . $cache_key . '.js');
    }//end _saveValueCallbackSystem

    /**
     * {@inheritDoc}
     */
    protected function _infoCallback(&$info) {

        if ($menu_info = $this->_getCache($info['menu_id'], 'Menu')) {
            $info['menu_name'] = $menu_info['menu_name'];
        }
    }

    /**
     * 设置写缓存数据
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-09-05 14:20:47
     * @lastmodify      2013-01-22 10:29:01 by mrmsl
     *
     * @return mixed 查询成功，返回数组，否则返回false
     */
    protected function _setCacheData() {
        return $this->_model->order('is_enable DESC,sort_order ASC, field_id ASC')->key_column($this->_pk_field)->select();
    }

    /**
     * 添加或编辑
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-01 16:57:05
     * @lastmodify      2013-01-22 10:29:20 by mrmsl
     *
     * @return void 无返回值
     */
    public function addAction() {
        $check     = $this->_model->checkCreate();//自动创建数据

        $check !== true && $this->_ajaxReturn(false, $check);//未通过验证
        $module_key= 'MODULE_NAME_' . MODULE_NAME;
        $pk_field  = $this->_pk_field;//主键
        $pk_value  = $this->_model->$pk_field;//管理员id
        $data      = $this->_model->getProperty('_data');//数据，$model->data 在save()或add()后被重置为array()
        $diff_key  = 'field_name,field_code,validate_rule,input_name,menu_name,is_enable,sort_order,memo,customize_1';//比较差异字段
        $msg       = L($pk_value ? 'EDIT' : 'ADD');//添加或编辑
        $log_msg   = $msg . L($module_key . ',FAILURE');//错误日志
        $error_msg = $msg . L('FAILURE');//错误提示信息

        if (!$menu_info = $this->_getCache($menu_id = $this->_model->menu_id, 'Menu')) {//菜单不存在
            $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%:,PARENT_FIELD,%menu_id({$menu_id}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, $error_msg);
        }

        $data['menu_name'] = $menu_info['menu_name'];//菜单名

        if ($pk_value) {//编辑

            if (!$field_info = $this->_getCache($pk_value)) {//表单域不存在
                $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%:,MODULE_NAME_ADMIN,%{$pk_field}({$pk_value}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
                $this->_ajaxReturn(false, $error_msg);
            }

            if ($this->_model->save() === false) {//更新出错
                $this->_sqlErrorExit($msg . L($module_key) . "{$field_info[$this->_name_column]}({$pk_value})" . L('FAILURE'), $error_msg);
            }

            $menu_info = $this->_getCache($field_info['menu_id'], 'Role');
            $field_info['menu_name'] = $menu_info['menu_name'];//菜单名

            $diff = $this->_dataDiff($field_info, $data, $diff_key);//差异
            $this->_model->addLog($msg . L($module_key)  . "{$field_info[$this->_name_column]}({$pk_value})." . $diff. L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_setCache()->_ajaxReturn(true, $msg . L('SUCCESS'));

        }
        else {
            $data = $this->_dataDiff($data, false, $diff_key);//数据

            if ($this->_model->add() === false) {//插入出错
                $this->_sqlErrorExit($msg . L($module_key) . $data . L('FAILURE'), $error_msg);
            }

            $this->_model->addLog($msg . L($module_key) . $data . L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_setCache()->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
    }//end addAction

    /**
     * 启用/禁用
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-01 16:55:45
     * @lastmodify      2013-01-22 10:29:33 by mrmsl
     *
     * @return void 无返回值
     */
    public function enableAction() {
        $this->_setOneOrZero('is_enable');
    }

    /**
     * 列表管理
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-01 16:53:52
     * @lastmodify      2013-01-22 10:29:43 by mrmsl
     *
     * @return void 无返回值
     */
    public function listAction() {
        $sort     = Filter::string('sort', 'get', $this->_pk_field);//排序字段
        $sort     = in_array($sort, $this->_getDbFields()) ? $sort : $this->_pk_field;
        $order    = !empty($_GET['dir']) ? Filter::string('dir', 'get') : Filter::string('order', 'get');//排序
        $order    = toggle_order($order);
        $column   = Filter::string('column', 'get');//搜索字段
        $keyword  = Filter::string('keyword', 'get');//搜索关键字
        $menu_id  = Filter::int('menu_id', 'get');//所属菜单
        $is_enable= Filter::int('is_enable', 'get');//是否启用 by mrmsl on 2012-09-15 02:18:18
        $where    = array();

        if ($menu_id) {
            //getChildrenIds($item_id, $include_self = true, $return_array = false, $filename = null, $level_field = 'level', $node_field = 'node') {
            $menu_id = $this->_getChildrenIds($menu_id, true, false, 'Menu');
            $menu_id ? $where['a.menu_id'] = array('IN', $menu_id) : '';
        }

        if ($keyword !== '' && in_array($column, array($this->_name_column, 'field_code', 'validate_rule', 'input_name'))) {
            $where['a.' . $column] = $this->_buildMatchQuery('a.' . $column, $keyword, Filter::string('match_mode', 'get'));
        }

        if ($is_enable != -1) {//启用状态 by mrmsl on 2012-09-15 02:20:43
            $where['a.is_enable'] = array('EQ', $is_enable);
        }

        $total      = $this->_model->alias('a')->where($where)->count();

        if ($total === false) {//查询出错
            $this->_sqlErrorExit(L('QUERY,MODULE_NAME_ADMIN') . L('TOTAL_NUM,ERROR'));
        }
        elseif ($total == 0) {//无记录
            $this->_ajaxReturn(true, '', null, $total);
        }

        $page_info = Filter::page($total);
        $data      = $this->_model->alias('a')->join('JOIN ' . TB_MENU . ' AS m ON a.menu_id=m.menu_id')->where($where)->field('a.*,m.menu_name')->limit($page_info['limit'])->order('a.' .$sort . ' ' . $order)->select();

        $data === false && $this->_sqlErrorExit(L('QUERY,MODULE_NAME_FIELD') . L('LIST,ERROR'));//出错

        $this->_ajaxReturn(true, '', $data, $total);

        //搜索
        if (!$field_id && $column && $keyword && in_array($column, array($this->_name_column, 'field_code', 'field_value'))) {
            $this->_queryTree($column, $keyword);
        }
        elseif ($field_id) {
            $this->_ajaxReturn(true, '', $this->_getTreeData($field_id, false));
        }

        $data = $this->_getCache(0, MODULE_NAME . '_tree');
        $this->_ajaxReturn(true, '', $data, count($this->_getCache()));
    }//end listAction

    /**
     * 所属表单
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-01 16:58:59
     * @lastmodify      2013-01-22 10:29:57 by mrmsl
     *
     * @return void 无返回值
     */
    public function publicFieldAction() {
        $field_id = Filter::int('node', 'get');
        $data    = $this->_getTreeData($field_id, 'nochecked');

        //增加顶级表单
        $this->_unshift && !$field_id && array_unshift($data, array('field_id' => 0, $this->_name_column => L('TOP_LEVEL_FIELD'), 'leaf' => true));

        $this->_ajaxReturn(true, '', $data);
    }

    /**
     * 加载表单域
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-27 14:06:37
     * @lastmodify      2013-01-22 10:30:15 by mrmsl
     *
     * @return void 无返回值
     */
    public function publicFormAction() {
        $controller = Filter::string('controller', 'get');//控制器
        $action     = Filter::string('action', 'get');//操作方法
        $callback   = Filter::string('callback', 'get');//jsonp callback
        $error_msg  = L('GET,MODULE_NAME_FIELD,DATA,FAILURE') . "controller={$action}&action={$action}";

        if ($controller && $action) {
            $this->_checkAdminPriv($controller, $action);//权限判断 by mashanlin on 2012-08-30 11:04:14

            $data   = $this->_model->alias('f')->field('f.field_id,f.menu_id,f.field_code,f.input_value,f.field_name,f.input_name,m.controller,m.action')
            ->join(TB_MENU . ' AS m ON f.menu_id=m.menu_id')
            ->where("m.controller='{$controller}' AND m.action='{$action}' AND f.is_enable=1")->
            order('f.sort_order ASC,f.field_id ASC')->select();
            $field  = array();

            $data === false && $this->_sqlErrorExit($error_msg);

            foreach ($data as $item) {
                $input_name = $item['input_name'];//输入框名称
                $field_name = $item['field_name'];//表单域名
                $field_code = $item['field_code'];//js代码

                if (strpos($field_code, 'verifycode_') === 0) {//验证码字段 by mrmsl on 2012-09-22 14:39:17
                    $_arr = explode('@', $field_code);
                    $field_code = $this->_fieldCode($_arr[0], isset($_arr[1]));

                    if (!$field_code) {
                        continue;
                    }

                    if (!isset($_arr[1])) {//无提示
                        $field_code = str_replace('@tip', "''", $field_code);
                    }
                    else {//验证码提示
                        $field_code = str_replace('@tip', "lang('%。0,MEAN,CN_QU,SYSTEM') + '<a class=\"a-font-000\" href=\"#' + this.getAction('system','verifycode') + '\">' + lang('SYSTEM,DEFAULT,VALUE') + '</a>'", $field_code);
                    }
                }

                $find       = array('@fieldLabel', '@field_name', '@input_name', '@value');
                $field_label= sprintf('<a class="a-font-000" href="#controller=field&action=add&field_id=%d&back=%s">%s</a>', $item['field_id'], urlencode("#controller={$controller}&action={$action}"), $field_name, $input_name);
                $replace    = array($field_label, $field_name, $input_name, $item['input_value']);
                $field_code = trim(str_ireplace($find, $replace, $field_code));
                $field[]    = strpos($field_code, 'extField.') === 0 ? $field_code : '{' . $field_code . '}';
            }//end foreach

            if (isset($item)) {
                $field[] = "{xtype: 'hidden', name: '_menu_id', value: {$item['menu_id']}}";
            }

            $field = "{$callback}(function () {var extField = Yap.Field.field();return [" . join(',' . EOL_LF . EOL_LF, $field) . '];})';
            exit($field);
        }
        else {
            $this->_model->addLog($error_msg, LOG_TYPE_INVALID_PARAM);
            send_http_status(HTTP_STATUS_SERVER_ERROR);
            $this->_ajaxReturn(false);
        }
    }//end publicFormAction

   /**
     * 保存值
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-08-29 13:47:39
     * @lastmodify      2013-01-22 10:30:42 by mrmsl
     *
     * @return void 无返回值
     */
    public function publicSaveValueAction() {
        $error   = L('SAVE,FAILURE');//保存失败错误
        $menu_id = Filter::int('_menu_id');//菜单id
        $menu    = $this->_getCache(0, 'Menu');//菜单数据

        if (!isset($menu[$menu_id])) {//菜单不存在
            $this->_model->addLog(L("SAVE,MODULE_NAME_FIELD,VALUE,FAILURE,%:(,MENU,%menu_id={$menu_id}}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, $error);
        }

        $menu_info  = $menu[$menu_id];//菜单信息
        $controller = $menu_info['controller'];//控制器
        $action     = $menu_info['action'];//操作方法

        $this->_checkAdminPriv($controller, $action);//权限判断 by mashanlin on 2012-08-30 11:06:25

        $menu = $this->nav($menu_id, 'menu_name', 'Menu');//菜单名
        $info = L('MODULE_NAME_FIELD,VALUE') . "({$menu})";//信息

        if (empty($_POST)) {//非法数据
            $this->_model->addLog(L('SAVE') . $info . L('FAILURE,%:,INVALID,DATA'), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, $error);
        }

        /*$field_arr  = $this->_model->alias('f')
        ->field('f.input_name,f.field_id,f.field_name,input_value,f.validate_rule,f.auto_operation')
        ->join(TB_MENU . ' AS m ON f.menu_id=m.menu_id')
        ->where("m.menu_id={$menu_id} AND f.is_enable=1")
        ->key_column($this->_pk_field)->select();*/
        //走缓存 by mrmsl on 2012-09-05 14:05:14
        $field_arr  = array_filter($this->_getCache(), create_function('$v', 'return $v["menu_id"] == ' . $menu_id . ' && $v["is_enable"];'));

        if (empty($field_arr)) {//查询出错或表单域为空

            if ($field_arr === false) {//查询出错
                $this->_sqlErrorExit(L('GET') . $menu . L('MODULE_NAME_FIELD,FAILURE'), $error);
            }
            else {
                $this->_model->addLog(L('SAVE') . $info . L('FAILURE,%:,MODULE_NAME_FIELD,IS_EMPTY'), LOG_TYPE_INVALID_PARAM);
            }

            $this->_ajaxReturn(false, $error);
        }

        $this->_model->saveValueCheckCreate($field_arr);//设置自动验证

        $checked = $this->_model->checkCreate('_validateSaveValue');//执行自动验证

        $checked !== true && $this->_ajaxReturn(false, $checked);//未通过验证

        $this->_model->autoOperation($_POST, Model::MODEL_BOTH);//自动填充 by mrmsl on 2012-09-07 13:07:57

        $log           = '';//管理日志
        $pk_field      = $this->_pk_field;//主键

        foreach ($field_arr as $field_id => $item) {
            $input_name  = $item['input_name'];

            if (isset($_POST[$input_name])) {
                $old_value = $item['input_value'];//原值
                $new_value = $_POST[$input_name];//新值

                if ($old_value != $new_value) {//值不相等
                    $this->_model->save(array($pk_field => $field_id, 'input_value' => $new_value));//更新
                    $log .= ", {$input_name}: {$old_value} => {$new_value}";//管理日志
                }
            }
        }

        $this->_setCache();//重新生成缓存
        //回调 by mrmsl on 2012-09-22 15:34:53
        method_exists($this, ($callback = '_saveValueCallback' . ucfirst($controller))) && $this->$callback($menu_info);

        $this->_model->addLog(L('SAVE') . $info . L('SUCCESS') . ($log ? $log : ''), LOG_TYPE_ADMIN_OPERATE);
        $this->_ajaxReturn(true, L('SAVE,SUCCESS'));
    }//end publicSaveValueAction
}