<?php
/**
 * 留言模型
 *
 * @file            Log.class.php
 * @package         Yap\Module\Yab\Model
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-26 17:16:05
 * @lastmodify      $Date$ $Author$
 */

class GuestbookModel extends BaseModel {
    /**
     * @var array $_auto 自动填充
     */
    protected $_auto = array(
        'add_time'     => 'time',
        'last_reply_time' => 'time',
        'user_ip'      => 'get_client_ip#1',
    );
    /**
     * @var array $_db_fields 表字段
     */
    protected $_db_fields = array (
        'parent_id'      => array('filter' => 'int', 'validate' =>  '_checkReply#INVALID,REPLY,MODULE_NAME_GUESTBOOK'),//父id
        //用户名
        'username'       => array('validate' => array('_checkLength#USERNAME#value|0|20')),
        'content'        => array('validate' => array('notblank#MODULE_NAME_GUESTBOOK,CONTENT', '_checkLength#MODULE_NAME_GUESTBOOK,CONTENT#value|0|500')),
        'add_time'       => array('filter' => 'int'),//添加时间
        'last_reply_time'=> array('filter' => 'int'),//最后回复时间
        'user_ip'        => array('filter' => 'int'),//用户ip
        'level'          => array('filter' => 'int', 'validate' => array('_checkLength#LEVEL,DATA#value|0')),
        'node'           => array('filter' => 'int', 'validate' => array('_checkLength#NODE,DATA#value|0')),
    );
    /**
     * @var string $_pk_field 数据表主键字段名称。默认log_id
     */
    protected $_pk_field        = 'comment_id';
    /**
     * @var string $_true_table_name 实际数据表名(包含表前缀)。默认TB_GUESTBOOK
     */
    protected $_true_table_name = TB_COMMENTS;//表

    /**
     * 检查回复回复是否存在
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-03-01 13:28:29
     *
     * @param int $parent_id 父id
     *
     * @return true|string true存在并且还可回复，即小于5层，否则错误信息
     */
    protected function _checkReply($parent_id) {

        if (!$parent_id) {//非回复
            return true;
        }

        $parent_info = $this->field('level,node,status')->find($parent_id);//父亲信息

        C('t_parent_info', $parent_info);

        if ($parent_info) {
            return $parent_info['level'] < 5 && (__GET || 1 == $parent_info['status']) ? true : L('INVALID,REPLY,MODULE_NAME_GUESTBOOK');
        }

        return L('REPLY,MODULE_NAME_GUESTBOOK,NOT_EXIST');
    }

    /**
     * 插入后置操作，向留言表增加刚插入id
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-03-01 13:30:52
     *
     * @param $data     插入数据
     * @param $options  查询表达式
     *
     * @return void 无返回值
     */
    protected function _afterInsert($data, $options) {
        $pk_value = $data[$this->_pk_field];

        if ($parent_info = C('t_parent_info')) {//父
            $node_arr = explode(',', $parent_info['node']);

            $data = array(
                'level'          => $parent_info['level'] + 1,//层级
                'node'           => $parent_info['node'] . ',' . $pk_value,//节点关系
            );

            $this->where($this->_pk_field . '=' . $pk_value)->save($data);
            $this->where($this->_pk_field . '=' . $node_arr[0])->save(array('last_reply_time' => time()));//更新最上层最后回复时间
        }
        else {

            $data = array(
                'node'           =>  $pk_value,
            );

            $this->where($this->_pk_field . '=' . $pk_value)->save($data);//节点关系
        }

        if ($this->execute('INSERT INTO ' . TB_GUESTBOOK . "(comment_id) VALUES({$pk_value})")) {//留言表
            $this->commit();
        }
    }//end _afterInsert
}