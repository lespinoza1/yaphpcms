<?php
/**
 * Yaf_View_Interface Smarty视图
 *
 * @file            Adapter.php
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-02-17 15:43:23
 * @lastmodify      $Date$ $Author$
 */

class Smarty_Adapter implements Yaf_View_Interface {
    /**
     * @var $_smarty Smarty实例
     */
    private $_smarty;

    /**
     * 构造函数
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 15:43:23
     *
     * @param string $tpl_path 模板路径。默认null，取默认值
     * @param array  $configs  smarty配置，如template_dir等
     *
     * @return void 无返回值
     */
    public function __construct($tpl_path = null, $configs = array()) {
        $this->_smarty = new Smarty();

        if (null !== $tpl_path) {
            $this->setScriptPath($tpl_path);
        }

        foreach ($configs as $key => $value) {
            $this->_smarty->$key = $value;
        }
    }

    /**
     * 魔术方法__isset，检测指定变量是否已经赋值
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:02:53
     *
     * @param string $key 待检测变量
     *
     * @return bool true已经赋值，否则false
     */
    public function __isset($key) {
        return null !== $this->_smarty->get_template_vars($key);
    }

    /**
     * 魔术方法__set，即调用$this->_smarty->assign
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:02:48
     *
     * @param mixed $key 变量名称或一组变量数组
     * @param mixed $val 变量值。默认null
     *
     * @return void 无返回值
     */
    public function __set($key, $val = null) {
        $this->assign($key, $val = null);
    }

    /**
     * 魔术方法__unset，清除指定变量已经赋值内容
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:02:59
     *
     * @param string $key 待检测变量
     *
     * @return void 无返回值
     */
    public function __unset($key) {
        $this->_smarty->clear_assign($key);
    }

    /**
     * 赋值
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:03:40
     *
     * @param mixed $key 变量名称或一组变量数组
     * @param mixed $val 变量值。默认null
     *
     * @return object Smarty实例
     */
    public function assign($key, $value = null) {

        if (is_array($key)) {
            $this->_smarty->assign($key);
        }
        else {
            $this->_smarty->assign($key, $value);
        }

        return $this->_smarty;
    }

    /**
     * 清空所有变量值
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:04:51
     *
     * @return void 无返回值
     */
    public function clearVars() {
        $this->_smarty->clear_all_assign();
    }

    /**
     * 渲染模板并输出
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:08:43
     * @lastmodify      2013-02-21 10:42:24 by mrmsl
     *
     * @param string $name 模板名称
     * @param mixed  $val  变量值。默认null
     *
     * @return void 无返回值
     */
    public function display($name, $value = null) {
        $this->_smarty->display($name);
    }

    /**
     * 返回Smarty实例
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 15:50:50
     *
     * @return object Smarty实例
     */
    public function getEngine() {
        return $this->_smarty;
    }

    /**
     * 获取Smarty模板路径
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 15:52:22
     *
     * @return string 模板路径
     */
    public function getScriptPath() {
        return $this->_smarty->template_dir;
    }

    /**
     * 渲染模板，不输出，而是模板内容
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 16:06:56
     *
     * @param string $name 模板名称
     * @param mixed  $val  变量值。默认null
     *
     * @return string 模板内容
     */
    public function render($name, $value = null) {
        return $this->_smarty->fetch($name);
    }

    /**
     * 设置Smarty配置
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 17:23:49
     *
     * @param string $key   配置名称或配置数组
     * @param mixed  $value 配置值。默认null
     *
     * @return void 无返回值
     */
    public function setConfig($key, $value = null) {

        if (is_array($key)) {

            foreach ($key as $k => $v) {
                $this->_smarty->$k = $v;
            }
        }
        else {
            $this->_smarty->$key = $value;
        }
    }

    /**
     * 设置Smarty模板路径
     *
     * @author          mrmsl <msl-138@163.com>
     * @date            2013-02-17 15:43:23
     *
     * @param string $tpl_path 模板路径
     *
     * @return void 无返回值
     */
    public function setScriptPath($path) {
        $this->_smarty->template_dir = $path;
    }
}