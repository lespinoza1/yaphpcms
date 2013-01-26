/**
 * 页面窗口视图
 *
 * @file            app/view/Yap.view.Viewport.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-03-17 08:46:32
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.view.Viewport', {
    extend: 'Ext.container.Viewport',
    /**
     * @cfg {Object}
     * 网站标题缓存
     */
    cache: {},//网站标题缓存 by mrmsl on 2012-07-24 15:21:54
    /**
     * @cfg {Object}
     * defaults
     */
    defaults: {
        xtype: 'container',
        bodyPadding: 4
    },
    /**
     * @cfg {String}
     * id
     */
    id: 'appViewport',
    /**
     * @cfg {String}
     * layout
     */
    layout: 'border',
    /**
     * @cfg {String}
     * style
     */
    style: 'background: #fff',

    /**
     * 初始化组件
     *
     * @private
     *
     * @return {void} 无返回值
     */
    initComponent: function() {
        this.items = [{
            xtype: 'appheader'
        }, {
            xtype: 'apptree'
        }, {
            xtype: 'appcenter'
        }];
        this.callParent(arguments);
    },

    /**
     * 设置页面标题，参数大于2个将手动设置标题
     *
     * @param {String} [controller=url自动获取] 控制器
     * @param {String} [action==url自动获取] 操作方法
     *
     * @return {void} 无返回值
     */
    setPageTitle: function(controller, action) {
        var str = '?' + location.hash.substr(1);
        controller = controller || _GET('controller', str), action = action || _GET('action', str);

        if (arguments[2]) {//手动设置标题
            document.title = arguments[2];
            //添加 => 编辑 by mrmsl on 2012-08-09 12:45:28 最近操作调用
            this.cache[controller + action] = this.cache[controller + action].replace(lang('ADD'), lang('EDIT'));
        }
        else {

            if (!this.cache[controller + action]) {//增加网站标题缓存控制 by mrmsl on 2012-07-24 15:22:21
                var store = Yap.cmp.tree.findRecordByUrl(controller, action);//高亮选中菜单
                var title = [];

                if (store) {
                    store.bubble(function(node) {
                        !node.isRoot() && title.push(node.get('menu_name'));
                    });
                }

                title = title.join('_');
                title = strip_tags(title);
                this.cache[controller + action] = title;
            }

            this.origTitle = this.origTitle ? this.origTitle : document.title;
            //编辑 => 添加  by mrmsl on 2012-08-09 12:46:17 最近操作调用
            document.title = this.cache[controller + action] ? (this.cache[controller + action].replace(lang('EDIT'), lang('ADD')) + '_' + this.origTitle) : this.origTitle;
        }

        Yap.cmp.card.setTitle(document.title.split('_').reverse().join(' » '));//主面板标题 by mrmsl on 2012-12-03 13:21:49
    }//end setPageTitle
});