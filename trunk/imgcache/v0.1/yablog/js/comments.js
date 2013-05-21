/**
 * 留言评论js
 *
 * @file            comments.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-07 13:48:02
 * @lastmodify      $Date$ $Author$
 */

var DATA_FORM_PANEL = 'form-panel',//表单父元素
    DATA_FORM_COMMENT = 'form-comment',//评论表单
    DATA_FORM_REPLY = 'form-reply',//回复表单
    GUESTBOOK_TYPE = 0,//留言类型
    BLOG_TYPE = 1,//博客评论类型
    MINIBLOG_TYPE = 2,//微博评论类型
    BTN_SUBMIT = 'btn-submit';//提交按钮

/**
 * 添加留言或者评论
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-05 22:13:52
 *
 * @return {void} 无返回值
 */
function addComments() {
    var formPanel = $('#' + DATA_FORM_PANEL).html(getFormHtml());

    var formComment = $('#' + DATA_FORM_COMMENT).on('submit', function() {

        if (IS_OLD_IE) {//
            var checked = true, el = [[formComment.find('input[name=username]'), 'USERNAME'], [formComment.find('textarea'), 'CONTENT']];

            $.each(el, function(index, item) {

                if (!$.trim(item[0].val())) {
                    alert(lang('PLEASE_ENTER,' + item[1]));
                    item[0].focus();
                    checked = false;
                    return false;
                }
            });

            if (!checked) {
                return false;
            }

            var el = formComment.find('input[name=email]'), emial = el.val().trim();

            if (email && /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)) {
                alert(lang('PLEASE_ENTER,CORRECT,CN_DE,EMAIL'));
                return false;
            }

        }//end if IS_OLD_IE

        var el = formComment.find('input[name=user_homepage]'), url = el.val().trim();

        if (url.length && 'http://' != url && !/http:\/\/[a-z0-9]+\.[a-z0-9]+/i.test(url)) {//主页链接
            alert(lang('PLEASE_ENTER,CORRECT,CN_DE,HOMEPAGE,LINK'));
            el.focus();
            return false;
        }

        if (!$body.data(BTN_SUBMIT)) {
            $body.data(BTN_SUBMIT, formComment.find('#' + BTN_SUBMIT));
        }

        $body.data(BTN_SUBMIT).attr('disabled', true);

        $.ajax({
            method: 'post',
            url: System.sys_base_site_url + 'comments/add.shtml',
            dataType: 'json',
            data: $(this).serialize(),
            ok: function (flag) {
                location.href = System.sys_base_site_url + 'msg.shtml?flag=' + flag;
            },
            success: function(data) {
                if (data) {

                    if (data.success) {
                        this.ok(data.success);
                    }
                    else {
                        alert(data.msg || lang('SYSTEM_ERROR'));
                    }
                }
                else {
                    alert(lang('SYSTEM_ERROR'));
                }
            },
            complete: function () {
                $body.data(BTN_SUBMIT).attr('disabled', false);
            },
            error: function (response) {

                if (response.responseText.indexOf('"success":true') > -1) {
                    location.reload();
                }
                else {
                    alert(lang('SYSTEM_ERROR'));
                }
            }
        });

        return false;
    });

    $body.data(DATA_FORM_PANEL, formPanel);
    $body.data(DATA_FORM_COMMENT, formComment);
}//end addComments

/**
 * 绑定验证码事件
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-21 08:43:04
 *
 * @return {void} 无返回值
 */
function bindVerifycode() {
    var el = $('#txt-verifycode');

    if (el.length) {
        el.focus(function () {
            var next = el.next('img');

            if (!next.length) {
                $('<img />').bind({
                    error: function() {

                        if (!el.data('error')) {log('error');
                            this.src = System.sys_base_common_imgcache + 'images/verifycode_error.png';
                            el.data('error', true);
                        }
                    },
                    click: function() {
                        this.src = System.sys_base_site_url + 'verifycode/' + window._VERIFYCODE_MODULE + '.shtml?' + Math.random();
                    }
                }).attr({
                    title: lang('REFRESH_CODE_TIP'),
                    id: 'img-verifycode',
                    src: System.sys_base_site_url + 'verifycode/' + window._VERIFYCODE_MODULE + '.shtml'
                }).css({
                    valign: 'absmiddle',
                    margin: '0 5px',
                    cursor: 'pointer'
                }).insertAfter(el)
                .after(
                    $('<span class="muted">' +
                    (1 == System[window._VERIFYCODE_MODULE + '_verifycode_case'] ? lang('CASE_SENSITIVE') : lang('NO,CASE_SENSITIVE')) +
                     '，' + lang('VERIFY_CODE_ORDER') + '：' + '<span class="text-error">' +
                    (System[window._VERIFYCODE_MODULE + '_verifycode_order']) +
                    '</span></span>')
                );
            }
        });
    }
    /*else if (!el.next('img').is(':visible')) {
        el.next('img').click();
    }*/
}//end bindVerifycode

/**
 * 获取留言或者评论 表单 html
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-06 17:05:32
 *
 * @return {string} 表单html
 */
