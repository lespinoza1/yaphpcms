/**
 * 全局js
 *
 * @file            global.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 * @lastmodify      $Date$ $Author$
 */

var DATA_FORM_PANEL = 'form-panel',
    DATA_FORM_COMMENT = 'form-comment',
    DATA_FORM_REPLY = 'form-reply',
    BLOG_FLAG = 'blog',
    BLOG_TYPE = 1,
    MINIBLOG_FLAG = 'miniblog',
    MINIBLOG_TYPE = 2,
    GUESTBOOK_FLAG = 'guestbook',
    GUESTBOOK_TYPE = 0,
    BTN_SUBMIT = 'btn-submit';

seajs.config({
    plugins: ['shim'],
    map: [
        ['.js', '.js?' + new Date().getTime()]
    ],
    alias: {
        lang: {//语言包
            src: System.sys_base_site_url + 'static/js/lang/zh_cn.js'
        },
        jquery: {//jquery
            src: System.sys_base_common_imgcache + 'js/jquery/jquery-1.9.1.min.js'
        },
        global: {//全局
            src: System.sys_base_js_url + 'global.js',
            deps: ['jquery']
        },
        common: {//通用函数库
            src: System.sys_base_common_imgcache + 'js/common.js'
        }
    }
});

seajs.use(['jquery', 'global'], bootstrap);

/**
 * 添加留言或者评论
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-05 22:13:52
 *
 * @return void 无返回值
 */
function addComments() {
    var formPanel = $('#' + DATA_FORM_PANEL);

    if (!formPanel.length) {
        return;
    }

    formPanel.html(getFormHtml());

    var formComment = $('#' + DATA_FORM_COMMENT).on('submit', function() {

        if (!$.trim(formComment.find('input[name=username]').val())) {
            alert('a');return false;
         }

        if (!$body.data(BTN_SUBMIT)) {
            $body.data(BTN_SUBMIT, formComment.find('#' + BTN_SUBMIT));
        }

        $body.data(BTN_SUBMIT).attr('disabled', true);

        $.post(System.sys_base_site_url + 'comments/add.shtml', $(this).serialize(), function (data) {
            if (data) {
                if (data.success) {
                }
                else {
                    alert(data.msg || '系统繁忙，请稍后再试');
                }
            }
            else {
                alert('系统繁忙，请稍后再试');
            }

            $body.data(BTN_SUBMIT).attr('disabled', false);
        });

        return false;
    });

    $body.data(DATA_FORM_PANEL, formPanel);
    $body.data(DATA_FORM_COMMENT, formComment);
}//end addComments

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
    showCommentsReply();//鼠标滑过留言评论，显示回复
    getMetaInfo();//获取博客,微博元数据,包括点击量,评论数等
    addComments();//添加留言或者评论

    $('#nav-' + NAV_ID).addClass('active');//高亮导航
}

/**
 * 获取留言或者评论 表单 html
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-06 17:05:32
 *
 * @return string 表单html
 */
