/**
 * 启动js
 *
 * @file            init.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 10:33:49
 * @lastmodify      $Date$ $Author$
 */

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
 * 启动函数
 *
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-05-01 17:22:16
 *
 * @return void 无返回值
 */
function bootstrap() {
    window.$html = $('html');
    navDropdown();//下拉菜单
    showMiniblogDetailLink();//非微博详情页，鼠标滑过微博，显示微博详情入口，同时隐藏添加时间
    showCommentsReply();//鼠标滑过留言评论，显示回复
    getMetaInfo();//获取博客,微博元数据,包括点击量,评论数等
    addComments();//添加留言或者评论

    $('#nav-' + NAV_ID).addClass('active');//高亮导航


}