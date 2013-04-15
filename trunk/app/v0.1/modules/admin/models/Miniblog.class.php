<?php
/**
 * 博客模型
 *
 * @file            Miniblog.class.php
 * @package         Yap\Module\Admin\Model
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-15 09:45:19
 * @lastmodify      $Date$ $Author$
 */

class MiniblogModel extends BaseModel {
    /**
     * @var string $_pk_field 数据表主键字段名称。默认blog_id
     */
    protected $_pk_field        = 'blog_id';
    /**
     * @var string $_true_table_name 实际数据表名(包含表前缀)。默认TB_MINIBLOG
     */
    protected $_true_table_name = TB_MINIBLOG;
    /**
     * @var array $_auto 自动填充
     */
    protected $_auto = array(
        'add_time'   => '_addtime#insert',//添加时间
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
        'content'         	=> array('filter' => 'raw', 'validate' => 'notblank#CONTENT'),
        'add_time'         => null,
    );
}