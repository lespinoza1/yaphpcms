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
    $('.form-comment').submit(function() {
        $.post(System.sys_base_site_url + 'comments/add.shtml', $(this).serialize(), function (data) {
            seajs.log(data, 'log');
        });
        return false;
    });
}

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
    });
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