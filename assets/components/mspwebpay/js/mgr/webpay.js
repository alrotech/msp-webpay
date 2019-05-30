/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

var WebPayPayment = function(config) {
    config = config || {};
    WebPayPayment.superclass.constructor.call(this, config);
};
Ext.extend(WebPayPayment, Ext.Component, { combo: {}, grid: {}, window: {} });
Ext.reg('webpaypayment', WebPayPayment);

WebPayPayment = new WebPayPayment();
