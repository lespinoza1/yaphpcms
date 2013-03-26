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
    queryField: 'sort,order,date_start,date_end,column,keyword,cate_id,status,page,match_mode',//查询字段

    /**
     * @inheritdoc Yap.controller.Base#addAction
     */
    addAction: function (data) {
        var callParent = this.callParent;
        Ext.require('Yap.ux.Ueditor', function() {
            var me = this,
            options = {
                listeners: {
                    submitsuccess: function (form, action) {
                        if (me.getEditRecord()) {
                            //设置cate_name以form.updateRecord()更新所属分类
                            form.findField('cate_name').setValue(form.findField('cate_id').getDisplayValue());
                        }

                        me._listgrid && form.findField(me.idProperty).getValue() == 0 && me.store().load();//新增
                    }
                }
            };

            callParent([data, options]);
        }, this);
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
            extField.fieldContainer('ADD,TIME', [//添加时间
                extField.dateField({name: 'add_time'}),
            ]), {
                xtype: 'treepicker',
                fieldLabel: TEXT.red() + lang('BELONG_TO_CATEGORY'),
                name: '_parent_name',
                value: data.cate_id,
                singleSelectValueField: 'parent_id',
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
                         var form = this.up('form').getForm();
                         /*form.setValues({
                             cate_id: data[me.idProperty]//父级id
                         });*/
                         me.loadEditDataSuccess(form, {//其它信息，包括父级名称，及初始化权限
                             result: {
                                 data: data
                             }
                         });
                     }
                }
            },
            extCombo.base({//发布状态
                width: 170,
                fieldLabel: lang('ISSUE,STATUS'),
                itemId: 'status',
                value: '1',
                store: [
                    ['0', lang('CN_WEI,ISSUE')],
                    ['1', lang('CN_YI,ISSUE')],
                    ['2', lang('CN_YI,DELETE')]
                ]
            }),
            extField.textarea('seo_keyword', 'PLEASE_ENTER,SEO_KEYWORD', 'SEO_KEYWORD', '', {width: 800, height: 50, minLength: 6, maxLength: 300}),//SEO关键字
            extField.textareaComment(lang('BETWEEN_BYTE').format(6, 180)),//SEO关键字提示
            extField.textarea('seo_description', 'PLEASE_ENTER,SEO_DESCRIPTION', 'SEO_DESCRIPTION', '', {width: 800, height: 70, minLength: 6, maxLength: 300}),//SEO描述
            extField.textareaComment(lang('BETWEEN_BYTE').format(6, 300)),//SEO描述提示
            extField.hiddenField(),//blog_id
            extField.hiddenField('cate_name'), //增加cate_name以form.updateRecord()更新所属分类
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
            text: lang('USER') + 'id',//用户id
            width: 60,
            dataIndex: this.idProperty
        }, {
            header: lang('USERNAME'),//用户名
            width: 120,
            dataIndex: 'username',
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'username');
            }
        }, {
            header: lang('REALNAME'),//真实姓名
            width: 100,
            dataIndex: 'realname',
            renderer: function(v) {
                return me.searchReplaceRenderer(v, 'realname');
            },
            sortable: false
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
            header: lang('LAST,LOGIN,TIME'),//最后登陆时间
            dataIndex: 'last_login_time',
            width: 140,
            renderer: this.renderDatetime
        }, {
            header: lang('LAST,LOGIN') + 'ip',//最后登陆ip
            dataIndex: 'last_login_ip',
            width: 120,
            sortable: false
        }, {
            header: lang('LOGIN,CN_CISHU'),//登陆次数
            dataIndex: 'login_num',
            width: 80
        }, {
            header: lang('CN_BANGDING,LOGIN'),//绑定登陆
            align: 'center',
            dataIndex: 'is_restrict',
            width: 80,
            renderer: function(v) {
                return me.renderYesNoImg(v, 'is_restrict');
            }
        }, {
            header: lang('LOCK'),//锁定
            align: 'center',
            dataIndex: 'is_lock',
            width: 60,
            renderer: function(v) {
                return me.renderYesNoImg(v, 'is_lock');
            }
        }, {//操作列
            flex: 1,
            xtype: 'appactioncolumn',
            items: [{//编辑
                renderer: function(v, meta, record) {
                    return record.get(me.idProperty) == ADMIN_ID && ADMIN_INFO.id != ADMIN_ID ? '' : '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('EDIT') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    me.edit(record, true, 'cate_id=' + record.get('cate_id'));
                }
            }, {//删除
                renderer: function(v, meta, record) {
                    return record.get(me.idProperty) == ADMIN_ID ? '' : '<span class="appactioncolumn appactioncolumn-'+ this +'">' + lang('DELETE') + '</span>';
                },
                handler: function(grid, rowIndex, cellIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    me['delete'](record, '<span class="font-red">{0}</span>(<span class="font-bold font-666">{1}</span>)'.format(htmlspecialchars(record.get('username')), htmlspecialchars(record.get('realname'))));
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
        data.column = data.column || 'username';
        data.match_mode = data.match_mode || 'eq';//匹配模式
        data.is_lock = Ext.valueFrom(data.is_lock, '-1');//锁定状态
        data.is_restrict = Ext.valueFrom(data.is_restrict, '-1');//绑定登陆状态
        data.page = intval(data.page) || 1;//页

        var options = {
            onItemClick: function(view, record, element, index, event) {//列表点击事件
                me.listitemclick(record, event, 'is_restrict');
                me.listitemclick(record, event, 'is_lock');//锁定
            }
        };
        this.callParent([data, options]);//通用列表
    },

    /**
     * @inheritdoc Yap.controller.Field#loadEditDataSuccess
     */
    loadEditDataSuccess: function(form, action) {
        var data = action.result.data;
        form.findField('_parent_name').setRawValue(data.parent_name);
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
                    if (!isNaN(pageData.pageCount) && this.changed) {//保证经过beforechange
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

        this._store = this._store || Ext.create('Yap.store.Admin');

        if (data) {
            var sorters = this._store.sorters.getAt(0);//排序

            //排序不一致，重新设置
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
            dock: 'top',
            items: [{
                text: lang('OPERATE'),
                itemId: 'btn',
                menu: [this.deleteItem(), {
                    text: lang('CN_BANGDING,LOGIN'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['is_restrict', 0]);
                        selection.length && me.setOneOrZero(selection[0], 1, 'is_restrict', lang('YOU_CONFIRM,CN_BANGDING,LOGIN,SELECTED,RECORD'), selection[1]);
                    }
                }, {
                    text: lang('RELEASE,CN_BANGDING,LOGIN'),
                    handler: function() {
                        var selection = me.hasSelect(me.selectModel, ['is_restrict', 1]);
                        selection.length && me.setOneOrZero(selection[0], 0, 'is_restrict', lang('YOU_CONFIRM,RELEASE,CN_BANGDING,LOGIN,SELECTED,RECORD'), selection[1]);
                    }
                }, {
                    text: lang('MOVE'),
                    menu: {
                        items: {
                            xtype: 'rolecombo',
                            listeners: {
                                select: function(combo, record) {
                                    this.ownerCt.parentMenu.hide();//隐藏菜单
                                    record = record[0];
                                    var selection = me.hasSelect(me.selectModel, me.idProperty);

                                    if (selection) {
                                        me.myConfirm({
                                            action: me.getActionUrl(false, 'move'),
                                            data: {
                                                blog_id: selection[0],
                                                cate_id: record.get('cate_id')
                                            },
                                            confirmText: lang('YOU_CONFIRM,MOVE,SELECTED,RECORD,TO') + '<strong style="font-weight: bold; color: red">' + record.get('cate_name') + '</strong>',
                                            failedMsg: lang('MOVE,FALIURE'),
                                            scope: me,
                                            store: me.store()
                                        });
                                    }
                                }
                            }
                        }
                    }
                }]
            }, '-', lang('ADD,TIME,CN_CONG'),
            extField.dateField({itemId: 'date_start'}), lang('TO'),
            extField.dateField({itemId: 'date_end'}), '-', lang('BELONG_TO_CATEGORY'), {
                xtype: 'rolecombo',
                url: this.getActionUrl('role', 'list', 'unshift'),
                value: data.cate_id
            }, extCombo.base({//绑定登陆状态
                width: 80,
                itemId: 'is_restrict',
                value: '-1',
                store: [
                    ['-1', lang('CN_BANGDING,STATUS')],
                    ['0', lang('CN_WEI,CN_BANGDING')],
                    ['1', lang('CN_YI,CN_BANGDING')]
                ]
            }), extCombo.base({//锁定状态
                width: 80,
                itemId: 'is_lock',
                value: '-1',
                store: [
                    ['-1', lang('LOCK,STATUS')],
                    ['0', lang('CN_WEI,LOCK')],
                    ['1', lang('CN_YI,LOCK')]
                ]
            }), {
                xtype: 'combobox',//搜索字段
                width: 80,
                itemId: 'column',
                store: [
                    ['username', lang('USERNAME')],
                    ['realname', lang('REALNAME')]
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
    }//end tbar
});

//放到最后，以符合生成jsduck类说明
Ext.data.JsonP.Yap_controller_Blog(Yap.controller.Blog);