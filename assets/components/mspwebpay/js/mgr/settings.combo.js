/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

WebPayPayment.combo.Settings = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'key',
        hiddenName: 'key',
        displayField: 'name_trans',
        valueField: 'key',
        fields: ['key', 'value', 'name_trans', 'xtype'],
        pageSize: 20,
        typeAhead: false,
        preselectValue: false,
        allowBlank: true,
        emptyText: _('ms2_payment_webpay_select_setting'),
        editable: false,
        hideMode: 'offsets',
        url: MODx.config.connector_url,
        baseParams: {
            action: 'system/settings/getList',
            namespace: 'minishop2',
            area: 'ms2_payment_webpay'
        }
    });

    WebPayPayment.combo.Settings.superclass.constructor.call(this, config);
};

Ext.extend(WebPayPayment.combo.Settings, MODx.combo.ComboBox);
Ext.reg('webpay-combo-settings', WebPayPayment.combo.Settings);
