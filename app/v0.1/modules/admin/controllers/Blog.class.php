<?php
/**
 * 博客控制器类
 *
 * @file            Blog.class.php
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-03-23 11:21:07
 * @lastmodify      $Date$ $Author$
 */

class BlogController extends BaseController {
    /**
     * @var array $_priv_map 权限映射，如'delete' => 'add'删除权限映射至添加权限
     */
    protected $_priv_map           = array(
        'delete'   => 'add',//删除
        'info'     => 'add',//具体信息
        'issue'    => 'add',//发布状态
        'isdelete' => 'add',//删除状态
    );

    /**
     * {@inheritDoc}
     */
    protected function _beforeExec(&$pk_id, &$log) {
        $log = join(', ', $pk_id);//操作日志

        return null;
    }

    /**
     * {@inheritDoc}
     */
    protected function _infoCallback(&$cate_info) {
        $cate_info['add_time'] = new_date(sys_config('sys_timezone_datetime_format'), $cate_info['add_time']);
        $cate_info['cate_name'] = $this->_getCache($cate_info['cate_id'] . '.cate_name', 'Category');
    }

    /**
     * 添加或保存
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-12-26 15:57:19
     * @lastmodify      2013-01-21 15:45:31 by mrmsl
     *
     * @return void 无返回值
     */
    public function addAction() {
        $check     = $this->_model->checkCreate();//自动创建数据

        $check !== true && $this->_ajaxReturn(false, $check);//未通过验证

        $pk_field  = $this->_pk_field;//主键
        $pk_value  = $this->_model->$pk_field;//博客id
        $data      = $this->_model->getProperty('_data');//数据，$model->data 在save()或add()后被重置为array()
        $diff_key  = 'title,content,cate_name,is_issue,seo_keyword,seo_description,sort_order,is_delete';//比较差异字段
        $msg       = L($pk_value ? 'EDIT' : 'ADD');//添加或编辑
        $log_msg   = $msg . L('MODULE_NAME_BLOG,FAILURE');//错误日志
        $error_msg = $msg . L('FAILURE');//错误提示信息
        $cate_info = $this->_getCache($cate_id = $this->_model->cate_id, 'Category');//所属分类

        $data['cate_name'] = $cate_info['cate_name'];//所属分类名称

        if ($pk_value) {//编辑

            if (!$blog_info = $this->_model->find($pk_value)) {//编辑博客不存在
                $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%:,MODULE_NAME_BLOG,%{$pk_field}({$pk_value}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
                $this->_ajaxReturn(false, $error_msg);
            }

            if (false === $this->_model->save()) {//更新出错
                $this->_sqlErrorExit($msg . L('MODULE_NAME_BLOG') . "{$blog['title']}({$pk_value})" . L('FAILURE'), $error_msg);
            }

            $cate_info = $this->_getCache($blog_info['cate_id'], 'Category');
            $blog_info['cate_name'] = $cate_info['cate_name'];//所属分类名

            $diff = $this->_dataDiff($blog_info, $data, $diff_key);//差异

            $this->_model->addLog($msg . L('MODULE_NAME_BLOG')  . "{$blog_info['title']}({$pk_value})." . $diff. L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
        else {
            $data = $this->_dataDiff($data, false, $diff_key);//数据

            if (false === $this->_model->add()) {//插入出错
                $this->_sqlErrorExit($msg . L('MODULE_NAME_BLOG') . $data . L('FAILURE'), $error_msg);
            }

            $this->_model->addLog($msg . L('MODULE_NAME_BLOG') . $data . L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
    }//end addAction

    /**
     * 获取博客具体信息
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-26 12:14:15
     *
     * @return $this->_info()结果
     */
    function infoAction() {
        return $this->_info(false);
    }

    /**
     * 删除状态
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:32:41
     *
     * @return void 无返回值
     */
    public function isDeleteAction() {
        $this->_setOneOrZero('isDelete');
    }

    /**
     * 发布状态
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:32:41
     *
     * @return void 无返回值
     */
    public function issueAction() {
        $this->_setOneOrZero('is_issue');
    }

    /**
     * 管理员列表
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-12-26 14:22:09
     * @lastmodify      2013-03-31 19:03:13 by mrmsl
     *
     * @return void 无返回值
     */
    public function listAction() {
        $db_fields      = $this->_getDbFields();//表字段
        $sort           = Filter::string('sort', 'get', $this->_pk_field);//排序字段
        $sort           = in_array($sort, $db_fields) ? $sort : $this->_pk_field;
        $order          = empty($_GET['dir']) ? Filter::string('order', 'get') : Filter::string('dir', 'get');//排序
        $order          = toggle_order($order);
        $keyword        = Filter::string('keyword', 'get');//关键字
        $date_start     = Filter::string('date_start', 'get');//注册开始时间
        $date_end       = Filter::string('date_end', 'get');//注册结束时间
        $cate_id        = Filter::int('cate_id', 'get');//所属管理组
        $column         = Filter::string('column', 'get');//搜索字段
        $is_delete      = Filter::int('is_delete', 'get');//删除
        $is_issue       = Filter::int('is_issue', 'get');//状态
        $where          = array();

        if ($keyword !== '' && in_array($column, array('title', 'seo_keyword', 'seo_description', 'content', 'from_name', 'from_url'))) {
            $where['' . $column] = $this->_buildMatchQuery('' . $column, $keyword, Filter::string('match_mode', 'get'));
        }

        if ($date_start && ($date_start = strtotime($date_start))) {
            $where['add_time'][] = array('EGT', $date_start);
        }

        if ($date_end && ($date_end = strtotime($date_end))) {
            $where['add_time'][] = array('ELT', $date_end);
        }

        if (isset($where['add_time']) && count($where['add_time']) == 1) {
            $where['add_time'] = $where['add_time'][0];
        }

        if (-1 != $is_delete) {//删除
            $where['is_delete'] = $is_delete;
        }

        if (-1 != $is_issue) {//状态
            $where['is_issue'] = $is_issue;
        }

        if ($cate_id) {
            $where['cate_id'] = $cate_id;
        }

        $total      = $this->_model->where($where)->count();

        if ($total === false) {//查询出错
            $this->_sqlErrorExit(L('QUERY,MODULE_NAME_BLOG') . L('TOTAL_NUM,ERROR'));
        }
        elseif ($total == 0) {//无记录
            $this->_ajaxReturn(true, '', null, $total);
        }

        $page_info = Filter::page($total);
        $data      = $this->_model
        ->where($where)->field('content', true)
        ->limit($page_info['limit'])
        ->order(('' .$sort) . ' ' . $order)->select();

        $data === false && $this->_sqlErrorExit(L('QUERY,MODULE_NAME_BLOG') . L('LIST,ERROR'));//出错

        $cate_arr = $this->_getCache(false, 'Category');

        foreach($data as &$v) {
            $v['cate_name'] = $cate_arr[$v['cate_id']]['cate_name'];
        }

        $this->_ajaxReturn(true, '', $data, $total);
    }//end listAction

    /**
     * 移动所属分类
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-31 19:27:28
     *
     * @return void 无返回值
     */
    function moveAction() {
        $field       = 'cate_id';//定段
        $cate_id     = Filter::int($field);//所属分类id
        $msg         = L('MOVE');//提示
        $log_msg     = $msg . L('MODULE_NAME_BLOG,FAILURE');//错误日志
        $error_msg   = $msg . L('FAILURE');//错误提示信息

        if ($cate_id) {//分类id
            $cate_info = $this->_getCache($cate_id, 'Category');

            if (!$cate_info) {//分类不存在
                $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%:,BELONG_TO_CATEGORY,%{$field}({$cate_id}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
                $this->_ajaxReturn(false, $error_msg);
            }

            $cate_name = $cate_info['cate_name'];
        }
        else {
            //非法参数
            $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%: {$field},IS_EMPTY"), LOG_TYPE_INVALID_PARAM);
            $this->_ajaxReturn(false, $error_msg);
        }

        $this->_setField($field, $cate_id, $msg, L('TO') . $cate_name);
    }//end moveAction
}