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
        'value' => ''
    ),
    'secret_key' => array(
        'xtype' => 'textfield',
        'value' => ''
    ),
    'login' => array(
        'xtype' => 'textfield',
        'value' => ''
    ),
    'password' => array(
        'xtype' => 'textfield',
        'value' => ''
    ),
    'checkout_url' => array(
        'xtype' => 'textfield',
        'value' => ''
    ),
    'gate_url' => array(
        'xtype' => 'textfield',
        'value' => ''
    ),
    'version' => array(
        'xtype' => 'numberfield',
        'value' => 2
    ),
    'developer_mode' => array(
        'xtype' => 'numberfield',
        'value' => 1
    ),
    'language' => array(
        'xtype' => 'textfield',
        'value' => 'russian'
    ),
    'currency' => array(
        'xtype' => 'textfield',
        'value' => 'BYR'
    ),
    'success_id' => array(
        'xtype' => 'numberfield',
        'value' => 0,
    ),
    'failure_id' => array(
        'xtype' => 'numberfield',
        'value' => 0,
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