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
     * @var bool $_after_exec_cache true删除后调用CommonController->_setCache()生成缓存， CommonController->delete()会用到。默认true
     */
    protected $_after_exec_cache    = true;
    /**
     * @var string $_name_column 名称字段 CommonController->delete()会用到。默认tpl_name
     */
    protected $_name_column         = 'tpl_name';
    /**
     * @var array $_priv_map 权限映射，如'delete' => 'add'删除权限映射至添加权限
     */
    protected $_priv_map            = array(
         'delete'  => 'add',//删除
         'info'    => 'add',//具体信息
         'build'   => 'add',//生成ssi
    );

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
     * 生成成功
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-13 16:07:14
     *
     * @param int $ssi_id ssi_id
     *
     * @return void 无返回值
     */
    private function _successAction($ssi_id) {
        $ssi_id && $this->_model->save(array($this->_pk_field => $ssi_id, 'last_build_time' => time()));

        if ('all' != ACTION_NAME || !$ssi_id) {
            $this->_model->addLog(L('BUILD_SSI') . ',' . ACTION_NAME, LOG_TYPE_ADMIN_OPERATE);
            $this->_setCache()->_ajaxReturn(true, L('BUILD_SSI,SUCCESS'));
        }
    }

    /**
     * 获取写缓存数据
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-13 15:32:21
     *
     * @return mixed 查询成功，返回数组，否则false
     */
    protected function _setCacheData() {
        $data = $this->_model->key_column($this->_pk_field)->order('sort_order ASC,ssi_id ASC')->select();

        if ($data) {

            foreach ($data as $k => $v) {
                $data[$k]['last_build_time'] = $v['last_build_time'] ? new_date(null, $v['last_build_time']) : '';
            }
        }

        return $data;
    }

    /**
     * 添加
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-13 15:39:44
     *
     * @return void 无返回值
     */
    public function addAction() {
        $check     = $this->_model->checkCreate();//自动创建数据

        $check !== true && $this->_ajaxReturn(false, $check);//未通过验证

        $pk_field  = $this->_pk_field;//主键
        $pk_value  = $this->_model->$pk_field;//ssiid

        $data      = $this->_model->getProperty('_data');//数据，$model->data 在save()或add()后被重置为array()
        $diff_key  = 'tpl_name,ssi_name,sort_order,memo';//比较差异字段 增加锁定字列by mrmsl on 2012-07-11 11:42:33
        $msg       = L($pk_value ? 'EDIT' : 'ADD');//添加或编辑
        $log_msg   = $msg . L('MODULE_NAME_SSI,FAILURE');//错误日志
        $error_msg = $msg . L('FAILURE');//错误提示信息

        if ($pk_value) {//编辑

            if (!$info = $this->_getCache($pk_value)) {//ssi不存在
                $this->_model->addLog($log_msg . '<br />' . L("INVALID_PARAM,%:,MODULE_NAME_SSI,%{$pk_field}({$pk_value}),NOT_EXIST"), LOG_TYPE_INVALID_PARAM);
                $this->_ajaxReturn(false, $error_msg);
            }

            if (false === $this->_model->save()) {//更新出错
                $this->_sqlErrorExit($msg . L('MODULE_NAME_SSI') . "{$info['tpl_name']}({$pk_value})" . L('FAILURE'), $error_msg);
            }

            $diff = $this->_dataDiff($info, $data, $diff_key);//差异
            $this->_model->addLog($msg . L('MODULE_NAME_SSI')  . "{$info['tpl_name']}({$pk_value})." . $diff. L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_setCache()->_ajaxReturn(true, $msg . L('SUCCESS'));

        }
        else {
            $data = $this->_dataDiff($data, false, $diff_key);//数据

            if ($this->_model->add() === false) {//插入出错
                $this->_sqlErrorExit($msg . L('MODULE_NAME_SSI') . $data . L('FAILURE'), $error_msg);
            }

            $this->_model->addLog($msg . L('MODULE_NAME_SSI') . $data . L('SUCCESS'), LOG_TYPE_ADMIN_OPERATE);
            $this->_setCache()->_ajaxReturn(true, $msg . L('SUCCESS'));
        }
    }

    /**
     * 全部生成ssi
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:40:15
     *
     * @return void 无返回值
     */
    public function allAction() {

        if ($data = $this->_getCache()) {

            foreach($data as $item) {
                $method = $item['tpl_name'] . 'Action';

                if (method_exists($this, $method)) {
                    $this->$method($item);
                }
                else {
                    C('LOG_FILENAME', 'ssi');
                    trigger_error($log = __METHOD__ . ',' . $method . L('NOT_EXIST'), E_USER_ERROR);
                }
            }
        }

        $this->_successAction(0);
    }

    /**
     * 底部
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 13:30:10
     *
     * @param array $info ssi信息
     *
     * @return void 无返回值
     */
    public function footerAction($info) {
        $this->_getViewTemplate('build_html')->assign('footer', sys_config('sys_base_copyright'));
        $this->_buildHtml(SSI_PATH . $info['tpl_name'] . C('HTML_SUFFIX'), $this->_fetch(null, $info['ssi_name']));
        $this->_successAction($info['ssi_id']);
    }

    /**
     * 列表
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-13 15:54:17
     *
     * @return void 无返回值
     */
    public function listAction() {
        $data = $this->_getCache();

        if ($data) {
            $data = array_values($data);
        }
        else {
            $data = array();
        }

        $this->_ajaxReturn(true, '', $data);
    }

    /**
     * 热门网文
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:59:29
     *
     * @param array $info ssi信息
     *
     * @return void 无返回值
     */
    public function hot_blogsAction($info) {
        $blogs = $this->_model
        ->table(TB_BLOG)
        ->order('hits DESC')
        ->where('is_issue=1 AND is_delete=0')
        ->field('link_url,title')
        ->limit(10)
        ->select();
        $this->_getViewTemplate('build_html')->assign('blogs', $blogs);
        $this->_buildHtml(SSI_PATH . $info['tpl_name'] . C('HTML_SUFFIX'), $this->_fetch(null, $info['ssi_name']));
        $this->_successAction($info['ssi_id']);
    }

    /**
     * 导航条
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 08:33:53
     *
     * @param array $info ssi信息
     *
     * @return void 无返回值
     */
    public function navbarAction($info) {
        $this->_getViewTemplate('build_html')->assign('category_html', $this->_categoryNav());
        $this->_buildHtml(SSI_PATH . $info['tpl_name'] . C('HTML_SUFFIX'), $this->_fetch(null, $info['ssi_name']));
        $this->_successAction($info['ssi_id']);
    }

    /**
     * 最新评论
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:38:39
     *
     * @param array $info ssi信息
     *
     * @return void 无返回值
     */
    public function new_commentsAction($info) {
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
        $this->_buildHtml(SSI_PATH . $info['tpl_name'] . C('HTML_SUFFIX'), $this->_fetch(null, $info['ssi_name']));
        $this->_successAction($info['ssi_id']);
    }

    /**
     * 标签云
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-05-10 10:38:39
     *
     * @param array $info ssi信息
     *
     * @return void 无返回值
     */
    public function tagsAction($info) {
        $tags = $this->_model
        ->table(TB_TAG)
        ->order('searches DESC')
        ->field('DISTINCT `tag`')
        ->limit(50)
        ->select();
        $this->_getViewTemplate('build_html')->assign('tags', $tags);
        $this->_buildHtml(SSI_PATH . $info['tpl_name'] . C('HTML_SUFFIX'), $this->_fetch(null, $info['ssi_name']));
        $this->_successAction($info['ssi_id']);
    }
}