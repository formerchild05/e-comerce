define([
    'require',
    'mage/url',
    'Magento_Checkout/js/view/payment/default'
], function (require, urlBuilder, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Minh_ZaloPay/payment/zalopay'
        },

        redirectAfterPlaceOrder: false,

        getDescription: function () {
            return window.checkoutConfig.payment.minh_zalopay.description;
        },

        getLogoUrl: function () {
            return require.toUrl('Minh_ZaloPay/images/zalopay.png');
        },

        afterPlaceOrder: function () {
            window.location.href = window.checkoutConfig.payment.minh_zalopay.startUrl ||
                urlBuilder.build('zalopay/payment/start');
        }
    });
});
