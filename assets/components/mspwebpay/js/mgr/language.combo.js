/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

WebPayPayment.combo.Language = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['caption','key'],
            data: [
                [_('ms2_payment_webpay_lang_english'), 'english'],
                [_('ms2_payment_webpay_lang_russian'), 'russian']
            ]
        }),
        name: 'language',
        hiddenName: 'language',
        displayField: 'caption',
        valueField: 'key',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        selectOnFocus: false,
        preventRender: true
    });

    WebPayPayment.combo.Language.superclass.constructor.call(this, config);
};

Ext.extend(WebPayPayment.combo.Language, MODx.combo.ComboBox);
Ext.reg('webpay-combo-language', WebPayPayment.combo.Language);
