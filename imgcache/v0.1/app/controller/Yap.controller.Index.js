/**
 * 首页控制器
 *
 * @file            app/controller/Yap.controller.Index.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-03-17 10:06:47
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.Index', {
    extend: 'Yap.controller.Base',
    /**
     * 获取url地址
     *
     * @return {String} url地址
     */
    getUrl: function() {
        return '#controller=index&action=index';
    },

    /**
     * 加载首页
     *
     * @return {void} 无返回值
     */
    loadIndex: function() {
        Yap.cmp.tabs.tabs.length == 0 && Yap.History.push('');
        Yap.cmp.tabs.setActiveTab('index');
        Yap.cmp.viewport.setPageTitle('');
        Yap.cmp.tree.selectUrl('none');
        Yap.cmp.card.layout.setActiveItem('appIndex');
    }
});