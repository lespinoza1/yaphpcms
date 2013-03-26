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
        'show'     => 'add',//显示隐藏
    );

    /**
     * {@inheritDoc}
     */
    protected function _infoCallback(&$cate_info) {
        $cate_info['add_time'] = new_date(sys_config('sys_timezone_datetime_format'), $cate_info['add_time']);
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
        $diff_key  = 'title,cate_name,status,seo_keyword,seo_descriptions,sort_order';//比较差异字段
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
     * 管理员列表
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2012-12-26 14:22:09
     * @lastmodify      2013-01-21 15:46:28 by mrmsl
     *
     * @return void 无返回值
     */
    public function listAction() {
        $db_fields      = $this->_getDbFields();//表字段
        $db_fields      = array_filter($db_fields, create_function('$v', 'return strpos($v, "_") !== 0;'));//过滤_开头
        $sort           = Filter::string('sort', 'get', $this->_pk_field);//排序字段
        $sort           = in_array($sort, $db_fields) || $sort == 'is_lock' ? $sort : $this->_pk_field;
        $order          = empty($_GET['dir']) ? Filter::string('order', 'get') : Filter::string('dir', 'get');//排序
        $order          = toggle_order($order);
        $keyword        = Filter::string('keyword', 'get');//关键字
        $date_start     = Filter::string('date_start', 'get');//注册开始时间
        $date_end       = Filter::string('date_end', 'get');//注册结束时间
        $cate_id        = Filter::int('cate_id', 'get');//所属管理组
        $column         = Filter::string('column', 'get');//搜索字段
        $is_lock        = Filter::int('is_lock', 'get');//锁定
        $is_restrict    = Filter::int('is_restrict', 'get');//绑定登陆 by mrmsl on 2012-09-15 11:53:58
        $where          = array();

        if ($keyword !== '' && in_array($column, array('username', 'realname'))) {
            $where['a.' . $column] = $this->_buildMatchQuery('a.' . $column, $keyword, Filter::string('match_mode', 'get'));
        }

        if ($date_start && ($date_start = local_strtotime($date_start))) {
            $where['a.add_time'][] = array('EGT', $date_start);
        }

        if ($date_end && ($date_end = local_strtotime($date_end))) {
            $where['a.add_time'][] = array('ELT', $date_end);
        }

        if (isset($where['a.add_time']) && count($where['a.add_time']) == 1) {
            $where['a.add_time'] = $where['a.add_time'][0];
        }

        if ($is_lock == 0) {//未锁定 by mrmsl on 2012-09-15 11:26:36
            $where['a.lock_end_time'] = array('ELT', APP_NOW_TIME);
        }
        elseif ($is_lock == 1) {//未锁定 by mrmsl on 2012-09-15 11:26:44
            $where['a.add_time'] = array('ELT', APP_NOW_TIME);
            $where['a.lock_end_time'] = array('EGT', APP_NOW_TIME);
        }

        if ($cate_id) {
            $where['a.cate_id'] = $cate_id;
        }

        if ($is_restrict == 0) {
            $where['a.is_restrict'] = $is_restrict;
        }
        elseif ($is_restrict == 1) {
            $where['a.is_restrict'] = $is_restrict;
        }

        $total      = $this->_model->alias('a')->where($where)->count();

        if ($total === false) {//查询出错
            $this->_sqlErrorExit(L('QUERY,MODULE_NAME_BLOG') . L('TOTAL_NUM,ERROR'));
        }
        elseif ($total == 0) {//无记录
            $this->_ajaxReturn(true, '', null, $total);
        }

        $now       = APP_NOW_TIME;
        $fields    = str_replace(array(',a.password', ',a.mac_address', ',a.add_time', ',a.lock_end_time', ',a.lock_memo'), '', join(',a.', $db_fields));
        $page_info = Filter::page($total);
        $data      = $this->_model->alias('a')
        ->join('JOIN ' . TB_BLOG_ROLE . ' AS r ON a.cate_id=r.cate_id')
        ->where($where)->field($fields . ',r.role_name,' . ("(a.add_time AND a.add_time<{$now} AND a.lock_end_time AND a.lock_end_time>{$now}) AS is_lock"))
        ->limit($page_info['limit'])
        ->order(($sort == 'is_lock' ? 'is_lock' : 'a.' .$sort) . ' ' . $order)->select();

        $data === false && $this->_sqlErrorExit(L('QUERY,MODULE_NAME_BLOG') . L('LIST,ERROR'));//出错

        $this->_ajaxReturn(true, '', $data, $total);
    }//end listAction

    /**
     * 显示/隐藏
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:32:41void 无返回值
     */
    public function showAction() {
        $this->_setOneOrZero();
    }
}