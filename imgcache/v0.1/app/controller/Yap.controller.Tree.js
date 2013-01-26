/**
 * 导航菜单树控制器
 *
 * @file            app/controller/Yap.controller.Tree.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-03-17 10:15:26
 * @lastmodify      $Date$ $Author$
 */


Ext.define('Yap.controller.Tree', {
    extend: 'Yap.controller.Base',
    /*
     * @cfg {Array}
     * 容器
     */
    stores: ['Yap.store.Tree'],
    /*
     * @cfg {Array}
     * 视图
     */
    views: ['Yap.view.Tree'],

    /**
     * 处理菜单树点击
     *
     * @param {String} href url地址
     *
     * @return {Boolean} 如果无权限，返回false，否则返回true
     */
    handleUrlClick: function (url) {
        var data = Ext.Object.fromQueryString(url);
        var activeTab = Yap.cmp.tabs.getActiveTab();
        var controller = data.controller;
        var action = data.action;

        if (!checkPriv(controller, action)) {//权限判断
            return false;
        }

        if (action == 'add') {//action等于add，导航
            Yap.History.push(url);
            return true;
        }

        if (activeTab && activeTab.controller == controller && activeTab.action == action) {//正显示
            getController('Tabs').addTabFromTree(activeTab);
            return true;
        }

        tabData = Yap.cmp.tabs.getTabData(controller, action);
        Yap.History.push(tabData ? Ext.Object.toQueryString(tabData) : url);

        return true;
    },

    /**
     * 初始化
     *
     * @private
     *
     * @return {void} 无返回值
     */
    init: function() {
        this.control({
            apptree: {
                afterrender: function(b) {//菜单完成渲染
                    b.el.on({//阻止默认行为
                        click: Ext.emptyFn,
                        delegate: 'a',
                        preventDefault: true
                    });
                },
                itemclick: function(view, record) {//单击事件

                    if (record.isLeaf() || record.get('action') != '#') {//叶或操作方法不为# by mrmsl on 2012-08-19 22:16:12
                        this.handleUrlClick(record.data.href.substr(1));
                    }

                    if (!record.isLeaf() && record.get('action') == '#') {//枝
                        record.isExpanded() ? record.collapse() : record.expand();
                    }
                },
                collapse: function(view) {//收缩事件 by mrmsl on 2012-11-26 13:30:23
                    view.fireEvent('resizetabs', 0);
                },
                expand: function(view) {//展开事件 by mrmsl on 2012-11-26 13:30:49
                    view.fireEvent('resizetabs', Yap.cmp.tree.getWidth());
                },
                resize: function(view, width) {//拉伸事件 by mrmsl on 2012-11-26 13:31:11
                    view.fireEvent('resizetabs', width);
                },
                resizetabs: function(left) {//重置标签栏 by mrmsl on 2012-11-27 12:35:20
                    var view = Yap.cmp && Yap.cmp.tabs;

                    if (view) {
                        var position = view.getPosition();
                        view.setPosition(left, position[1], {
                            duration: 300
                        });
                        view.resizeTabs();
                    }
                }
            }
        }, this);
    }
});