<?php
/**
 * 博客模型
 *
 * @file            Admin.class.php
 * @package         Yap\Module\Admin\Model
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-03-23 13:36:59
 * @lastmodify      $Date$ $Author$
 */

class BlogModel extends BaseModel {
    /**
     * @var string $_pk_field 数据表主键字段名称。默认blog_id
     */
    protected $_pk_field        = 'blog_id';
    /**
     * @var string $_true_table_name 实际数据表名(包含表前缀)。默认TB_BLOG
     */
    protected $_true_table_name = TB_BLOG;
    /**
     * @var array $_auto 自动填充
     */
    protected $_auto = array(
        'add_time'   => '_strtotime#insert',//添加时间
        'update_time'=> 'time#update',//添加时间
        'is_issue'   => '_getCheckboxValue',//发布状态
    );
    /**
     * @var array $_db_fields
     * 数据表字段信息
     * filter: 数据类型，array(数据类型(string,int,float...),Filter::方法参数1,参数2...)
     * validate: 自动验证，支持多个验证规则
     *
     * @see Model.class.php create()方法对数据过滤
     * @see BaseModel.class.php __construct()方法设置自动验证字段_validate
     */
    protected $_db_fields = array (
        'blog_id'          => array('filter' => 'int', 'validate' => 'unsigned#PRIMARY_KEY,DATA,INVALID'),//自增主键

        //所属分类id
        'cate_id'          => array('filter' => 'int', 'validate' => '_checkCateId#PLEASE_SELECT,BELONG_TO_CATEGORY#data'),

        //标题
        'title'            => array('validate' => array('notblank#TITLE', 'title#{%TITLE,EXIST}#VALUE_VALIDATE#unique', '_checkLength#TITLE#value|0|60')),
        'content'         	=> array('filter' => 'raw', 'validate' => 'notblank#CONTENT'),

        //seo关键字
        'seo_keyword'      => array('validate' => array('notblank#SEO_KEYWORD', '_checkLength#SEO_KEYWORD#value|6|180')),
        //seo描述
        'seo_description'  => array('validate' => array('notblank#SEO_DESCRIPTION', '_checkLength#SEO_DESCRIPTION#value|6|300')),
        'is_issue'         => array('filter' => 'int', 'validate' => array('0,1#{%ISSUE,STATUS,MUST,IN,CN_WEI,ISSUE,、,CN_YI,ISSUE}#MUST_VALIDATE#in')),//是否显示
        'is_delete'        => array('filter' => 'int', 'validate' => array('0,1#{%DELETE,STATUS,MUST,IN,CN_WEI,DELETE,、,CN_YI,DELETE}#MUST_VALIDATE#in')),//是否显示
        'sort_order'       => array('filter' => 'int', 'validate' => 'unsigned#ORDER#-2'),//排序
        'from_name'        => array('validate' => '_checkLength#FROM_NAME#value|0|200'),//来源名称
        'from_url'         => array('validate' => '_checkLength#FROM_URL#value|0|200'),//来源url
        'add_time'         => null,
        'update_time'      => array('filter' => 'int', 'validate' => array('_checkLength#UPDATE,TIME,DATA#value|0')),
    );

    /**
     * 验证所属分类
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-23 14:50:38
     *
     * @param int   $cate_id 分类id
     * @param array $data      _POST数据
     *
     * @return mixed true验证成功。否则，如果未输入，返回提示信息，否则返回false
     */
    protected function _checkCateId($cate_id, $data) {

        if (!$cate_id) {//未输入
            return false;
        }
        elseif ($cate_id < 0) {
            return L('INVALID,BELONG_TO_CATEGORY,DATA');
        }

        return $this->_getCache($cate_id, 'Category') ? true : L('BELONG_TO_CATEGORY,NOT_EXIST');
    }
}