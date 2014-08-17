<?php

if ($object->xpdo) {
    /* @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            if (!empty($options['webpay-store-id'])) {
                if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'ms2_payment_webpay_store_id'))) {
                    $tmp = $modx->newObject('modSystemSetting');
                }
                $tmp->fromArray(array(
                    'namespace' => 'minishop2',
                    'area' => 'ms2_payment_webpay',
                    'xtype' => 'textfield',
                    'value' => $options['webpay-store-id'],
                    'key' => 'ms2_payment_webpay_store_id',
                ), '', true, true);
                $tmp->save();
            }
            if (!empty($options['webpay-login'])) {
                if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'ms2_payment_webpay_login'))) {
                    $tmp = $modx->newObject('modSystemSetting');
                }
                $tmp->fromArray(array(
                        'namespace' => 'minishop2',
                        'area' => 'ms2_payment_webpay',
                        'xtype' => 'textfield',
                        'value' => $options['webpay-login'],
                        'key' => 'ms2_payment_webpay_login',
                    ), '', true, true);
                $tmp->save();
            }
            if (!empty($options['webpay-password'])) {
                if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'ms2_payment_webpay_password'))) {
                    $tmp = $modx->newObject('modSystemSetting');
                }
                $tmp->fromArray(array(
                        'namespace' => 'minishop2',
                        'area' => 'ms2_payment_webpay',
                        'xtype' => 'textfield',
                        'value' => $options['webpay-password'],
                        'key' => 'ms2_payment_webpay_password',
                    ), '', true, true);
                $tmp->save();
            }
            if (!empty($options['webpay-secret-key'])) {
                if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'ms2_payment_webpay_secret_key'))) {
                    $tmp = $modx->newObject('modSystemSetting');
                }
                $tmp->fromArray(array(
                        'namespace' => 'minishop2',
                        'area' => 'ms2_payment_webpay',
                        'xtype' => 'textfield',
                        'value' => $options['webpay-secret-key'],
                        'key' => 'ms2_payment_webpay_secret_key',
                    ), '', true, true);
                $tmp->save();
            }
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modelPath = $modx->getOption('minishop2.core_path', null, $modx->getOption('core_path') . 'components/minishop2/') . 'model/';
            $modx->addPackage('minishop2', $modelPath);
            /* @var msPayment $payment */
            $modx->removeCollection('msPayment', array('class' => 'WebPay'));
            $modx->removeCollection('modSystemSetting', array('key:LIKE' => 'ms2\_payment\_webpay\_%'));
            break;
    }
}

return true;