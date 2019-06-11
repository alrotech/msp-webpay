<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

define('MODX_API_MODE', true);
require dirname(dirname(dirname(__DIR__))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2');
$miniShop2->loadCustomClasses('payment');

if (!class_exists('WebPay')) {
    $msg = '[ms2::payment::WebPay] Could not load payment class "WebPay"';
    $modx->log(modX::LOG_LEVEL_ERROR, $msg, '', '', __FILE__, __LINE__); die($msg);
}

$context = '';
$params = [];

$orderId = (int)$_GET['order'];
$order = $modx->getObject('msOrder', $orderId);

if (!$order || !$order instanceof msOrder) {
    BePaid::fail("Order with id '$orderId' not found. Exit.");
}

$handler = new BePaid($order);

/* @var msPaymentInterface|WebPay $handler */
$handler = new WebPay($modx->newObject('msOrder'));

switch ($_GET['action']) {
    case 'payment':
        $request = json_decode($_REQUEST['request']);
        $url = $handler->config['checkout_url'];

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
            $modx->log(modX::LOG_LEVEL_ERROR, '[ms2:payment:WebPay] Returned empty order id.');
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
        if ($order = $modx->getObject('msOrder', $_REQUEST['wsb_order_num'])) {
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
