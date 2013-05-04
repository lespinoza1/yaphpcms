<?php
/**
 * 评论留言模型
 *
 * @file            Log.class.php
 * @package         Yap\Module\Yab\Model
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-26 17:40:44
 * @lastmodify      $Date: 2013-03-01 16:53:50 +0800 (周五, 01 三月 2013) $ $Author: msl-138@163.com $
 */

class CommentsModel extends BaseModel {
    /**
     * @var array $_auto 自动填充
     */
    protected $_auto = array(
        'add_time'          => 'time',
        'last_reply_time'   => 'time',
        'user_ip'           => 'get_client_ip#1',
    );
    /**
     * @var array $_db_fields 表字段
     */
    protected $_db_fields = array (
        'parent_id'      => array('filter' => 'int', 'validate' =>  '_checkReply#INVALID,COMMENT'),//父id
        //用户名
        'username'       => array('validate' => array('_checkLength#USERNAME#value|0|20')),
        'content'        => array('validate' => array('notblank#CONTENT')),
        'add_time'       => array('filter' => 'int', 'validate' => array('_checkLength#ADD_TIME,DATA#value|0')),//添加时间
        'last_reply_time'=> array('filter' => 'int', 'validate' => array('_checkLength#LAST_REPLY_TIME,DATA#value|0')),//最后回复时间
        'user_ip'        => array('filter' => 'int', 'validate' => array('_checkLength#USER_IP,DATA#value|0')),//用户ip
        'level'          => array('filter' => 'int', 'validate' => array('_checkLength#LEVEL,DATA#value|0')),
        'node'           => array('filter' => 'int', 'validate' => array('_checkLength#NODE,DATA#value|0')),
        'user_homepage'  => array('filter' => 'url', 'validate' => array(array('', '{%PLEASE_ENTER,CORRECT,CN_DE,HOMEPAGE,LINK}', Model::VALUE_VALIDATE, 'url'), '_checkLength#MODULE_NAME_COMMENT,HOMEPAGE,LINK#value|0|50')),
        'user_pic'       => array('filter' => 'url', 'validate' => array('_checkLength#USER_PIC,DATA#value|0')),
        '_controller'    => array('validate' => array(array('blog,miniblog,guestbook', '{%INVALID_PARAM,_CONTROLLER}', Model::MUST_VALIDATE, 'in'))),
        '_blog_id'       => array('filter' => 'int', 'validate' =>  '_checkBlog#BLOG,NOT_EXIST'),//博客id 或 微博id
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
     * 检查博客或者微博是否存在
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-05-04 12:02:33
     *
     * @param int $blog_id 博客id 或 微博id
     *
     * @return true|string true存在，否则错误信息
     */
    protected function _checkBlog($blog_id) {
        C(array(
            'T_CONTROLLER'  => $controller = Filter::string('_controller'),
            'T_TABLE'       => DB_PREFIX . $controller,
            'T_BLOG_ID'     => $blog_id,
        ));

        if ('guestbook' == $controller) {
            return true;
        }

        return $blog_id && $this->table(C('T_TABLE'))->where('blog_id=' . $blog_id)->find();
    }

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

        C('T_PARENT_INFO', $parent_info);

        if ($parent_info) {
            return $parent_info['level'] < 5 ? true : L('INVALID,REPLY');
        }

        return L('REPLY,NOT_EXIST');
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

        if ($parent_info = C('T_PARENT_INFO')) {//父
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

        $table = C('T_TABLE');

        if (TB_GUESTBOOK == $table && $this->execute('INSERT INTO ' . $table . "(comment_id) VALUES({$pk_value})")) {//留言表
            $this->commit();
        }
        elseif (($blog_id = C('T_BLOG_ID')) && $this->execute('INSERT INTO ' . $table . "_comments(blog_id,comment_id) VALUES({$blog_id},{$pk_value})")) {//评论
            $this->commit();
        }
    }//end _afterInsert
}