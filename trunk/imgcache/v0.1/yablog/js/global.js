/**
 * 全局js
 *
 * @file            global.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-30 21:34:20
 * @lastmodify      $Date$ $Author$
 */

//下拉菜单插件
(function($) {

    $.fn.dropdown = function(options) {

        var defaults = {
            dropdownClass: ' > ul.dropdown-menu',
            hoverClass: 'open'
        },
        opts = $.extend({}, defaults, options);

        return this.each(function() {
            var me = $(this),
            dropdowns = me.find(opts.dropdownClass);

            me.hover(function() {
                dropdowns.fadeIn();
            }, function() {
                dropdowns.fadeOut();
            });
        });
    }
})(jQuery);