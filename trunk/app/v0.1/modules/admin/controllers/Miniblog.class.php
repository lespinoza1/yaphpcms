<?php
/**
 * 微博控制器类
 *
 * @file            Miniblog.class.php
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-15 09:54:14
 * @lastmodify      $Date$ $Author$
 */

class MiniblogController extends BaseController {
    /**
     * @var array $_priv_map 权限映射，如'delete' => 'add'删除权限映射至添加权限
     */
    protected $_priv_map           = array(
        'delete'   => 'add',//删除
        'info'     => 'add',//具体信息
    );

    /**
     * 删除后置操作
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-26 22:04:00
     *
     * @param array $pk_id 主键值
     *
     * @return void 无返回值
     */
    protected function _afterDelete($pk_id) {
        $this->deleteHtmlAction(null);
    }

    /**
     * {@inheritDoc}
     */
    protected function _beforeExec(&$pk_id, &$log) {
        $log = join(', ', $pk_id);//操作日志

        $data       = $this->_model
        ->where(array($this->_pk_field => array('IN', $pk_id)))
        ->key_column($this->_pk_field)
        ->field($this->_pk_field . ',link_url')
        ->select();

        C('HTML_BUILD_INFO', $data);

        return null;
    }

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
        $pk_value  = $this->_model->$pk_field;//微博id
        $data      = $this->_model->getProperty('_data');//数据，$model->data 在save()或add()后被重置为array()
        $diff_key  = 'content';//比较差异字段
        $msg       = L($pk_value ? 'EDIT' : 'ADD');//添加或编辑
        $log_msg   = $msg . L('MODULE_NAME_MINIBLOG,FAILURE');//错误日志
        $error_msg = $msg . L('FAILURE');//错误提示信息

        if ($pk_value) {//编辑

            if (!$blog_info = $this->_model->find($pk_value)) {//编辑微博不存在
                $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%:,MODULE_NAME_MINIBLOG,%{$pk_field}({$pk_value}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
                $this->_ajaxReturn(false, $error_msg);
            }

            if (false === $this->_model->save()) {//更新出错
                $this->_sqlErrorExit($msg . L('MODULE_NAME_MINIBLOG') . "{$blog['title']}({$pk_value})" . L('FAILURE'), $error_msg);
            }

            $diff = $this->_dataDiff($blog_info, $data, $diff_key);//差异
            C('HTML_BUILD_INFO', array('link_url' => $blog_info['link_url']));
            $this->deleteHtmlAction(null);
            $this->_model->addLog($msg . L('MODULE_NAME_MINIBLOG')  . "{$blog_info['content']}({$pk_value})." . $diff. L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
        else {
            $data = $this->_dataDiff($data, false, $diff_key);//数据

            if (false === ($insert_id = $this->_model->add())) {//插入出错
                $this->_sqlErrorExit($msg . L('MODULE_NAME_MINIBLOG') . $data . L('FAILURE'), $error_msg);
            }

            $this->_model->addLog($msg . L('MODULE_NAME_MINIBLOG') . $data . L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
    }//end addAction

    /**
     * 删除静态文件
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-04-26 22:05:03
     *
     * @param $build_arr array|null 已修改微博信息
     *
     * @return void 无返回值
     */
    public function deleteHtmlAction($build_arr = array()) {
        $build_arr = null === $build_arr ? C('HTML_BUILD_INFO') : $build_arr;

        if (!$build_arr) {
            return;
        }

        foreach ($build_arr as $item) {
            is_file($filename = str_replace(BASE_SITE_URL, WWWROOT, $item['link_url'])) && unlink($filename);
        }
    }

    /**
     * 获取微博具体信息
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-26 12:14:15
     *
     * @return $this->_info()结果
     */
    public function infoAction() {
        return $this->_info(false);
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
        $where          = array();

        if ($keyword !== '') {
            $where['content'] = $this->_buildMatchQuery('content', $keyword, Filter::string('match_mode', 'get'));
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

        $total      = $this->_model->where($where)->count();

        if ($total === false) {//查询出错
            $this->_sqlErrorExit(L('QUERY,MODULE_NAME_MINIBLOG') . L('TOTAL_NUM,ERROR'));
        }
        elseif ($total == 0) {//无记录
            $this->_ajaxReturn(true, '', null, $total);
        }

        $page_info = Filter::page($total);
        $data      = $this->_model
        ->where($where)
        ->limit($page_info['limit'])
        ->order(('' .$sort) . ' ' . $order)->select();

        $data === false && $this->_sqlErrorExit(L('QUERY,MODULE_NAME_MINIBLOG') . L('LIST,ERROR'));//出错

        $this->_ajaxReturn(true, '', $data, $total);
    }//end listAction
}