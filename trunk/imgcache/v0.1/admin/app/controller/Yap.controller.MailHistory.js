/**
 * 邮件历史控制器
 *
 * @file            app/controller/Yap.controller.MailHistory.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-06-07 12:39:58
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.MailHistory', {
    extend: 'Yap.controller.Base',
    /**
     * @cfg {String}
     * 主键
     */
    idProperty: 'history_id',
    /**
     * @cfg {String}
     * 查询字段
     */
    queryField: 'sort,order,date_start,date_end,column,keyword,template_id,template_name,email,subject,content,page,match_mode',//查询字段

    constructor: function() {//构造函数
        this.defineModel().defineStore();
    },

    /**
     * @inheritdoc Yap.controller.Base#listAction
     */
    listAction: function(data) {
        var me = this;

        data.sort = data.sort || this.idProperty;//排序字段
        data.order = data.order || 'DESC';//排序
        data.date_start = data.date_start || '';
        data.date_end = data.date_end || '';
        data.keyword = data.keyword || '';
        data.template_id = data.template_id || '';
        data.column = data.column || 'email';
        data.match_mode = data.match_mode || 'eq';//匹配模式
        data.page = intval(data.page) || 1;//页

        var options = {
            onItemClick: function(view, record, element, index, event) {//列表点击事件
                //me.listitemclick(record, event, 'is_delete');
                //me.listitemclick(record, event, 'is_issue');
            }
        };
        this.callParent([data, options]);//通用列表
    },

    /**
     * @inheritdoc Yap.controller.Admin#getListColumns
     */
    getListColumns: function() {
        var me = this;

        return [{
            text: 'id',//id
            width: 50,
            dataIndex: this.idProperty
        }, {
            header: lang('EMAIL'),//排序
            dataIndex: 'email',
            width: 120,
            align: 'center'
        }, {
            header: lang('BELONG_TO,MAIL_TEMPLATE'),//模板名
            width: 150,
            dataIndex: 'template_name'
        }, {
            header: lang('MAIL_SUBJECT'),//备注
            flex: 1,
            dataIndex: 'subject',
            sortable: false
        }, {//操作列
            width: 160,
            xtype: 'appactioncolumn',
            items: [{
                renderer: function(v, meta, record) {//编辑
                    return '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('EDIT') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    me.edit(grid.getStore().getAt(rowIndex));
                }
            }, {
                renderer: function(v, meta, record) {//删除
                    return '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('DELETE') + '</span>';
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
            displayInfo: true,
            listeners: {

                /**
                 * 分页前
                 *
                 * @ignore
                 *
                 * @param {Object} paging 分页条
                 * @param {Number} page      将分至页
                 *
                 * @return {void} 无返回值
                 */
                beforechange: function(paging, page) {
                    this.changed = true;
                },

                /**
                 * 分页后
                 *
                 * @ignore
                 *
                 * @param {Object} grid     列表grid
                 * @param {Object} pageData 分类数据
                 *
                 * @return {void} 无返回值
                 */
                change: function(grid, pageData) {
                    if (pageData && !isNaN(pageData.pageCount) && this.changed) {//保证经过beforechange
                        data = {
                            page: pageData.currentPage,
                            sort: data.sort,
                            order: data.order
                        };
                        data.page != _GET('page') && me.store(me.setHistory(data));
                        this.changed = false;
                    }
                }
            }
        };
    },//end pagingBar

    /**
     * @inheritdoc Yap.controller.Admin#store
     */
    store: function(data) {

        if (!this._store) {//未创建

            this._store = Ext.create('Yap.store.MailHistory');
        }

        if (data) {
            var sorters = this._store.sorters.getAt(0);//排序

            //排序不一致，重新设置 by mrmsl on 2012-07-27 15:45:18
            if (sorters.property != data.sort || sorters.direction != data.order) {
                this._store.sorters.clear();
                this._store.sorters.add(new Ext.util.Sorter({
                    property: data.sort,
                    direction: data.order
                }));
            }

            this._store._data = this.httpBuildQuery(data, this.queryField);
            this._store.proxy.url = this.getActionUrl(false, 'list', this.httpBuildQuery(data, this.queryField.split(',').slice(2)));
        }

        return this._store;
    },//end store

    /**
     * @inheritdoc Yap.controller.Admin#tbar
     */
    tbar: function(data) {
        var me = this, extField = Yap.Field.field(), extCombo = Yap.Field.combo();

        return {
            xtype: 'toolbar',
            layout: {
                overflowHandler: 'Menu'
            },
            dock: 'top',
            items: [{
                text: lang('OPERATE'),
                itemId: 'btn',
                menu: [this.deleteItem(), {
                    text: lang('DELETE'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel);
                        selection.length && me.deleteBlogHtml(selection);
                    }
                }]
            }, '-', lang('ADD,TIME,CN_CONG'),
            extField.dateField({itemId: 'date_start'}), lang('TO'),
            extField.dateField({itemId: 'date_end'}), '-', lang('BELONG_TO,MAIL_TEMPLATE'),
            extField.hiddenField('template_id'),//template_id
            /*{
                xtype: 'treepicker',
                width: 150,
                itemId: 'template_name',
                value: data.cate_id,
                emptyText: lang('BELONG_TO_CATEGORY'),
                displayField: 'cate_name',
                pickerIdProperty: 'cate_id',
                store: Ext.create('Yap.store.Category', {
                    folderSort: false,
                    url: this.getActionUrl('category', 'publicCategory', 'unshift&parent_id={0}&emptyText={1}'.format(data.cate_id, lang('BELONG_TO_CATEGORY')))
                })
            }, */{
                xtype: 'combobox',//搜索字段
                width: 70,
                itemId: 'column',
                store: [
                    ['email', lang('EMAIL')],
                    ['subject', lang('MAIL_SUBJECT')],
                    ['content', lang('MAIL_CONTENT')]
                ],
                value: data.column,
                editable: false
            },
            extCombo.matchMode(),//匹配模式
            extField.keywordField(data.keyword, {width: 120}),//关键字输入框
            this.btnSearch(function() {//搜索按钮
                var ownerCt = this.ownerCt;
                var hash = Ext.util.History.getToken();
                var data = Ext.Object.fromQueryString(hash);
                data.sort = data.sort || me.idProperty;
                data.order = data.order || 'DESC';
                data = me.getQueryData(ownerCt, data);

                me.store(me.setHistory(data)).loadPage(1);
            })]
        };
    },//end tbar

    //放到最后定义，否则，jsduck后，上面的方法将属于Yap.store.MailHistory或Yap.model.MailHistory
    /**
     * @inheritdoc Yap.controller.Field#defineModel
     */
    defineModel: function() {
        /**
         * 博客数据模型
         */
        Ext.define('Yap.model.MailHistory', {
            extend: 'Ext.data.Model',
            /**
             * @cfg {Array}
             * 字段
             */
            fields: [this.idProperty, 'subject', 'content', 'add_time', 'template_id', 'template_name', 'times', 'email'],
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
     * @member Yap.controller.MailHistory
     */
    defineStore: function() {
        /**
         * 博客数据容器
         */
        Ext.define('Yap.store.MailHistory', {
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
            remoteSort: true,

            /**
             * @cfg {Object/String}
             * 模型
             */
            model: 'Yap.model.MailHistory',
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
Ext.data.JsonP.Yap_controller_MailHistory(Yap.controller.MailHistory);