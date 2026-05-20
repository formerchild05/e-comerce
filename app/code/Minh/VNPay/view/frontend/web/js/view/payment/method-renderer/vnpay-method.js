define([
    'require',
    'mage/url',
    'Magento_Checkout/js/view/payment/default'
], function (require, urlBuilder, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Minh_VNPay/payment/vnpay'
        },

        redirectAfterPlaceOrder: false,

        getDescription: function () {
            return window.checkoutConfig.payment.minh_vnpay.description;
        },

        getLogoUrl: function () {
            return require.toUrl('Minh_VNPay/images/vnpay.jpg');
        },

        afterPlaceOrder: function () {
            window.location.href = window.checkoutConfig.payment.minh_vnpay.startUrl ||
                urlBuilder.build('vnpay/payment/start');
        }
    });
});
