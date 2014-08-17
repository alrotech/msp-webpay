<?php
/**
 * Loads system settings into build
 *
 * @package mspwebpay
 * @subpackage build
 */
$settings = array();

$tmp = array(
    'store_id' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'ms2_payment_webpay'
    ),
    'secret_key' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'ms2_payment_webpay'
    ),
    'login' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'ms2_payment_webpay'
    ),
    'password' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'ms2_payment_webpay'
    ),
    'checkout_url' => array(
        'xtype' => 'textfield',
        'value' => 'https://secure.webpay.by',
        'area' => 'ms2_payment_webpay'
    ),
    'gate_url' => array(
        'xtype' => 'textfield',
        'value' => 'https://billing.webpay.by',
        'area' => 'ms2_payment_webpay'
    ),
    'version' => array(
        'xtype' => 'numberfield',
        'value' => 2,
        'area' => 'ms2_payment_webpay'
    ),
    'developer_mode' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'ms2_payment_webpay'
    ),
    'language' => array(
        'xtype' => 'textfield',
        'value' => 'russian',
        'area' => 'ms2_payment_webpay'
    ),
    'currency' => array(
        'xtype' => 'textfield',
        'value' => 'BYR',
        'area' => 'ms2_payment_webpay'
    ),
    'success_id' => array(
        'xtype' => 'numberfield',
        'value' => 0,
        'area' => 'ms2_payment_webpay'
    ),
    'failure_id' => array(
        'xtype' => 'numberfield',
        'value' => 0,
        'area' => 'ms2_payment_webpay'
    ),
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'ms2_payment_webpay_' . $k,
            'namespace' => 'minishop2',
            'area' => 'ms2_payment',
        ), $v
    ),'',true,true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;