function getFormHtml() {

    if (BLOG_FLAG == NAV_ID) {
        var type = BLOG_TYPE, blogId = META_INFO.hits.split(',')[1];
    }
    else if (MINIBLOG_FLAG == NAV_ID) {
        var type = MINIBLOG_TYPE, blogId = META_INFO.hits.split(',')[1];
    }
    else {
        var type = GUESTBOOK_TYPE, blogId = 0;
    }

    if (window['_getFormHtml']) {
        return window['_getFormHtml'];
    }

    var html = [];
    html.push('<form class="form-horizontal" id="' + DATA_FORM_COMMENT + '" method="post" action="' + System.sys_base_site_url + 'comments/add.shtml">');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label"><span class="text-error">*</span>用户名</label>');
    html.push('        <div class="controls">');
    html.push('            <input type="text" name="username" required maxlength="20" />');
    html.push('            <span class="muted">(20个字符以内，一个汉字三个字节)</span>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label">主页</label>');
    html.push('        <div class="controls">');
    html.push('            <input type="url" name="user_homepage" />');
    html.push('            <span class="muted">(选填)</span>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label"><span class="text-error">*</span>内容</label>');
    html.push('        <div class="controls">');
    html.push('            <textarea name="content" rows="3" cols="50" class="input-block-level" required></textarea>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="controls text-right">');
    html.push('        <button id="btn-submit" class="btn btn-primary">提 交</button>');
    html.push('        <button id="btn-reset-cancel" type="reset" class="btn">取 消</button>');
    html.push('    </div>');
    html.push('    <input type="hidden" name="type" value="' + type + '" />');
    html.push('    <input type="hidden" name="parent_id" value="0" />');
    html.push('    <input type="hidden" name="blog_id" value="' + blogId + '" />');
    html.push('</form>');

    html = html.join('');

    return html;
}//end getFormHtml

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
function getMetaInfo() {
    'undefined' != typeof(META_INFO) && $.post(System.sys_base_site_url + 'ajax/metainfo.shtml', $.param(META_INFO), setMetaInfo);
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

        $.each(data.blog, function(index, item) {
            $('.blog-diggs-' + index).text(item.diggs);
            $('.blog-hits-' + index).text(item.hits);
            $('.blog-comments-' + index).text(item.comments);
        });

        $.each(data.miniblog, function(index, item) {
            $('.miniblog-diggs-' + index).text(item.diggs);
            $('.miniblog-hits-' + index).text(item.hits);
            $('.miniblog-comments-' + index).text(item.comments);
        });
    }
}

/**
 * 鼠标滑过留言评论，显示回复
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 22:20:05
 *
 * @return void 无返回值
 */
function showCommentsReply() {
    $('.comments-detail .popover-content').hover(function(e) {

        $.each($(this).parents('.popover-content'), function(index, item) {
            $(item).find('.reply:first').hide();
        });

        $(this).find('.reply:first').show();

        return false;
    }, function(e) {
        $(this).find('.reply:first').hide();
    }).find('.reply').click(function () {
        var href = $(this).attr('href'),
        el = $(href),
        id = href.split('-')[1];

        if (!$body.data(DATA_FORM_REPLY)) {
            var html = [];
            html.push('<div class="popover hide bottom" id="' + DATA_FORM_REPLY +'">');
            html.push('    <div class="arrow"></div>');
            html.push('    <div class="popover-title">回复 <b class="name"></b></div>');
            html.push('    <div class="popover-content">');
            //html.push('        ' + getFormHtml());
            html.push('    </div>');
            html.push('</div>');
            el.after(html.join(''));
            var form = $('#' + DATA_FORM_REPLY);
            $body.data(DATA_FORM_COMMENT).appendTo(form.find('.popover-content'));
            $body.data(DATA_FORM_REPLY, form);

            $('#btn-reset-cancel').on('click', function() {
                $body.data(DATA_FORM_REPLY).hide();
                $body.data(DATA_FORM_COMMENT).appendTo($body.data(DATA_FORM_PANEL)).find('input[name=parent_id]').val(0);
            });
        }
        else {
            $body.data(DATA_FORM_COMMENT).appendTo($body.data(DATA_FORM_REPLY).find('.popover-content'));
            el.after($body.data(DATA_FORM_REPLY));
        }

        $body.data(DATA_FORM_REPLY).show()
        .find('b.name').text($(this).next().text())
        .end()
        .find('input[name=parent_id]').val(id);

        $html.animate({
            scrollTop: el.offset().top - 100
        }, 500);
        return false;
    });
}//end showCommentsReply

/**
 * 非微博详情页，鼠标滑过微博，显示微博详情入口，同时隐藏添加时间
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 22:00:47
 *
 * @return void 无返回值
 */
function showMiniblogDetailLink() {

    if ('undefined' == typeof(IS_MINIBLOG_DETAIL)) {
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