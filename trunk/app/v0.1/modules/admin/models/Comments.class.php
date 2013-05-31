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

class CommentsModel extends CommonModel {
    /**
     * @var string $_pk_field 数据表主键字段名称。默认comment_id
     */
    protected $_pk_field        = 'comment_id';
    /**
     * @var string $_true_table_name 实际数据表名(包含表前缀)。默认TB_BLOG
     */
    protected $_true_table_name = TB_COMMENTS;
    /**
     * @var array $_auto 自动填充
     */
    protected $_auto = array(
        'add_time'   => '_addtime#insert',//添加时间
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
     * @see CommonModel.class.php __construct()方法设置自动验证字段_validate
     */
    protected $_db_fields = array (
        'comment_id'     => array('filter' => 'int', 'validate' => 'unsigned#PRIMARY_KEY,DATA,INVALID'),//自增主键
        'parent_id'      => array('filter' => 'int'),//父id
        'content'        => array('validate' => array('notblank#CONTENT')),
        'status'         => array('filter' => 'int'),
        'at_email'       => array('filter' => 'int'),//有人回复时通知我
    );
}