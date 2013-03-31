<?php
/**
 * 博客分类控制器类
 *
 * @file            Category.class.php
 * @package         Yap\Module\Admin\Controller
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-03-21 13:26:27
 * @lastmodify      $Date$ $Author$
 */

class CategoryController extends BaseController {
    /**
     * @var bool $_get_children_ids true取所有子表单， BaseController->delete()会用到。默认true
     */
    protected $_get_children_ids   = true;
    /**
     * @var string $_name_column 名称字段 BaseController->delete()会用到。默认cate_name
     */
    protected $_name_column        = 'cate_name';
    /**
     * @var array $_priv_map 权限映射，如'delete' => 'add'删除权限映射至添加权限
     */
    protected $_priv_map           = array(
        'delete'   => 'add',//删除
        'info'     => 'add',//具体信息
        'show'     => 'add',//显示隐藏
    );

    /**
     * 获取分类树数据
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:42:41
     * @lastmodify      2013-03-31 13:17:48 by mrmsl
     *
     * @param $data 初始数据。默认array()，读分类树缓存
     *
     * @return array 分类树数据
     */
    private function _getCategory($data = array()) {
        $data = $data ? $data : $this->_getCache(0, MODULE_NAME . '_tree');

        if (!$data) {//无分类缓存，直接返回
            return array();
        }

        $tree = array();
        $k    = 0;

        static $cate_id      = null;
        static $include_self = null;

        $cate_id      = null === $cate_id  ? Filter::int($this->_pk_field, 'get') : $cate_id;
        $include_self = null === $include_self ? isset($_GET['include_self']) : $include_self;

        foreach ($data as $cate) {

            if ($this->_unshift && !$include_self ? $cate_id != $cate[$this->_pk_field] : $cate['is_show']) {
                $tree[$k] = array(
                    'cate_id'   => $cate['cate_id'],
                    'parent_id' => $cate['parent_id'],
                    'cate_name' => $cate['cate_name'],
                    'leaf'      => $cate['leaf'],
                );

                if (!empty($cate['data'])) {
                    $tree[$k]['data'] = $this->_getCategory($cate['data']);
                }

                $k++;
            }
        }

        return $tree;
    }//end _getCategory

    /**
     * {@inheritDoc}
     */
    protected function _infoCallback(&$info) {
        $this->_treeInfoCallback($info, $this->_name_column);
    }

    /**
     * 添加或编辑
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:45:57
     *
     * @return void 无返回值
     */
    public function addAction() {
        $this->_commonAddTreeData('cate_name,en_name,seo_keyword,seo_description,parent_name,is_show,sort_order', $this->_name_column);
    }

    /**
     * 生成缓存
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:38:34
     *
     * @return object this
     */
    public function createAction() {
        $data      = $this->_model
        ->order('parent_id ASC,is_show DESC,sort_order ASC, cate_id ASC')
        ->key_column($this->_pk_field)->select();

        if ($data === false) {
            $this->_model->addLog();
            $this->_ajaxReturn(false, L('CREATE_CATEGORY_CACHE,FAILURE'), 'EXIT');
        }

        $tree_data = Misc_Tree::array2tree($data, $this->_pk_field);//树形式

        return $this->_setCache($data)->_setCache($tree_data, $this->_getControllerName() . '_tree');
    }

    /**
     * 列表管理
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:50:24
     *
     * @return void 无返回值
     */
    public function listAction() {
        $cate_id     = Filter::int('node', 'get');//分类id
        $column      = Filter::string('column', 'get');//搜索字段

        //搜索 by mrmsl on 2012-07-24 18:02:02
        if (isset($_GET['is_show']) && in_array($column, array('cate_name', 'en_name', 'seo_keyword', 'seo_description'))) {
            $keyword     = Filter::string('keyword', 'get');//搜索关键字
            $is_show     = Filter::int('is_show', 'get');//是否显示 by mrmsl on 2012-09-15 12:14:57

            -1 != $is_show && $this->_queryTreeWhere = array('is_show' => array('eq', $is_show));

            $this->_queryTree($column, $keyword);
        }
        elseif ($cate_id) {
            $this->_ajaxReturn(true, '', $this->_getTreeData($cate_id, false));
        }

        $data = $this->_getCache(0, MODULE_NAME . '_tree');

        $this->_ajaxReturn(true, '', $data, count($this->_getCache()));
    }//end listAction

    /**
     * 所属分类
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-21 13:58:09
     *
     * @return void 无返回值
     */
    public function publicCategoryAction() {
        $data = $this->_getCategory();

        //增加顶级分类
        $this->_unshift && array_unshift($data, array('cate_id' => 0, 'cate_name' => isset($_GET['emptyText']) ? Filter::string('emptyText', 'get') : L('TOP_LEVEL_CATEGORY'), 'leaf' => true));

        $parent_id = Filter::int('parent_id', 'get');

        //添加指定分类子分类，获取指定分类信息by mashanlng on 2012-08-21 13:53:35
        if ($parent_id && ($parent_info = $this->_getCache($parent_id))) {
            $parent_info = array(
                 'cate_id'     => $parent_id,
                 'parent_name' => $parent_info['cate_name'],
            );
            $this->_ajaxReturn(array('data' => $data, 'parent_data' => $parent_info));
        }

        $this->_ajaxReturn(true, '', $data);
    }

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