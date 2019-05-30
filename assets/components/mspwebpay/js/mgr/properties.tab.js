/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

Ext.ComponentMgr.onAvailable('minishop2-window-payment-update', function () {
    this.on('beforerender', function (paymentWindow) {
        if (!/webpay/i.test(paymentWindow.record.class)) { return; }
        var tabs = this.findByType('modx-tabs').pop();
        tabs.add({
            title: _('properties'),
            items: [{
                xtype: 'webpay-grid-payment-properties',
                payment: paymentWindow.record.id
            }]
        });
    });
});
