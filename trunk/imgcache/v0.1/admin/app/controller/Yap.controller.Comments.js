/**
 * 留言评论控制器
 *
 * @file            app/controller/Yap.controller.Comments.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-28 11:25:45
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.Comments', {
    extend: 'Yap.controller.Base',
    /**
     * @cfg {String}
     * 主键
     */
    idProperty: 'comment_id',
    /**
     * @property {Array}
     * 类型
     */
    typeArr: [
        ['0', lang('GUESTBOOK')],
        ['1', lang('BLOG,COMMENT')],
        ['2', lang('MINIBLOG,COMMENT')]
    ],
    /**
     * @cfg {String}
     * 查询字段
     */
    queryField: 'sort,order,date_start,date_end,column,keyword,type,auditing,page,match_mode',//查询字段

    /**
     * @inheritdoc Yap.controller.Base#addAction
     */
    addAction: function (data) {
        var me = this,
        options = {
            listeners: {
                submitsuccess: function (form, action) {
                     me.store().load();
                }
            }
        };

        if (this.ueditor) {
            return me.superclass.addAction.apply(this, [data, options]);
        }

        seajs.use(['ueditor', 'ueditorConfig'], function() {
            Ext.require('Yap.ux.Ueditor', function () {
                me.ueditor = true;
                me.superclass.addAction.apply(this, [data, options]);
            }, me);
        });
    },

    constructor: function() {//构造函数
        this.defineModel().defineStore();
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
        var me = this, extField = Yap.Field.field(), extCombo = Yap.Field.combo();

        return [
            //发布状态
            extField.fieldContainer('ISSUE,STATUS', [
                 extField.checkbox('is_issue', '', '', 'CN_WEI,ISSUE', '0', '', {xtype: 'radio'}),
                 extField.checkbox('is_issue', '', '', 'CN_YI,ISSUE', '1', '', {xtype: 'radio', style: 'margin-left: 15px'})
                ], true, {
                value: {is_issue: Ext.valueFrom(data.is_issue, '1')},
                xtype: 'radiogroup'
           }),
            {//摘要
                xtype: 'ueditor',
                name: 'summary',
                //value: lang('PLEASE_ENTER,CONTENT'),
                fieldLabel: lang('SUMMARY')
            },
            extField.textareaComment(lang('SUMMARY_TIP').format(300)),//SEO描述提示
            {//内容
                xtype: 'ueditor',
                name: 'content',
                value: lang('PLEASE_ENTER,CONTENT'),
                fieldLabel: lang('CONTENT')
            },
            extField.hiddenField(),//comment_id
            this.btnSubmit()//通用提交按钮
        ]
    },

    /**
     * 获取数据列
     *
     * @return {Array} 数据列配置
     */
    getListColumns: function() {
        var me = this, statusArr = [TEXT.gray(lang('CN_WEI,AUDITING')), TEXT.green(lang('CN_YI,PASS')), TEXT.red(lang('CN_WEI,PASS'))];

        return [{
            text: 'id',//id
            width: 60,
            dataIndex: this.idProperty
        }, {
            header: lang('USERNAME'),//用户
            width: 100,
            dataIndex: 'username',
            renderer: function(v, cls, record) {
                return record.get('user_homepage') ? '<a href="{0}" target="_blank" class="link">{1}'.format(record.get('user_homepage'), me.searchReplaceRenderer(v, 'username')) : me.searchReplaceRenderer(v, 'username');
            },
            sortable: false
        }, {
            header: lang('CONTENT'),//内容
            minWidth: 400,
            dataIndex: 'content',
            renderer: function (v) {
                return me.searchReplaceRenderer(strip_tags(v), 'content');
            },
            sortable: false
        }, {
            header: lang('EMAIL'),//邮箱
            align: 'center',
            width: 120,
            dataIndex: 'email',
            renderer: function (v) {
                me.searchReplaceRenderer(v, 'email');
            },
            sortable: false
        }, {
            header: lang('ADD,TIME'),//添加时间
            align: 'center',
            dataIndex: 'add_time',
            width: 140,
            renderer: this.renderDatetime,
            sortable: false
        }, {
            header: lang('type'),//类型
            align: 'center',
            dataIndex: 'type',
            width: 100,
            renderer: function(v, cls, record) {
                return me.typeArr[v][1];
            },
            sortable: false
        }, {
            header: lang('STATUS'),//状态
            align: 'center',
            dataIndex: 'status',
            width: 80,
            renderer: function(v, cls, record) {
                return statusArr[v];
            },
            sortable: false
        }, {//操作列
            flex: 1,
            xtype: 'appactioncolumn',
            items: [{//编辑
                renderer: function(v, meta, record) {
                    return '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('EDIT') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    me.edit(record, true, 'cate_id=' + record.get('cate_id'));
                }
            }, {//删除
                renderer: function(v, meta, record) {
                    return '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('DELETE') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    me['delete'](record, lang('CN_CI,' + (0 == record.get('type') ? 'GUESTBOOK' : 'COMMENT')));
                }
            }]
        }];
    },//end getListColumns

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
        data.column = data.column || 'username';
        data.match_mode = data.match_mode || 'eq';//匹配模式
        data.type = Ext.valueFrom(data.type, '-1');//类型
        data.auditing = Ext.valueFrom(data.auditing, '-1');//审核状态
        data.page = intval(data.page) || 1;//页

        var options = {
            onItemClick: function(view, record, element, index, event) {//列表点击事件
                //me.listitemclick(record, event, 'is_restrict');
                //me.listitemclick(record, event, 'is_lock');//锁定 by mrmsl on 2012-09-05 17:35:48
            }
        };
        this.callParent([data, options]);//通用列表
    },

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
     * 列表页store
     *
     * @return {Object} Ext.data.Store
     */
    store: function(data) {

        this._store = this._store || Ext.create('Yap.store.Comments');

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
     * 列表顶部工具栏
     *
     * @return {Object} Ext.tool.Toolbar工具栏配置项
     */
    tbar: function(data) {
        var me = this, extField = Yap.Field.field(), extCombo = Yap.Field.combo(), typeArr = Ext.Array.clone(me.typeArr);
        typeArr.unshift(['-1', lang('TYPE')]);

        return {
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: lang('OPERATE'),
                itemId: 'btn',
                menu: [this.deleteItem(), {
                    text: lang('PASS'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['status', [0, 2]]);
                        selection.length && me.setOneOrZero(selection[0], 1, 'auditing', lang('YOU_CONFIRM,PASS,SELECTED,RECORD'), selection[1]);
                    }
                }, {
                    text: lang('NO,PASS'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['status', [0, 1]]);
                        selection.length && me.setOneOrZero(selection[0], 2, 'auditing', lang('YOU_CONFIRM,NO,PASS,SELECTED,RECORD'), selection[1]);
                    }
                }, {
                    text: lang('CN_WEI,AUDITING'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['status', [1, 2]]);
                        selection.length && me.setOneOrZero(selection[0], 0, 'auditing', lang('YOU_CONFIRM,CN_WEI,AUDITING,SELECTED,RECORD'), selection[1]);
                    }
                }]
            }, '-', lang('ADD,TIME,CN_CONG'),
            extField.dateField({itemId: 'date_start'}), lang('TO'),
            extField.dateField({itemId: 'date_end'}), '-',
            extCombo.base({//类型
                width: 80,
                itemId: 'type',
                value: '-1',
                store: typeArr
            }),
            extCombo.auditing(),//审核状态
            {
                xtype: 'combobox',//搜索字段
                width: 80,
                itemId: 'column',
                store: [
                    ['username', lang('USERNAME')],
                    ['email', lang('EMAIL')],
                    ['content', lang('CONTENT')],
                    ['blog_title', lang('BLOG,TITLE')],
                    ['blog_content', lang('BLOG,CONTENT')],
                    ['blog_id', lang('BLOG') + 'id'],
                    ['miniblog_id', lang('MINIBLOG') + 'id'],
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

    //放到最后定义，否则，jsduck后，上面的方法将属于Yap.store.Blog或Yap.model.Blog
    /**
     * @inheritdoc Yap.controller.Field#defineModel
     */
    defineModel: function() {
        /**
         * 留言评论数据模型
         */
        Ext.define('Yap.model.Comments', {
            extend: 'Ext.data.Model',
            /**
             * @cfg {Array}
             * 字段
             */
            fields: [this.idProperty, 'blog_id', 'content', 'add_time', 'last_reply_time', 'username', 'user_ip', 'username', 'email','user_homepage', 'status', 'type', 'at_email', 'province', 'city','is_admin'],
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
     * @member Yap.controller.Blog
     */
    defineStore: function() {
        /**
         * 博客数据容器
         */
        Ext.define('Yap.store.Comments', {
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
            model: 'Yap.model.Comments',
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
            //增加排序，以防点击列表头部排序时，多余传参，出现不必要的错误
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
Ext.data.JsonP.Yap_controller_Comments(Yap.controller.Comments);