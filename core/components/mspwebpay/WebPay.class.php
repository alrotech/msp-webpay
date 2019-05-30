<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

if (!class_exists('msPaymentInterface')) {
    require_once dirname(dirname(__DIR__)) . '/model/minishop2/mspaymenthandler.class.php';
}

class WebPay extends msPaymentHandler implements msPaymentInterface {
    public $config;
    public $modx;

    function __construct(xPDOObject $object, $config = array()) {
        $this->modx = & $object->xpdo;

        $siteUrl = $this->modx->getOption('site_url');
        $assetsUrl = $this->modx->getOption('minishop2.assets_url', $config, $this->modx->getOption('assets_url').'components/minishop2/');
        $paymentUrl = $siteUrl . substr($assetsUrl, 1) . 'payment/webpay.php';

        $this->config = array_merge(
            array(
                'store_name' => $this->modx->getOption('site_name'),
                'store_id' => $this->modx->getOption('ms2_payment_webpay_store_id'),
                'secret' => $this->modx->getOption('ms2_payment_webpay_secret_key'),
                'login' => $this->modx->getOption('ms2_payment_webpay_login'),
                'password' => $this->modx->getOption('ms2_payment_webpay_password'),

                'payment_url' => $paymentUrl,
                'checkout_url' => $this->modx->getOption('ms2_payment_webpay_checkout_url'),
                'gate_url' => $this->modx->getOption('ms2_payment_webpay_gate_url'),

                'version' => $this->modx->getOption('ms2_payment_webpay_version', 2, true),
                'language' => $this->modx->getOption('ms2_payment_webpay_language', 'russian', true),
                'currency' => $this->modx->getOption('ms2_payment_webpay_currency', 'BYR', true),

                'developer_mode' => $this->modx->getOption('ms2_payment_webpay_developer_mode', 0, true),

                'json_response' => false
            ),
            $config
        );

        if ($this->config['developer_mode']) {
            $this->config['checkout_url'] = 'https://secure.sandbox.webpay.by:8843';
            $this->config['gate_url'] = 'https://sandbox.webpay.by';
        }
    }

    public function send(msOrder $order) {
        $link = $this->getPaymentLink($order);

        return $this->success('', array('redirect' => $link));
    }

    public function getPaymentLink(msOrder $order) {
        $id = $order->get('id');
        $cost = $order->get('cost');

        $user = $order->getOne('User');
        if ($user) {
            $user = $user->getOne('Profile');
        }
        $address = $order->getOne('Address');
        $delivery = $order->getOne('Delivery');

        $products = $this->modx->getCollection('msOrderProduct', array('order_id' => $id));

        $random = md5(substr(md5(time()), 5, 10));

        $request = array(
            '*scart' => '',
            'wsb_order_num' => $id,
            'wsb_storeid' => $this->config['store_id'],
            'wsb_store' => $this->config['store_name'],
            'wsb_version' => $this->config['version'],
            'wsb_currency_id' => $this->config['currency'],
            'wsb_language_id' => $this->config['language'],
            'wsb_seed' => $random,
            'wsb_test' => $this->config['developer_mode'],
            'wsb_return_url' => $this->config['payment_url'] . '?action=success',
            'wsb_cancel_return_url' => $this->config['payment_url'] . '?action=cancel',
            'wsb_notify_url' => $this->config['payment_url'] . '?action=notify',
            //,'wsb_tax' => 0 // not required
            'wsb_shipping_name' => $delivery->get('name'),
            'wsb_shipping_price' => $delivery->get('price'),
            //,'wsb_discount_name' => '?' // not required
            //,'wsb_discount_price' => '?' // not required
            'wsb_total' => $cost,
            'wsb_email' => $user->get('email'),
            'wsb_phone' => $address->get('phone'),
            //,'wsb_icn' => '' // not required // special
            //,'wsb_card' => '' // not required // special
        );

        $i = 0;
        foreach ($products as $product) {
            $request["wsb_invoice_item_name[$i]"] = $product->get('name');
            $request["wsb_invoice_item_quantity[$i]"] = $product->get('count');
            $request["wsb_invoice_item_price[$i]"] = $product->get('price');
            $i++;
        }
        $signature = sha1(
             $request['wsb_seed']
            .$request['wsb_storeid']
            .$request['wsb_order_num']
            .$request['wsb_test']
            .$request['wsb_currency_id']
            .$request['wsb_total']
            .$this->config['secret']
        );
        $request['wsb_signature'] = $signature;

        $link = $this->config['payment_url']
            .'?'
            .http_build_query(
                array(
                    'action' => 'payment',
                    'request' => json_encode($request)
                )
            );

        return $link;
    }

