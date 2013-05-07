/**
 * 全局js
 *
 * @file            global.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 * @lastmodify      $Date$ $Author$
 */

var HOME_FLAG = 'home',//首页标识
    BLOG_FLAG = 'blog',//博客标识
    MINIBLOG_FLAG = 'miniblog',//微博标识
    GUESTBOOK_FLAG = 'guestbook';//留言标识

var _c = Math.random();

seajs.config({//seajs配置
    plugins: ['shim'],
    map: [
        //['.js', '.js?' + new Date().getTime()]
    ],
    alias: {
        lang: {//语言包
            src: System.sys_base_site_url + 'static/js/lang/zh_cn.js?' + _c
        },
        jquery: {//jquery
            src: System.sys_base_common_imgcache + 'js/jquery/jquery-1.9.1.min.js?' + _c,
        },
        comments: {//留言评论
            src: System.sys_base_js_url + 'comments.js?' + _c,
            deps: ['lang']
        }
    }
});

seajs.use(['jquery'], bootstrap);

/**
 * 启动函数
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 17:22:16
 *
 * @return void 无返回值
 */
function bootstrap() {
    window.$html = $('html');
    window.$body = $('body');
    navDropdown();//下拉菜单
    showMiniblogDetailLink();//非微博详情页，鼠标滑过微博，显示微博详情入口，同时隐藏添加时间
    getMetaInfo();//获取博客,微博元数据,包括点击量,评论数等

    if ($('#form-panel').length) {//评论留言

        seajs.use(['comments', 'lang'], function() {
            showCommentsReply();//鼠标滑过留言评论，显示回复
            addComments();//添加留言或者评论
        });
    }

    $('#nav-' + NAV_ID).addClass('active');//高亮导航
}

/**
 * 获取博客,微博元数据,包括点击量,评论数等
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-02 16:23:34
 *
 * @return void 无返回值
 */
function getMetaInfo() {
    'undefined' != typeof(META_INFO) && $.post(System.sys_base_site_url + 'ajax/metainfo.shtml', $.param(META_INFO), setMetaInfo);
}

/**
 * 设置或获取语言，支持批量
 *
 * @member window
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-07-04 11:17:12
 * @lastmodify      2013-01-12 16:19:06 by mrmsl
 *
 * @param {Mixed} name  名
 * @param {Mixed} value 值
 *
 * @return {Mixed} 如果不传参数name，将返回整个语言包；否则返回指定语言
 */
function lang(name, value) {

    if (!name) {//返回整个语言包
        return L;
    }
    else if (undefined !== value) {//单个
        L[name.toUpperCase()] = value;
        return L;
    }
    else {//取值
        var _lang = '';

        $.each(name.split(','), function(index, item) {

            if (0 == item.indexOf('%')) {//支持原形返回
                _lang += item.substr(1);
            }
            else {//如果设置值，返回值，否则只返回键名
                item = item.toUpperCase();
                _lang += undefined === L[item] ? item : L[item]
            }

        });

        return _lang;
    }
}//end lang

/**
 * 导航菜单下拉
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 *
 * @return void 无返回值
 */
function navDropdown() {
    var me = $('#nav-category'),
    dropdowns = me.find(' > ul.dropdown-menu');

    me.hover(function() {
        dropdowns.show();
    }, function() {
        dropdowns.hide();
    });
}

/**
 * 获取博客,微博元数据,包括点击量,评论数等
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-02 16:23:34
 *
 * @return void 无返回值
 */
function setMetaInfo(data) {

    if (data && data.success) {

        data.blog && $.each(data.blog, function(index, item) {
            $('.blog-diggs-' + index).text(item.diggs);
            $('.blog-hits-' + index).text(item.hits);
            $('.blog-comments-' + index).text(item.comments);
        });

        data.miniblog && $.each(data.miniblog, function(index, item) {
            $('.miniblog-diggs-' + index).text(item.diggs);
            $('.miniblog-hits-' + index).text(item.hits);
            $('.miniblog-comments-' + index).text(item.comments);
        });
    }
}

/**
 * 非微博详情页，鼠标滑过微博，显示微博详情入口，同时隐藏添加时间
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 22:00:47
 *
 * @return void 无返回值
 */
function showMiniblogDetailLink() {

    if (HOME_FLAG == NAV_ID || MINIBLOG_FLAG == NAV_ID && ('undefined' == typeof(IS_MINIBLOG_DETAIL))) {
        $('.miniblog-info').hover(function() {
            var me = $(this);
            me.find('.add_time').hide();
            me.find('.link').show();
        }, function() {
            var me = $(this);
            me.find('.add_time').show();
            me.find('.link').hide();
        });
    }
}