function getFormHtml() {

    switch (NAV_ID) {
        case GUESTBOOK_FLAG://留言
            var type = GUESTBOOK_TYPE, blogId = 0, verifycodeModule = 'module_guestbook';
            break;

        default://评论
            var blogId = META_INFO.hits.split(',')[1], verifycodeModule = 'module_comments';
            var type = BLOG_FLAG == NAV_ID ? BLOG_TYPE : MINIBLOG_TYPE;
            break;
    }

    window._VERIFYCODE_MODULE = verifycodeModule;

    var html = [];
    html.push('<form class="form-horizontal" id="' + DATA_FORM_COMMENT + '" method="post" action="' + System.sys_base_site_url + 'comments/add.shtml">');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label"><span class="text-error">*</span>' + lang('USERNAME') + '</label>');
    html.push('        <div class="controls">');
    html.push('            <input type="text" name="username" required maxlength="20" />');
    html.push('            <span class="muted">(' + lang('LT_BYTE').replace('{0}', 20) + '。' + lang('CN_TO_BYTE') + ')</span>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label">' + lang('EMAIL') + '</label>');
    html.push('        <div class="controls">');
    html.push('            <input type="email" value="" name="email" maxlength="50" />');
    html.push('            <span class="muted">(' + lang('CN_XUANTIAN,%，,SECRET,%，,NO,SHOW') + '。' + lang('LT_BYTE').replace('{0}', 50) + ')</span>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label">' + lang('HOMEPAGE') + '</label>');
    html.push('        <div class="controls">');
    html.push('            <input type="text" value="http://" name="user_homepage" />');
    html.push('            <span class="muted">(' + lang('CN_XUANTIAN') + '。' + lang('LT_BYTE').replace('{0}', 50) + ')</span>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="control-group">');
    html.push('        <label class="control-label"><span class="text-error">*</span>' + lang('CONTENT') + '</label>');
    html.push('        <div class="controls">');
    html.push('            <textarea name="content" rows="3" cols="50" class="input-block-level" required></textarea>');
    html.push('            <span class="muted">http(s)://www.yablog.cn/path/?querystring ' + lang('SPACE') + '... =&gt; <a href="http://www.yablog.cn.com/path/?querystring" rel="nofollow">http(s)://www.abc.com/path/?querystring</a></span>');
    html.push('        </div>');
    html.push('    </div>');

    if (1 == System[verifycodeModule + '_verifycode_enable']) {//开启验证码
        html.push('    <div class="control-group">');
        html.push('        <label class="control-label"><span class="text-error">*</span>' + lang('VERIFY_CODE') + '</label>');
        html.push('        <div class="controls">');
        html.push('            <input type="text" id="txt-verifycode" name="_verify_code" required maxlength="6" />');
        html.push('        </div>');
        html.push('    </div>');
    }
    else {
        html.push('<input type="hidden" name="_verify_code" value="ok" />');
    }

    html.push('    <div class="control-group">');
    html.push('        <div class="controls">');
    html.push('            <label class="muted"><input type="checkbox" value="1" name="at_email" /> ' + lang('AT_ME_NOTICE_ME') + '</label>');
    html.push('        </div>');
    html.push('    </div>');
    html.push('    <div class="controls text-right">');
    html.push('        <button id="btn-submit" class="btn btn-primary">' + lang('SUBMIT') + '</button>');
    html.push('        <button id="btn-reset-cancel" type="reset" class="btn">' + lang('CANCEL') + '</button>');
    html.push('    </div>');
    html.push('    <input type="hidden" name="type" value="' + type + '" />');
    html.push('    <input type="hidden" name="parent_id" value="0" />');
    html.push('    <input type="hidden" name="blog_id" value="' + blogId + '" />');
    html.push('</form>');

    html = html.join('');

    return html;
}//end getFormHtml

/**
 * 鼠标滑过留言评论，显示回复
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 22:20:05
 *
 * @return {void} 无返回值
 */
function showCommentsReply() {
    $('.comments-detail .popover-content').hover(function(e) {

        $.each($(this).parents('.popover-content'), function(index, item) {
            $(item).find('.reply:first').hide();
        });

        !$(this).find('#' + DATA_FORM_COMMENT).length && $(this).find('.reply:first').show();

        return false;
    }, function(e) {
        $(this).find('.reply:first').hide();
    })
    .find('.reply').click(function () {//回复
        var href = $(this).attr('href'),
        el = $(href),
        id = href.split('-')[1];

        if (!$body.data(DATA_FORM_REPLY)) {
            var html = [];
            html.push('<div class="popover hide bottom" id="' + DATA_FORM_REPLY +'">');
            html.push('    <div class="arrow"></div>');
            html.push('    <div class="popover-title">' + lang('REPLY') + ' <b class="name"></b></div>');
            html.push('    <div class="popover-content">');
            //html.push('        ' + getFormHtml());
            html.push('    </div>');
            html.push('</div>');
            el.after(html.join(''));
            var form = $('#' + DATA_FORM_REPLY);
            $body.data(DATA_FORM_COMMENT).appendTo(form.find('.popover-content'));
            $body.data(DATA_FORM_REPLY, form);

            $('#btn-reset-cancel').on('click', function() {//取消
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
        }, 300);
        return false;
    })
    .end().find('a[href^=#comment-]').click(function() {
        $html.animate({
            scrollTop: $($(this).attr('href')).offset().top - 50
        }, 300);
        return false;
    });
}//end showCommentsReply