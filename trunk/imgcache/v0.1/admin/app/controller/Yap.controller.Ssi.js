/**
 * ssi服务器端包含控制器
 *
 * @file            app/controller/Yap.controller.Ssi.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-13 21:58:41
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.Ssi', {
    extend: 'Yap.controller.Base',
    /**
     * @cfg {String}
     * 主键
     */
    idProperty: 'ssi_id',
    /**
     * @cfg {String}
     * 查询字段
     */
    queryField: 'sort,order,sort_order,tpl_name,ssi_name,memo',//查询字段

    /**
     * @inheritdoc Yap.controller.Base#addAction
     */
    addAction: function (data) {
        var me = this,
        options = {
            listeners: {
                submitsuccess: function (form, action) {
                    me._listgrid && form.findField(me.idProperty).getValue() == 0 && me.store().load();
                }
            }
        };

        this.callParent([data, options]);
    },

    constructor: function() {//构造函数
        this.defineModel().defineStore();
    },

    /**
     * 生成ssi
     *
     * @private
     *
     * @param {String} ssi ssi
     *
     * @return {void} 无返回值
     */
    build: function(ssi) {return log(ssi);
        this.commonAction({
            action: this.getActionUrl(false, 'pack'),
            data: 'file=' + file,
            scope: this,
            store: this.store()
        });
    },

    /**
     * 获取表单域
     *
     * @author       mrmsl <msl-138@163.com>
     * @date         2012-09-24 15:18:32
     * @lastmodify   2012-12-14 11:48:46 by mrmsl
     *
     * @param {Object} data 当前标签数据
     *
     * @return {Array} 表单域
     */
    formField: function(data) {
        var me = this;
        var extField = Yap.Field.field();

        return [
            extField.fieldContainer(['TPL_NAME', [//模板名，不包括后缀
                [null, 'tpl_name', 'PLEASE_ENTER,TPL_NAME'],
                lang('LT_BYTE').format(30)
            ]]),
            extField.fieldContainer(['SSI_NAME,', [//生成ssi文件名，不包括后缀
                [null, 'ssi_name', 'PLEASE_ENTER,SSI_NAME'],
                lang('LT_BYTE').format(30)
            ]]),
            extField.sortOrderField(),//排序
            extField.memoField(),//备注
             extField.hiddenField(),//ssi_id
            this.btnSubmit()//通用提交按钮
        ]
    },

    /**
     * 获取数据列
     *
     * @return {Array} 数据列配置
     */
    getListColumns: function() {
        var me = this;

        return [{
            text: 'id',//id
            width: 60,
            dataIndex: this.idProperty
        }, {
            header: lang('TPL_NAME'),//模板文件名
            width: 120,
            dataIndex: 'tpl_name',
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'tpl_name');
            }
        }, {
            header: lang('SSI_NAME'),//生成ssi文件名
            width: 120,
            dataIndex: 'ssi_name',
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'ssi_name');
            }
        }, {
            header: lang('LAST_BUILD_TIME'),//最后生成ssi文件时间
            dataIndex: 'last_build_time',
            width: 140
        }, {
            header: lang('MEMO'),//备注
            dataIndex: 'memo',
            flex: 1,
            sortable: false
        }, {//操作列
            flex: 1,
            xtype: 'appactioncolumn',
            items: [
            me.editColumnItem(true),
            {//删除
                renderer: function(v, meta, record) {
                    return '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('DELETE') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    me['delete'](record, '<span class="font-red">{0}</span>(<span class="font-bold font-666">{1}</span>)'.format(htmlspecialchars(record.get('tpl_name')), htmlspecialchars(record.get('ssi_name'))));
                }
            }]
        }];
    },//end getListColumns

    /**
     * @inheritdoc Yap.controller.Base#listAction
     */
    listAction: function(data) {
        Yap.cmp.card.layout.setActiveItem(this.listgrid(data));
        global('app_contextmenu_refresh') && this.store().load();//标签页右键刷新 by mrmsl on 2012-08-15 09:10:17
        return this;
    },

    /**
     * @inheritdoc Yap.controller.Admin#store
     */
    store: function(data) {

        if (!this._store) {//未创建
            this._store = Ext.create('Yap.store.Ssi');
        }

        return this._store;
    },//end store

    /**
     * 列表顶部工具栏
     *
     * @return {Object} Ext.tool.Toolbar工具栏配置项
     */
    tbar: function(data) {
        var me = this, extField = Yap.Field.field(), extCombo = Yap.Field.combo();

        return {
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: lang('BUILD_SSI'),
                handler: function() {
                    var ssi = me.hasSelect(me._listgrid);
                    ssi && me.build(ssi);
                }
            }]
        };
    },//end tbar

    //放到最后定义，否则，jsduck后，上面的方法将属于Yap.store.Ssi.model.Ssi
    /**
     * @inheritdoc Yap.controller.Field#defineModel
     */
    defineModel: function() {
        /**
         * 系统日志数据模型
         */
        Ext.define('Yap.model.Ssi', {
            extend: 'Ext.data.Model',
            /**
             * @cfg {Array}
             * 字段
             */
            fields: [this.idProperty, 'tpl_name', 'ssi_name', 'last_build_time', 'memo', 'sort_order'],
            /**
             * @cfg {String}
             * 主键
             */
            idProperty: this.idProperty
        });

        return this;
    },

    /**
     * @inheritdoc Yap.controller.Field#defineStore
     * @member Yap.controller.Ssi
     */
    defineStore: function() {
        /**
         * 系统日志数据容器
         */
        Ext.define('Yap.store.Ssi', {
            extend: 'Ext.data.Store',
            /**
             * @cfg {Boolean}
             * 自动消毁
             */
            autoDestroy: true,
            /**
             * @cfg {Boolean}
             * 服务器端排序
             */
            remoteSort: false,
            /**
             * @cfg {Object/String}
             * 模型
             */
            model: 'Yap.model.Ssi',
            /**
             * @cfg {Object}
             * proxy
             */
            proxy: {
                type: C.dataType,
                url: this.getActionUrl(false, 'list'),
                reader: C.dataReader(),
                listeners: exception(),//捕获异常 by mrmsl on 2012-07-08 21:44:36
                messageProperty: 'msg',
                simpleSortMode: true
            },
            //增加排序，以防点击列表头部排序时，多余传参，出现不必要的错误 by mrmsl on 2012-07-27 16:21:54
            /**
             * @cfg {Object}
             * sorters
             */
            sorters: {
                property : this.idProperty,
                direction: 'DESC'
            },
            constructor: function(config) {//构造函数
                this.callParent([config || {}]);
            }
        });

        return this;
    }//end defineStore
});

//放到最后，以符合生成jsduck类说明
Ext.data.JsonP.Yap_controller_Ssi(Yap.controller.Ssi);