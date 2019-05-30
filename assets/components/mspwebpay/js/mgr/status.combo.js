/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

WebPayPayment.combo.Status = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'status',
        hiddenName: 'status',
        displayField: 'name',
        valueField: 'id',
        fields: ['id', 'name'],
        pageSize: 10,
        typeAhead: true,
        preselectValue: false,
        value: 0,
        editable: true,
        hideMode: 'offsets',
        url: WebPayPayment.ms2Connector,
        baseParams: {
            action: 'mgr/settings/status/getlist',
            combo: true
        }
    });

    WebPayPayment.combo.Status.superclass.constructor.call(this, config);
};

Ext.extend(WebPayPayment.combo.Status, MODx.combo.ComboBox);
Ext.reg('webpay-combo-status', WebPayPayment.combo.Status);
