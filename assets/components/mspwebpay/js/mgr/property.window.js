/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

WebPayPayment.window.PaymentProperty = function (config) {
    config = config || { new: false };

    if (!config.id) {
        config.id = 'webpay-window-payment-property'
    }

    Ext.applyIf(config, {
        url: WebPayPayment.ownConnector,
        layout: 'anchor',
        cls: 'modx-window',
        modal: true,
        width: 400,
        autoHeight: true,
        allowDrop: false,
        baseParams: {
            action: 'mgr/properties/' + (config.new ? 'create' : 'update'),
            payment: config.payment
        },
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config),
        closeAction: 'close'
    });

    WebPayPayment.window.PaymentProperty.superclass.constructor.call(this, config);

    this.on('hide', function () {
        var self = this;
        window.setTimeout(function () {
            self.close();
        }, 200);
    });
};

Ext.extend(WebPayPayment.window.PaymentProperty, MODx.Window, {

    dynamicValueField: function dynamicValueField(xtype, value) {
        var form = Ext.getCmp('webpay-window-payment-property-form');
        var field = Ext.getCmp('webpay-property-value');

        form.remove(field);
        form.add({
            name: 'value',
            fieldLabel: _('value'),
            xtype: xtype,
            value: value,
            anchor: '100%',
            id: 'webpay-property-value'
        });
        form.doLayout();
    },

    getFields: function getFields(config) {
        return [{
            layout: 'form',
            defaults: { msgTarget: 'under', autoHeight: true },
            id: 'webpay-window-payment-property-form',
            items: [{
                fieldLabel: _('parameter'),
                xtype: 'webpay-combo-settings',
                name: 'key',
                anchor: '100%',
                readOnly: !config.new,
                listeners: {
                    loaded: {
                        fn: function (combo) {
                            var record = combo.getStore().getAt(0);
                            console.log(combo);
                            this.dynamicValueField(record.get('xtype'), record.get('value'));
                        }, scope: this
                    },
                    select: {
                        fn: function (combo, record) {
                            
                            this.dynamicValueField(record.data.xtype, record.data.value);
                        }, scope: this
                    }
                }
            }, {
                fieldLabel: _('value'),
                xtype: 'textarea',
                name: 'value',
                anchor: '100%',
                id: 'webpay-property-value'
            }]
        }];
    },

    getKeys: function getKeys() {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit();
            }, scope: this
        }];
    },

    getButtons: function getButtons(config) {
        return [{
            scope: this,
            text: _('cancel'),
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }, {
            scope: this,
            text: _('save'),
            handler: this.submit,
            cls: 'primary-button'
        }]
    }

});
Ext.reg('webpay-window-payment-property', WebPayPayment.window.PaymentProperty);
