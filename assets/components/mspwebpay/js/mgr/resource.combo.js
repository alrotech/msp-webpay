/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

WebPayPayment.combo.Resource = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'resource',
        hiddenName: 'resource',
        displayField: 'pagetitle',
        valueField: 'id',
        fields: ['id', 'pagetitle'],
        pageSize: 20,
        typeAhead: true,
        preselectValue: false,
        value: 0,
        editable: true,
        hideMode: 'offsets',
        url: WebPayPayment.ms2Connector,
        baseParams: {
            action: 'mgr/system/element/resource/getlist',
            combo: true
        }
    });

    WebPayPayment.combo.Resource.superclass.constructor.call(this, config);
};

Ext.extend(WebPayPayment.combo.Resource, MODx.combo.ComboBox);
Ext.reg('webpay-combo-resource', WebPayPayment.combo.Resource);
