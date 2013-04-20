<?php
/**
 * 博客分类模型
 *
 * @file            Category.class.php
 * @package         Yap\Module\Yablog\Model
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-18 11:53:42
 * @lastmodify      $Date$ $Author$
 */

class CategoryModel extends BaseModel {
    /**
     * @var string $_pk_field 数据表主键字段名称。默认cate_id
     */
    protected $_pk_field        = 'cate_id';
    /**
     * @var string $_true_table_name 实际数据表名(包含表前缀)。默认TB_CATEGORY
     */
    protected $_true_table_name = TB_CATEGORY;
}