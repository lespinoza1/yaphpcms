/**
 * 模块设置控制器
 *
 * @file            app/controller/Yap.controller.Module.js
 * @version         0.1
 * @author          mrmsl <msl-138@163.com>
 * @date            2012-09-22 15:20:06
 * @lastmodify      $Date$ $Author$
 */

Ext.define('Yap.controller.Module', {
    extend: 'Yap.controller.Base',

    /**
     * @inheritdoc Yap.controller.Log#__call
     */
    __call: function(data) {
        this.tabs(data.action);
    }
});

//放到最后，以符合生成jsduck类说明
Ext.data.JsonP.Yap_controller_Module(Yap.controller.Module);