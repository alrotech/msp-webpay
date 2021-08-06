<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

define('MODX_API_MODE', true);
require dirname(__DIR__, 3) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->loadCustomClasses('payment');

if (!class_exists('WebPay')) {
    $msg = '[ms2::payment::webpay] Could not load payment class "WebPay"';
    $modx->log(modX::LOG_LEVEL_ERROR, $msg);
    die($msg);
}

$orderId = (int)$_GET['wsb_order_num'];
$order = $modx->getObject('msOrder', $orderId);

if (!$order || !$order instanceof msOrder) {
    $msg = '[ms2::payment::webpay] Order not found. Exit.';
    $modx->log(modX::LOG_LEVEL_ERROR, $msg);
    die($msg);
}

$handler = new WebPay($order);

switch ($_GET['action']) {
    case 'payment':
        $request = json_decode($_REQUEST['request'], true);

        $handler->config = $handler->getProperties($order->getOne('Payment'));
        $handler->adjustCheckoutUrls();

        $url = $handler->config[WebPay::OPTION_CHECKOUT_URL];

        $fields = [];
        foreach ($request as $k => $v) {
            $fields[] = sprintf('<input type="hidden" name="%s" value="%s">', $k, $v);
        }

        $form = sprintf('<form action="%s" method="post">%s</form>', $url, implode('', $fields));
        $script = '<script>window.onload=function(){document.forms[0].submit();}</script>';

        echo $form, $script; exit;

        break;
    case 'notify':
        if (empty($_POST['site_order_id'])) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ms2:payment:webpay] Returned empty order id.');
        }
        /** @var msOrder $order */
        $order = $modx->getObject('msOrder', $_POST['site_order_id']);
        if ($order) {
            $_POST['action'] = $_GET['action'];
            $handler->receive($order, $_POST);
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ms2:payment:WebPay] Could not retrieve order with id ' . $_POST['site_order_id']);
        }
        break;
    case 'success':
    case 'cancel':
        if (empty($_REQUEST['wsb_order_num'])) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ms2:payment:WebPay] Returned empty order id.');
        }
        /** @var msOrder $order */
        $order = $modx->getObject('msOrder', $_REQUEST['wsb_order_num']);
        if ($order) {
            $handler->receive($order, $_REQUEST);
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ms2:payment:WebPay] Could not retrieve order with id ' . $_REQUEST['wsb_order_num']);
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

$redirect = !empty($_REQUEST['action']) && ($_REQUEST['action'] === 'success') ? $success : $cancel;

$modx->sendRedirect($redirect);