    public function receive(msOrder $order, $params = array()) {
        switch ($params['action']) {
            case 'success':
                if (empty($params['wsb_tid'])) {
                    $this->paymentError("Could not get transaction id. Process stopped.");
                }

                $transaction_id = $params['wsb_tid'];
                $postdata = '*API=&API_XML_REQUEST='.urlencode('<?xml version="1.0" encoding="ISO-8859-1"?><wsb_api_request><command>get_transaction</command><authorization><username>'.$this->config['login'].'</username><password>'.md5($this->config['password']).'</password></authorization><fields><transaction_id>'.$transaction_id.'</transaction_id></fields></wsb_api_request>');

                $curl = curl_init($this->config['gate_url']);
                curl_setopt ($curl, CURLOPT_HEADER, 0);
                curl_setopt ($curl, CURLOPT_POST, 1);
                curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
                $response = curl_exec ($curl);
                curl_close ($curl);

                $xml = simplexml_load_string($response);

                if ((string) $xml->status == 'success') {
                    $fields = (array) $xml->fields;

                    $crc = md5(
                         $fields['transaction_id']
                        .$fields['batch_timestamp']
                        .$fields['currency_id']
                        .$fields['amount']
                        .$fields['payment_method']
                        .$fields['payment_type']
                        .$fields['order_id']
                        .$fields['rrn']
                        .$this->config['secret']
                    );

                    if ($crc == $fields['wsb_signature'] && in_array($fields['payment_type'], array(1,4))) {
                        $miniShop2 = $this->modx->getService('miniShop2');
                        @$this->modx->context->key = 'mgr';
                        $miniShop2->changeOrderStatus($order->get('id'), 2);
                    } else {
                        $this->paymentError('Transaction with id ' . $transaction_id . ' is not valid.');
                    }
                } else {
                    $this->paymentError('Could not check transaction with id ' . $transaction_id);
                }
                break;
            case 'notify':
                $crc = md5(
                     $params['batch_timestamp']
                    .$params['currency_id']
                    .$params['amount']
                    .$params['payment_method']
                    .$params['order_id']
                    .$params['site_order_id']
                    .$params['transaction_id']
                    .$params['payment_type']
                    .$params['rrn']
                    .$this->config['secret']
                );
                if ($crc == $params['wsb_signature'] && in_array($params['payment_type'], array(1,4))) {
                    $miniShop2 = $this->modx->getService('miniShop2');
                    @$this->modx->context->key = 'mgr';
                    $miniShop2->changeOrderStatus($order->get('id'), 2);
                    header("HTTP/1.0 200 OK");
                    exit;
                } else {
                    $this->paymentError('Transaction with id ' . $params['transaction_id'] . ' is not valid.');
                }
                break;
            case 'cancel':
                $miniShop2 = $this->modx->getService('miniShop2');
                @$this->modx->context->key = 'mgr';
                $miniShop2->changeOrderStatus($order->get('id'), 4);
                break;
        }
    }

    public function paymentError($text, $request = array()) {
        $this->modx->log(modX::LOG_LEVEL_ERROR,'[miniShop2:WebPay] ' . $text . ', request: ' . print_r($request, 1));
        header("HTTP/1.0 400 Bad Request");

        die('ERROR: ' . $text);
    }
}
