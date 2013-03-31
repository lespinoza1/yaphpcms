/**
 * 博客控制器
 *
 * @file            app/controller/Yap.controller.Blog.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-03-26 11:19:55
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.Blog', {
    extend: 'Yap.controller.Base',
    /**
     * @cfg {String}
     * 主键
     */
    idProperty: 'blog_id',
    /**
     * @cfg {String}
     * 查询字段
     */
    queryField: 'sort,order,date_start,date_end,column,keyword,cate_id,is_issue,is_delete,page,match_mode',//查询字段

    constructor: function() {//构造函数
        this.defineModel().defineStore();
    },

    /**
     * @inheritdoc Yap.controller.Base#addAction
     */
    addAction: function (data) {
        var me = this,
        options = {
            listeners: {
                submitsuccess: function (form, action) {
                    form.findField('cate_name').setValue(form.findField('_picker_cate_name').rawValue);
                    me._listgrid && form.findField(me.idProperty).getValue() == 0 && me.store().load();//新增
                }
            }
        };

        if (this.ueditor) {
            return me.superclass.addAction.apply(this, [data, options]);
        }

        seajs.use([UEDITOR_HOME_URL + 'editor_config', UEDITOR_HOME_URL + 'ueditor.min'], function() {
            Ext.require('Yap.ux.Ueditor', function () {
                me.ueditor = true;
                me.superclass.addAction.apply(this, [data, options]);
            }, me);
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
        var me = this, extField = Yap.Field.field(), extCombo = Yap.Field.combo();

        return [
            extField.fieldContainer(['TITLE', [//标题
                [null, 'title', 'PLEASE_ENTER,TITLE', false, '', {width: 400}],
                lang('LT_BYTE').format(60) + '，' + lang('CN_TO_BYTE')
            ]]),
            extField.fieldContainer(['FROM_NAME', [//来源名称
                [null, 'from_name', '', false, '', {width: 400}],
                lang('LT_BYTE').format(200)
            ], true]),
            extField.fieldContainer(['FROM_URL', [//来源url
                [null, 'from_url', '', false, '', {width: 400}],
                lang('LT_BYTE').format(200)
            ], true]),
            extField.fieldContainer('ADD,TIME', [//添加时间
                extField.dateField({name: 'add_time'}),
            ]),
            extField.hiddenField('cate_id'),//cate_id
            {
                xtype: 'treepicker',
                fieldLabel: TEXT.red() + lang('BELONG_TO_CATEGORY'),
                name: '_picker_cate_name',
                value: data.cate_id,
                emptyText: lang('BELONG_TO_CATEGORY'),
                displayField: 'cate_name',
                pickerIdProperty: 'cate_id',
                store: Ext.create('Yap.store.Category', {
                    folderSort: false,
                    url: this.getActionUrl('category', 'publicCategory', 'parent_id={0}'.format(data['cate_id']))
                }),
                storeOnLoad: function(store) {//添加指定分类子分类，设置指定分类相关信息
                    var data = store.proxy.reader.rawData;

                    if (data && data.parent_data) {
                        data = data.parent_data;
                        this.setRawValue(data.parent_name);
                     }
                }
            },
            //发布状态
            extField.fieldContainer('ISSUE,STATUS', [
                 extField.checkbox('is_issue', '', '', 'CN_WEI,ISSUE', '0', '', {xtype: 'radio'}),
                 extField.checkbox('is_issue', '', '', 'CN_YI,ISSUE', '1', '', {xtype: 'radio', style: 'margin-left: 15px'})
                ], true, {
                value: {is_issue: Ext.valueFrom(data.is_issue, '1')},
                xtype: 'radiogroup'
           }),
            //删除状态
            extField.fieldContainer('DELETE,STATUS', [
                 extField.checkbox('is_delete', '', '', 'CN_WEI,DELETE', '0', '', {xtype: 'radio'}),
                 extField.checkbox('is_delete', '', '', 'CN_YI,DELETE', '1', '', {xtype: 'radio', style: 'margin-left: 15px'})
                ], true, {
                value: {is_delete: Ext.valueFrom(data.is_delete, '0')},
                xtype: 'radiogroup'
           }),
            extField.sortOrderField(),//排序
            extField.textarea('seo_keyword', 'PLEASE_ENTER,SEO_KEYWORD', 'SEO_KEYWORD', '', {width: 800, height: 50, minLength: 6, maxLength: 300}),//SEO关键字
            extField.textareaComment(lang('BETWEEN_BYTE').format(6, 180)),//SEO关键字提示
            extField.textarea('seo_description', 'PLEASE_ENTER,SEO_DESCRIPTION', 'SEO_DESCRIPTION', '', {width: 800, height: 70, minLength: 6, maxLength: 300}),//SEO描述
            extField.textareaComment(lang('BETWEEN_BYTE').format(6, 300)),//SEO描述提示
            {
                xtype: 'ueditor',
                name: 'content',
                value: lang('PLEASE_ENTER,CONTENT'),
                fieldLabel: lang('CONTENT')
            },
            extField.hiddenField(),//blog_id
            extField.hiddenField('cate_name'),
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
            text: lang('MODULE_NAME_BLOG') + 'id',//博客id
            width: 60,
            dataIndex: this.idProperty
        }, {
            header: lang('TITLE'),//标题
            width: 300,
            dataIndex: 'title',
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'title');
            }
        }, {
            header: lang('BELONG_TO_CATEGORY'),//所属分类
            width: 120,
            dataIndex: 'cate_name',
            sortable: false
        }, {
            header: lang('ADD,TIME'),//添加时间
            dataIndex: 'add_time',
            width: 140,
            renderer: this.renderDatetime
        }, {
            header: lang('FROM_NAME'),//来源名称
            dataIndex: 'from_name',
            //flex: 1,
            width: 120,
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'from_name');
            },
            hidden: true,
            sortable: false
        }, {
            header: lang('FROM_URL'),//来源url
            dataIndex: 'from_url',
            //flex: 1,
            width: 200,
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'from_url');
            },
            hidden: true,
            sortable: false
        }/*, {
            header: lang('SEO_KEYWORD'),//seo关键字
            dataIndex: 'seo_keyword',
            //flex: 1,
            width: 200,
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'seo_keyword');
            },
            hidden: true,
            sortable: false
        }, {
            header: lang('SEO_DESCRIPTION'),//SEO描述
            dataIndex: 'seo_description',
            //flex: 1,
            minWidth: 200,
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'seo_description');
            },
            hidden: true,
            sortable: false
        }*/, {
            header: lang('HITS'),//点击次数
            dataIndex: 'hits',
            width: 80
        }, {
            header: lang('COMMENTS'),//点击次数
            dataIndex: 'comments',
            width: 80
        }, {
            header: lang('ISSUE'),//发布
            align: 'center',
            dataIndex: 'is_issue',
            width: 80,
            renderer: function(v) {
                return me.renderYesNoImg(v, 'is_issue');
            }
        }, {
            header: lang('DELETE'),//锁定
            align: 'center',
            dataIndex: 'is_delete',
            width: 60,
            renderer: function(v) {
                return me.renderYesNoImg(v, 'is_delete');
            }
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
                    me['delete'](record, '<span class="font-red">{0}</span>'.format(htmlspecialchars(record.get('title'))));
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
        data.cate_id = data.cate_id || '';
        data.column = data.column || 'title';
        data.match_mode = data.match_mode || 'eq';//匹配模式
        data.is_delete = Ext.valueFrom(data.is_delete, '-1');//删除
        data.is_issue = Ext.valueFrom(data.is_issue, '-1');//发布
        data.page = intval(data.page) || 1;//页

        var options = {
            onItemClick: function(view, record, element, index, event) {//列表点击事件
                me.listitemclick(record, event, 'is_delete');
                me.listitemclick(record, event, 'is_issue');
            }
        };
        this.callParent([data, options]);//通用列表
    },

    /**
     * @inheritdoc Yap.controller.Field#loadEditDataSuccess
     */
    loadEditDataSuccess: function(form, action) {
        form.findField('_picker_cate_name').setRawValue(action.result.data.cate_name);
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
     * @inheritdoc Yap.controller.Admin#store
     */
    store: function(data) {

        if (!this._store) {//未创建

            this._store = Ext.create('Yap.store.Blog');
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
     * 列表顶部工具栏
     *
     * @return {Object} Ext.tool.Toolbar工具栏配置项
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
                    text: lang('CN_WEI,ISSUE'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['is_issue', 1]);
                        selection.length && me.setOneOrZero(selection[0], 0, 'issue', lang('YOU_CONFIRM,CN_WEI,ISSUE,SELECTED,RECORD'), selection[1]);
                    }
                }, {
                    text: lang('CN_YI,ISSUE'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['is_issue', 0]);
                        selection.length && me.setOneOrZero(selection[0], 1, 'issue', lang('YOU_CONFIRM,RELEASE,CN_YI,ISSUE,SELECTED,RECORD'), selection[1]);
                    }
                }, {
                    text: lang('MOVE'),
                    menu: {
                        items: {
                            xtype: 'treepicker',
                            width: 200,
                            value: data.cate_id,
                            emptyText: lang('BELONG_TO_CATEGORY'),
                            displayField: 'cate_name',
                            pickerIdProperty: 'cate_id',
                            store: Ext.create('Yap.store.Category', {
                                folderSort: false,
                                url: this.getActionUrl('category', 'publicCategory', 'unshift&parent_id={0}'.format(data.cate_id))
                            }),
                            storeOnLoad: function(store) {//添加指定分类子分类，设置指定分类相关信息
                                var data = store.proxy.reader.rawData;

                                if (data && data.parent_data) {
                                    this.setRawValue(data.parent_data.parent_name);
                                 }
                            }
                        }
                    }
                }]
            }, '-', lang('ADD,TIME,CN_CONG'),
            extField.dateField({itemId: 'date_start'}), lang('TO'),
            extField.dateField({itemId: 'date_end'}), '-', lang('BELONG_TO_CATEGORY'),
            extField.textField('cate_id', false, false, false, {hidden: true}),//cate_id 搜索item.isXType('textfield)
            {
                xtype: 'treepicker',
                width: 150,
                name: 'cate_name',
                value: data.cate_id,
                emptyText: lang('BELONG_TO_CATEGORY'),
                displayField: 'cate_name',
                pickerIdProperty: 'cate_id',
                store: Ext.create('Yap.store.Category', {
                    folderSort: false,
                    url: this.getActionUrl('category', 'publicCategory', 'unshift&parent_id={0}&emptyText={1}'.format(data.cate_id, lang('BELONG_TO_CATEGORY')))
                })
            }, extCombo.base({//发布状态
                width: 80,
                itemId: 'is_issue',
                value: '-1',
                store: [
                    ['-1', lang('ISSUE,STATUS')],
                    ['0', lang('CN_WEI,ISSUE')],
                    ['1', lang('CN_YI,ISSUE')]
                ]
            }), extCombo.base({//锁定状态
                width: 80,
                itemId: 'is_delete',
                value: '-1',
                store: [
                    ['-1', lang('DELETE,STATUS')],
                    ['0', lang('CN_WEI,DELETE')],
                    ['1', lang('CN_YI,DELETE')]
                ]
            }), {
                xtype: 'combobox',//搜索字段
                width: 80,
                itemId: 'column',
                store: [
                    ['title', lang('TITLE')],
                    ['seo_keyword', lang('SEO_KEYWORD')],
                    ['seo_description', lang('SEO_DESCRIPTION')],
                    ['content', lang('CONTENT')],
                    ['from_name', lang('FROM_NAME')],
                    ['from_url', lang('FROM_URL')]
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
         * 博客数据模型
         */
        Ext.define('Yap.model.Blog', {
            extend: 'Ext.data.Model',
            /**
             * @cfg {Array}
             * 字段
             */
            fields: [this.idProperty, 'content', 'add_time', 'title', 'cate_name', 'is_issue', 'is_delete', 'sort_order','from_name', 'from_url', 'hits', 'comments', 'seo_keyword', 'seo_description'],
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
        Ext.define('Yap.store.Blog', {
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
            model: 'Yap.model.Blog',
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
Ext.data.JsonP.Yap_controller_Blog(Yap.controller.Blog);