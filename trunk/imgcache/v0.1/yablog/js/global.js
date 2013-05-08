/**
 * 全局js
 *
 * @file            global.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 * @lastmodify      $Date$ $Author$
 */

var HOME_FLAG = 'index',//首页标识
    BLOG_FLAG = 'blog',//博客标识
    MINIBLOG_FLAG = 'miniblog',//微博标识
    GUESTBOOK_FLAG = 'guestbook',//留言标识
    IS_OLD_IE = /msie (6|7|8)/i.test(navigator.userAgent),//IE6-8,不支持html5,比如<input required,<input type="url"等
    _c = Math.random();

//字符串格式化输出
String.prototype.format = function() {

    if (typeof arguments[0] == 'object') {//json形，如'a{a}b{b}'.format({a: 'a', b: 'b'}) => aabb
        var args = arguments[0], pattern = /\{(\w+)\}/g;
    }
    else {//数字形，如format('a{1}b{2}', 'a', 'b') => aabb
        var args = arguments, pattern = /\{(\d+)\}/g;
    }

    return this.replace(pattern, function(m, i) {
        return args[i];
    });
};

//去除左右空白，支持自定义需要去除的字符列表 by mrmsl on 2012-07-28 10:29:41
String.prototype.ltrim = function(charlist, mode) {
    var patten = new RegExp('^' + (charlist || '\\s+'), mode || 'g');
    return this.replace(patten, '');
};
String.prototype.rtrim = function(charlist, mode) {
    var patten = new RegExp((charlist || '\\s+') + '$', mode || 'g');
    return this.replace(patten, '');
};
String.prototype.trim = function(charlist, mode) {
    charlist = charlist || '\\s';
    var patten = new RegExp('^' + charlist + '+|' + charlist + '+' + '$', mode || 'g');
    return this.replace(patten, '');
};

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
            src: System.sys_base_common_imgcache + 'js/jquery/jquery-1.9.1.min.js?' + _c
        },
        comments: {//留言评论
            src: System.sys_base_js_url + 'comments.js?' + _c
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
 * @return {void} 无返回值
 */
function bootstrap() {
    window.$html = $('html');
    window.$body = $('body');
    navDropdown();//下拉菜单
    showMiniblogDetailLink();//非微博详情页，鼠标滑过微博，显示微博详情入口，同时隐藏添加时间
    getMetaInfo();//获取博客,微博元数据,包括点击量,评论数等
    resetTime();//重置时间，即显示为 刚刚、5分钟前、3小时前、昨天10:23、前天15：26等

    if ($('#form-panel').length) {//评论留言

        seajs.use('comments', function() {
            showCommentsReply();//鼠标滑过留言评论，显示回复
            addComments();//添加留言或者评论
        });
    }

    $('#nav-' + NAV_ID).addClass('active');//高亮导航
}

/**
 * 格式化时间，类似php date函数
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 10:55:29
 *
 * @param {string} [format=System.sys_timezone_datetime_format] 格式
 * @param {mixed} [constructor] new Date()初始化参数
 *
 * @return {string} 格式化后的时间
 */
function date(format, constructor) {

    if (typeof(constructor) == 'object') {//已经是日期类型
        var datetime = constructor;
    }
    else {
        var datetime = constructor ? new Date(constructor) : new Date();
    }

    format = format || System.sys_timezone_datetime_format;

    var o = {
        'Y': datetime.getFullYear(),
        'm': datetime.getMonth() + 1,
        'd': datetime.getDate(),
        'H': datetime.getHours(),
        'i': datetime.getMinutes(),
        's': datetime.getSeconds()
    };

    for (var i in o) {
        _s = i == 'Y' ? o[i] : str_pad(o[i], 2, '0');//不为年，补0
        format = format.replace(new RegExp(i, 'g'), _s);
    }

    return format;
}

/**
 * 获取博客,微博元数据,包括点击量,评论数等
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-02 16:23:34
 *
 * @return {void} 无返回值
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
 * @date            2013-05-08 11:01:04
 *
 * @param {string} name 名
 * @param {mixed} [value] 值
 *
 * @return {mixed} 如果不传参数name，将返回整个语言包；否则返回指定语言
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
                _lang += undefined === L[item] ? item : L[item];
            }

        });

        return _lang;
    }
}//end lang

/**
 * console.log
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-07 21:38:55
 *
 * @return {void} 无返回值
 */
function log() {

    if ('undefined' != typeof(console)) {

        for (var i = 0, len = arguments.length; i < len; i++) {
            console.log(arguments[i]);
        }
    }
}

/**
 * 导航菜单下拉
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 *
 * @return {void} 无返回值
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
 * 重置时间，即显示为 刚刚、5分钟前、3小时前、昨天10:23、前天15：26等
 *
 * @author              mashanling(msl-138@163.com)
 * @date                2013-05-08 10:49:51
 *
 * @return {void} 无返回值
 */
function resetTime() {

    $('.time-axis').each(function (index, item) {
        $(item).text(timeAxis($(item).data('time')));
    });

    setInterval(resetTime, 60000);
}

/**
 * 获取博客,微博元数据,包括点击量,评论数等
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-02 16:23:34
 *
 * @return {void} 无返回值
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
 * @return {void} 无返回值
 */
function showMiniblogDetailLink() {

    if ((HOME_FLAG == NAV_ID || MINIBLOG_FLAG == NAV_ID) && 'undefined' == typeof(IS_MINIBLOG_DETAIL)) {
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

/**
 * 使用另一个字符串填充字符串为指定长度。类似php str_pad
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 11:17:26
 *
 * @param {string} string 待填充字符串
 * @param {int} [lendgh=10] 总长度
 * @param {string} [pad=' '] 填充字符
 * @param {string} [padType=undefined] 填充类型，right为右填充
 *
 * @return {string} 填充后的字符串
 */
function str_pad(str, length, pad, padType) {
    str = String(str);
    length = length ? length : 10;
    pad = pad == undefined ? ' ' : pad;

    while (str.length < length) {
        str = padType == 'right' ? str + pad : pad + str;
    }

    return str;
}

/**
 * 时间轴，即显示为 刚刚、5分钟前、3小时前、昨天10:23、前天15：26等
 *
 * @author              mashanling(msl-138@163.com)
 * @date                2013-05-08 10:49:44
 *
 * @param {int} time unix时间戳
 *
 * @return {string} 格式化显示的时间
 */
function timeAxis(time) {
    var str,
        now = new Date().getTime() / 1000
        diff = now - time;

    if (diff < 60) {
        return lang('JUST_NOW');
    }
    else if (diff < 3600) {
        return lang('MINUTES_AGO').format(Math.floor(diff / 60));
    }
    else if (diff < 86400) {
        return lang('HOURS_AGO').format(Math.floor(diff / 3600));
    }
    else if (diff < 86400 * 3) {
        return lang(1 == Math.floor(diff / 86400) ? 'YESTERDAY' : 'THE_DAY_BEFORE_YESTERDAY') + date(' H:i', time * 1000)
    }

    return date(null, time * 1000);
}