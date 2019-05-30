<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(__DIR__)))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->loadCustomClasses('payment');

if (!class_exists('WebPay')) {
    exit('Error: could not load payment class "WebPay".');
}

$context = '';
$params = array();

/* @var msPaymentInterface|WebPay $handler */
$handler = new WebPay($modx->newObject('msOrder'));

switch ($_GET['action']) {
    case 'payment':
        $request = json_decode($_REQUEST['request']);
        $url = $handler->config['checkout_url'];

        $fields = '';
        foreach ($request as $k => $v) {
            $fields .= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
        }

        $form = "<form action=\"$url\" method=\"post\">$fields</form>";
        $script = "
            <script>
                window.onload=function(){
                    document.forms[0].submit();
                }
            </script>
        ";

        echo $form, $script;
        exit;
        break;
    case 'notify':
        if (empty($_POST['site_order_id'])) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2:WebPay] Returned empty order id.');
        }
        if ($order = $modx->getObject('msOrder', $_POST['site_order_id'])) {
            $_POST['action'] = $_GET['action'];
            $handler->receive($order, $_POST);
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2:WebPay] Could not retrieve order with id ' . $_POST['site_order_id']);
        }
        break;
    case 'success':
    case 'cancel':
        if (empty($_REQUEST['wsb_order_num'])) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2:WebPay] Returned empty order id.');
        }
        if ($order = $modx->getObject('msOrder', $_REQUEST['wsb_order_num'])) {
            $handler->receive($order, $_REQUEST);
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2:WebPay] Could not retrieve order with id ' . $_REQUEST['wsb_order_num']);
        }
        break;
}

$success = $cancel = $modx->getOption('site_url');

if ($id = $modx->getOption('ms2_payment_webpay_success_id', null, 0)) {
    $success = $modx->makeUrl($id, $context, $params, 'full');
}
if ($id = $modx->getOption('ms2_payment_webpay_cancel_id', null, 0)) {
    $cancel = $modx->makeUrl($id, $context, $params, 'full');
}

$redirect = !empty($_REQUEST['action']) && ($_REQUEST['action'] == 'success') ? $success : $cancel;
$modx->sendRedirect($redirect);
