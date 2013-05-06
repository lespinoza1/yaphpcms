/**
 * 全局js
 *
 * @file            global.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 * @lastmodify      $Date$ $Author$
 */

/**
 * 添加留言或者评论
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-05 22:13:52
 *
 * @return void 无返回值
 */
function addComments() {

    if (!$('#form-panel').length) {
        return;
    }

    $('#form-panel').html(getFormHtml());
    $('.form-comment').on('submit', function() {
        $.post(System.sys_base_site_url + 'comments/add.shtml', $(this).serialize(), function (data) {
            seajs.log(data, 'log');
        });
        return false;
    });
}

/**
 * 获取留言或者评论 表单 html
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-06 17:05:32
 *
 * @return string 表单html
 */
function getFormHtml(type) {

    if (undefined === type) {

        if (undefined === window['_formType']) {

            if ('blog' == NAV_ID) {
                var type = 1, blogId = META_INFO.hits.split(',')[1];
            }
            else if ('miniblog' == NAV_ID) {
                var type = 2, blogId = META_INFO.hits.split(',')[1];
            }
            else {
                var type = 0, blogId = 0;
            }
        }
        else {
            var type = window['_formType'];
        }
    }

    if (window['_getFormHtml']) {
        return window['_getFormHtml'];
    }

    var html = [];
    html.push('<form class="form-horizontal form-comment" method="post" action="' + System.sys_base_site_url + 'comments/add.shtml">');
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
    html.push('        <button class="btn btn-primary">提交</button>');
    html.push('        <button class="btn">取消</button>');
    html.push('    </div>');
    html.push('    <input type="hidden" name="type" value="' + type + '" />');
    html.push('    <input type="hidden" name="parent_id" value="0" />');
    html.push('    <input type="hidden" name="blog_id" value="' + blogId + '" />');
    html.push('</form>');

    html = html.join('');
    window['_getFormHtml'] = html;

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

    if ('undefined' == typeof(META_INFO)) {
        return;
    }

    $.ajax({
        dataType: 'json',
        url: System.sys_base_site_url + 'ajax/metainfo.shtml',
        data: $.param(META_INFO),
        method: 'post',
        success: setMetaInfo
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
        var form = $('#form-reply'), href = $(this).attr('href'), el = $(href), id = href.split('-')[1];

        if (!form.length) {
            var html = [];
            html.push('<div class="popover hide bottom" id="form-reply">');
            html.push('    <div class="arrow"></div>');
            html.push('    <div class="popover-title">回复 <b class="name"></b></div>');
            html.push('    <div class="popover-content">');
            html.push('        ' + getFormHtml());
            html.push('    </div>');
            html.push('</div>');
            el.after(html.join(''));
        }
        else {
            el.after(form.hide());
        }

        form =$('#form-reply').fadeIn(500)
        .find('b.name:first').text($(this).next().text())
        .end().find('input[name=parent_id]:first').val(id);

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