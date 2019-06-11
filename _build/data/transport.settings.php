<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

class msPaymentHandler {} // mocking for get access to WebPay class constants during the building the package
require_once dirname(dirname(__DIR__)) . '/core/components/mspwebpay/WebPay.class.php';

$list = [
    WebPay::OPTION_STORE_ID     => ['xtype' => 'textfield', 'value' => ''],
    WebPay::OPTION_SECRET_KEY   => ['xtype' => 'textfield', 'value' => ''],
    WebPay::OPTION_LOGIN        => ['xtype' => 'textfield', 'value' => ''],
    WebPay::OPTION_PASSWORD     => ['xtype' => 'textfield', 'value' => ''],
    WebPay::OPTION_CHECKOUT_URL => ['xtype' => 'textfield', 'value' => 'https://payment.webpay.by'],
    WebPay::OPTION_GATE_URL     => ['xtype' => 'textfield', 'value' => 'https://billing.webpay.by'],
    WebPay::OPTION_VERSION      => ['xtype' => 'numberfield', 'value' => 2],
    WebPay::OPTION_LANGUAGE     => ['xtype' => 'textfield', 'value' => 'russian'], // todo: replace by onw combo via plugin
    WebPay::OPTION_CURRENCY     => ['xtype' => 'textfield', 'value' => 'BYR'], // todo: replace by onw combo via plugin
    WebPay::OPTION_SUCCESS_STATUS   => ['xtype' => 'mspp-combo-status', 'value' => 2],
    WebPay::OPTION_FAILURE_STATUS   => ['xtype' => 'mspp-combo-status', 'value' => 4],
    WebPay::OPTION_SUCCESS_PAGE     => ['xtype' => 'mspp-combo-resource', 'value' => 0],
    WebPay::OPTION_FAILURE_PAGE     => ['xtype' => 'mspp-combo-resource', 'value' => 0],
    WebPay::OPTION_UNPAID_PAGE      => ['xtype' => 'mspp-combo-resource', 'value' => 0],

    WebPay::OPTION_DEVELOPER_MODE   => ['xtype' => 'combo-boolean', 'value' => true],
    WebPay::OPTION_CHECKOUT_URL_TEST => ['xtype' => 'textfield', 'value' => 'https://securesandbox.webpay.by'],
    WebPay::OPTION_GATE_URL_TEST     => ['xtype' => 'textfield', 'value' => 'https://sandbox.webpay.by'],
];

$settings = [];
foreach ($list as $k => $v) {
    $setting = $xpdo->newObject(modSystemSetting::class);
    $setting->fromArray(array_merge([
        'key' => WebPay::PREFIX . '_' . $k,
        'namespace' => 'minishop2',
        'area' => WebPay::PREFIX,
        'editedon' => null,
    ], $v), '', true, true);

    $settings[] = $setting;
}

return $settings;
