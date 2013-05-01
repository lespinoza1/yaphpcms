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
    $('li.dropdown').dropdown();//下拉菜单

}