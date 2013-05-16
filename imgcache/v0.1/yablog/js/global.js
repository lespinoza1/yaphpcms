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
        tagCloud: {//标签云
            src: System.sys_base_common_imgcache + 'js/jquery/jquery.3DTagCloud.js?' + _c,
            deps: ['jquery']
        },
        comments: {//留言评论
            src: System.sys_base_js_url + 'comments.js?' + _c
        }
    }
});

seajs.use(['jquery'], bootstrap);

/**
 * 获取参数，类似php $_GET。不支持获取数组
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 14:33:00
 *
 * @param {string} name 参数名称
 * @param {string} [str=location.href]  匹配字符串
 *
 * @return {string} 参数值或空字符串
 */
function _GET(name, str) {
    var pattern = new RegExp('[\?&]' + name + '=([^&]+)', 'g');
    str = str || location.href;
    var arr, match = '';

    while ((arr = pattern.exec(str)) !== null) {
        match = arr[1];
    }

    return match;
}

/**
 * 启动函数
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 17:22:16
 *
 * @return {void} 无返回值
 */
function bootstrap() {
    window.$html = $('html,body');
    window.$body = $('body');
    navDropdown();//下拉菜单
    showMiniblogDetailLink();//非微博详情页，鼠标滑过微博，显示微博详情入口，同时隐藏添加时间
    getMetaInfo();//获取博客,微博元数据,包括点击量,评论数等
    resetTime();//重置时间，即显示为 刚刚、5分钟前、3小时前、昨天10:23、前天15：26等
    digg();//顶操作
    setTitle();//设置title属性

    if ($('#form-panel').length) {//评论留言

        seajs.use('comments', function() {
            showCommentsReply();//鼠标滑过留言评论，显示回复
            addComments();//添加留言或者评论
        });
    }

    if ($('#tag-cloud').length) {//标签云
        seajs.use('tagCloud', function() {
            $('#tag-cloud').tagCloud();
        });
    }

    $('#nav-' + NAV_ID).addClass('active');//高亮导航
}//end bootstrap

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
}//end date



/**
 * 顶操作
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-02 16:23:34
 *
 * @return {void} 无返回值
 */
function digg() {

    $('a[data-diggs]').one('click', function () {
        $.post(System.sys_base_site_url + 'ajax/digg.shtml', 'diggs=' + $(this).data('diggs'), function (data) {

            if (data && data.success) {
                var el = $(data.success), diggs = $(el[0]).text();
                el.text(intval(diggs) + 1);
            }
        });
    });
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
 * 转义html，类似php htmlspechalchars
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 14:35:20
 *
 * @param {string} str 待转义字符串
 *
 * @return {string} 转义后的字符串
 */
function htmlspecialchars(str) {
    return str.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&#39;');
}

/**
 * 反转义html
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 14:35:25
 *
 * @param {string} str 待转义字符串
 *
 * @return {string} 转义后的字符串
 */
function htmlspecialchars_decode(str) {
    return str.replace(/\&lt;/g, '<').replace(/\&gt;/g, '>').replace(/\&quot;/g, '"').replace(/\&#39;/g, "'");
}

/**
 * 转化为整数，类似php intval函数
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 14:39:02
 *
 * @param {mixed} str   需要转换的字符串
 * @param {int} [def=0] 转换失败默认值
 * @param {int} [radix=10] 进制
 *
 * @return {int} 转化后的整数
 */
function intval(str, def, radix) {
    radix = radix || 10;
    var str = parseInt(str, radix);

    return isNaN(str) ? parseInt(def == undefined ? 0 : def, radix) : str;
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
    var flag = 'setIntervalresetTime';

    $('.time-axis').each(function (index, item) {
        $(item).text(timeAxis($(item).data('time')));
    });

    if (!$body.data(flag)) {
        $body.data(flag, true);
        setInterval(resetTime, 60000);
    }
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
 * 设置title属性
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-08 22:07:37
 *
 * @return {void} 无返回值
 */
function setTitle() {
    var arr = [['span.add_time', 'CN_FABIAO,TIME'], ['a.hits', 'READS'], ['a[data-diggs]', 'DIGG'], ['a.comments', 'COMMENTS'], ['a.category', 'CATEGORY']];

    $.each(arr, function(index, item) {
        $(item[0]).attr('title', lang(item[1]));
    });
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
 * 去掉html标签
 *
 * @author              mashanling(msl-138@163.com)
 * @date                2013-05-08 14:36:46
 *
 * @param {string} str 字符串
 * @param {bool} [img=false] true保留img标签，false不保留
 *
 * @return {String} 去掉html标签后的字符串
 */
function strip_tags(str, img) {
    str = String(str);
    var pattern = img ? /<(?!img)[^>]*>/ig : /<[^>]*>/gi;

    return str.replace(pattern, '');
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

/**
 * 数字精确度
 *
 * @author              mashanling(msl-138@163.com)
 * @date                2013-05-08 14:37:40
 *
 * @param {float} value 数字
 * @param {int} [precision=2] 小数点位数
 *
 * @return {float} 精确小数点后的数值
 */
function toFixed(value, precision) {
    precision = precision === undefined ? 2 : precision;

    if ((0.9).toFixed() !== '1') {//IE下等于0
        var pow = Math.pow(10, precision);
        return (Math.round(value * pow) / pow).toFixed(precision);
    }

    return value.toFixed(precision);
}

/**
 * 转化为浮点数
 *
 * @author              mashanling(msl-138@163.com)
 * @date                2013-05-08 14:38:06
 *
 * @param {mixed} str 需要转换的字符串
 * @param {float} [def=0.00] 转换失败默认值
 *
 * @return {float} 转化后的浮点数
 */
function toFloat(str, def) {
    var str = parseFloat(str);

    return isNaN(str) ? parseFloat(def == undefined ? 0.00 : def) : str;
}