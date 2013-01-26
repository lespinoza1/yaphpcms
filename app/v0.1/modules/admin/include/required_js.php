<?php
/**
 * 必须加载js文件，首页及压缩js调用
 *
 * @file            require_js.php
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-12-30 10:16:18
 * @lastmodify      $Date$ $Author$
 */

return array(
    'util/common.js',
    'util/override.js',
    'util/Yap.History.js',
    'util/Yap.Application.js',
    'util/Yap.Field.js',//表单域 by mrmsl on 2012-12-11 15:56:15
    'store/Yap.store.Admin.js',
    'store/Yap.store.Role.js',
    'store/Yap.store.Tree.js',
    'store/Yap.store.Area.js',//国家地区 by mrmsl 22:05 2012-7-18
    'ux/Yap.ux.RoleCombo.js',
    'ux/Yap.ux.TreePicker.js',//下拉树 by mrmsl on 2012-08-02 18:25:52
    'ux/Yap.ux.Form.js',//表单 by mrmsl on 2012-12-11 15:55:54
    'ux/Yap.ux.Grid.js',//普通列表扩展 by mrmsl on 2012-12-18 11:32:51
    'ux/Yap.ux.TreeGrid.js',//树列表扩展 by mrmsl on 2012-12-18 11:32:51
    'view/Yap.view.Viewport.js',
    'view/Yap.view.Tabs.js',
    'view/Yap.view.Index.js',
    'view/Yap.view.Center.js',
    'view/Yap.view.Header.js',
    'view/Yap.view.Tree.js',
    'controller/Yap.controller.Base.js',//底层控制器 by mrmsl on 2012-07-28 09:04:24
    'controller/Yap.controller.Tree.js',
    'controller/Yap.controller.Tabs.js',
    'controller/Yap.controller.Index.js',
    'controller/Yap.controller.Login.js',
);