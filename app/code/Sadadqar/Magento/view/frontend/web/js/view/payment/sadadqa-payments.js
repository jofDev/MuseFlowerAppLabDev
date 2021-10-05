define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push({
            type: 'sadadqa',
            component: 'Sadadqar_Magento/js/view/payment/method-renderer/sadadqa-method'
        });
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
