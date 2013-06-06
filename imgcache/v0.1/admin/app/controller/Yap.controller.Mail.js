/**
 * 邮件模板控制器
 *
 * @file            app/controller/Yap.controller.Mail.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-06-06 17:12:43
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.Mail', {
    extend: 'Yap.controller.Base',
    /**
     * @cfg {String}
     * 主键
     */
    idProperty: 'template_id',
    /**
     * @cfg {String}
     * 名称字段
     */
    nameColumn: 'template_name',//名称字段

    constructor: function() {//构造函数
        this.defineModel().defineStore();
    },

    /**
     * @inheritdoc Yap.controller.Base#listAction
     */
    listAction: function(data) {
        Yap.cmp.card.layout.setActiveItem(this.listgrid(data));
        global('app_contextmenu_refresh') && this.store().load();//标签页右键刷新 by mrmsl on 2012-08-15 09:10:17
        return this;
    },

    /**
     * @inheritdoc Yap.controller.Base#addAction
     */
    addAction: function (data) {
        var me = this,
        options = {
            listeners: {
                submitsuccess: function () {
                    me._listgrid && me._store.load();
                }
            }
        };

        this.callParent([data, options]);
    },

    /**
     * @inheritdoc Yap.controller.Admin#formField
     */
    formField: function(data) {
        var me = this, extField = Yap.Field.field();

        return [
            extField.fieldContainer(['TEMPLATE,NAME', [//模板名称
                [null, this.nameColumn, 'PLEASE_ENTER,TEMPLATE,NAME'],
                lang('LT_BYTE').format(20) + '，' + lang('CN_TO_BYTE')
            ]]),
            extField.fieldContainer(['MAIL_SUBJECT', [//邮件主题
                [null, 'subject', 'PLEASE_ENTER,MAIL_SUBJECT', '', '', {width: 400}],
                lang('LT_BYTE').format(150)
            ]]),
            extField.textarea('content', 'PLEASE_ENTER,MAIL_CONTENT', 'MAIL_CONTENT', '', { width: 1000, height: 300 }),//模板内容
            extField.sortOrderField(),//排序
            extField.memoField(),//备注
            extField.textareaComment(lang('LT_BYTE').format(60)),//备注提示
            extField.hiddenField(),//template_id
            this.btnSubmit()//通用提交按钮
        ];
    },

    /**
     * @inheritdoc Yap.controller.Admin#getListColumns
     */
    getListColumns: function() {
        var me = this;

        return [{
            text: lang('TEMPLATE') + 'id',//模板id
            width: 50,
            dataIndex: this.idProperty
        }, {
            header: lang('TEMPLATE,NAME'),//模板名
            width: 120,
            dataIndex: this.nameColumn
        }, {
            header: lang('ORDER'),//排序
            dataIndex: 'sort_order',
            width: 50,
            align: 'center'
        }, {
            header: lang('MEMO'),//备注
            flex: 1,
            dataIndex: 'memo',
            sortable: false
        }, {//操作列
            width: 160,
            xtype: 'appactioncolumn',
            items: [{
                renderer: function(v, meta, record) {//编辑
                    return record.get(me.idProperty) == ADMIN_ROLE_ID && ADMIN_INFO.roleId != ADMIN_ROLE_ID ? '' : '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('EDIT') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    me.edit(grid.getStore().getAt(rowIndex));
                }
            }, {
                renderer: function(v, meta, record) {//删除
                    return record.get(me.idProperty) == ADMIN_ROLE_ID && ADMIN_INFO.roleId != ADMIN_ROLE_ID ? '' : '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('DELETE') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    me['delete'](record, '<span class="font-bold font-red">' + htmlspecialchars(record.get(me.nameColumn)) + '</span>');
                }
            }]
        }];
    },//end getListColumns

    /**
     * 分页条
     *
     * @param {Object} data 当前标签数据
     *
     * @return {Object} Ext.toolbar.Paging配置项
     */
    pagingBar: function(data) {
        var me = this;

        return {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: this.store(),
            displayInfo: true
        };
    },//end pagingBar

    /**
     * @inheritdoc Yap.controller.Admin#store
     */
    store: function() {

        if (!this._store) {//未创建
            this._store = Ext.create('Yap.store.Mail', {
                autoLoad: true
            });
        }

        return this._store;
    },//end store

    /**
     * @inheritdoc Yap.controller.Admin#tbar
     */
    tbar: function() {
        var me = this;

        return {
            xtype: 'toolbar',
            dock: 'top',
            items: this.deleteItem()
        }
    },//end tbar

    //放到最后定义，否则，jsduck后，上面的方法将属于Yap.store.Mail或Yap.model.Mail
    /**
     * @inheritdoc Yap.controller.Field#defineModel
     */
    defineModel: function() {
        /**
         * 博客数据模型
         */
        Ext.define('Yap.model.Mail', {
            extend: 'Ext.data.Model',
            /**
             * @cfg {Array}
             * 字段
             */
            fields: [this.idProperty, 'subject', 'content', 'add_time', 'template_name', 'update_time', 'memo', 'sort_order'],
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
     * @member Yap.controller.Mail
     */
    defineStore: function() {
        /**
         * 博客数据容器
         */
        Ext.define('Yap.store.Mail', {
            extend: 'Ext.data.Store',
            /**
             * @cfg {Boolean}
             * 自动消毁
             */
            autoDestroy: true,

            /**
             * @cfg {Object/String}
             * 模型
             */
            model: 'Yap.model.Mail',
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
                property : 'sort_order',
                direction: 'ASC'
            },
            constructor: function(config) {//构造函数
                this.callParent([config || {}]);
            }
        });

        return this;
    }//end defineStore
});

//放到最后，以符合生成jsduck类说明
Ext.data.JsonP.Yap_controller_Mail(Yap.controller.Mail);