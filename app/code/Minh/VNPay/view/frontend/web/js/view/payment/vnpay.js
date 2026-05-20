define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'minh_vnpay',
        component: 'Minh_VNPay/js/view/payment/method-renderer/vnpay-method'
    });

    return Component.extend({});
});
