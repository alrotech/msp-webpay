<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

if (!class_exists('msPaymentHandler')) {
    $path = dirname(dirname(dirname(__DIR__))) . '/minishop2/model/minishop2/mspaymenthandler.class.php';
    if (is_readable($path)) {
        require_once $path;
    }
}

/**
 * Class for handling requests to WebPay API
 */
class WebPay extends msPaymentHandler
{
    const PREFIX = 'ms2_payment_webpay';

    const OPTION_STORE_ID = 'store_id';
    const OPTION_SECRET_KEY = 'secret_key';
    const OPTION_LOGIN = 'login';
    const OPTION_PASSWORD = 'password';
    const OPTION_CHECKOUT_URL = 'checkout_url';
    const OPTION_GATE_URL = 'gate_url';
    const OPTION_LANGUAGE = 'language';
    const OPTION_VERSION = 'version';
    const OPTION_COUNTRY = 'country';
    const OPTION_CURRENCY = 'currency';
    const OPTION_DEVELOPER_MODE = 'developer_mode';

    const OPTION_SUCCESS_STATUS = 'success_status';
    const OPTION_FAILURE_STATUS = 'failure_status';
    const OPTION_SUCCESS_PAGE = 'success_page';
    const OPTION_FAILURE_PAGE = 'failure_page';
    const OPTION_UNPAID_PAGE = 'unpaid_page';

    /** @var array */
    public $config = [];

    /** @var xPDO */
    public $modx;

    /** @var miniShop2 */
    public $ms2;

    /**
     * @return xPDO
     */
    protected function getMODX()
    {
        return $this->modx;
    }

    /**
     * WebPay constructor.
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = [])
    {
        parent::__construct($object, $config);
        
        $this->config = array_merge(
            array(
                'store_name' => $this->modx->getOption('site_name'),
                'store_id' => $this->modx->getOption('ms2_payment_webpay_store_id', '473977949'),
                'secret' => $this->modx->getOption('ms2_payment_webpay_secret_key', 'gsfdgwertrfgsfdgsfzdfgdsfgsdf'),
                'login' => $this->modx->getOption('ms2_payment_webpay_login', 'modcasts'),
                'password' => $this->modx->getOption('ms2_payment_webpay_password', '8uTrE96Fjh'),

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

        $this->config['payment_url'] = join('/', [
            rtrim($this->getMODX()->getOption('site_url'), '/'),
            ltrim($this->getMODX()->getOption('assets_url'), '/'),
            'components/mspwebpay/webpay.php'
        ]);

        if ($this->config['developer_mode']) {
            $this->config['checkout_url'] = 'https://securesandbox.webpay.by';
            $this->config['gate_url'] = 'https://sandbox.webpay.by';
        }
    }

    /**
     * @param msOrder $order
     * @return array|string
     */
    public function send(msOrder $order)
    {
        $link = $this->getPaymentLink($order);

        return $this->success('', ['redirect' => $link]);
    }

    /**
     * @param msOrder $order
     * @return string
     */
    public function getPaymentLink(msOrder $order)
    {
        $id = $order->get('id');
        $cost = $order->get('cost');

        $user = $order->getOne('User');
        if ($user) {
            $user = $user->getOne('Profile');
        }
        $address = $order->getOne('Address');
        $delivery = $order->getOne('Delivery');

        $products = $this->modx->getCollection('msOrderProduct', ['order_id' => $id]);

        $random = md5(substr(md5(time()), 5, 10));

        $request = [
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
        ];

        $i = 0;
        /** @var msOrderProduct $product */
        foreach ($products as $product) {
            $request["wsb_invoice_item_name[$i]"] = $product->get('name');
            $request["wsb_invoice_item_quantity[$i]"] = $product->get('count');
            $request["wsb_invoice_item_price[$i]"] = $product->get('price');
            $i++;
        }
        $signature = sha1($request['wsb_seed'] . $request['wsb_storeid'] . $request['wsb_order_num'] . $request['wsb_test'] . $request['wsb_currency_id'] . $request['wsb_total'] . $this->config['secret']);
        $request['wsb_signature'] = $signature;

        $link = $this->config['payment_url'] . '?' . http_build_query([
                'action' => 'payment',
                'request' => json_encode($request),
            ]);

        return $link;
    }

    /**
     * @param msOrder $order
     * @param array $params
     * @return array|string|void
     */
    public function receive(msOrder $order, $params = [])
    {
        switch ($params['action']) {
            case 'success':
                if (empty($params['wsb_tid'])) {
                    $this->paymentError("Could not get transaction id. Process stopped.");
                }

                $transaction_id = $params['wsb_tid'];
                $postdata = '*API=&API_XML_REQUEST=' . urlencode('<?xml version="1.0" encoding="ISO-8859-1"?><wsb_api_request><command>get_transaction</command><authorization><username>' . $this->config['login'] . '</username><password>' . md5($this->config['password']) . '</password></authorization><fields><transaction_id>' . $transaction_id . '</transaction_id></fields></wsb_api_request>');

                $curl = curl_init($this->config['gate_url']);
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($curl);
                curl_close($curl);

                $xml = simplexml_load_string($response);

                if ((string)$xml->status === 'success') {
                    $fields = (array)$xml->fields;

                    $crc = md5($fields['transaction_id'] . $fields['batch_timestamp'] . $fields['currency_id'] . $fields['amount'] . $fields['payment_method'] . $fields['payment_type'] . $fields['order_id'] . $fields['rrn'] . $this->config['secret']);

                    if ($crc === $fields['wsb_signature'] && in_array($fields['payment_type'], [1, 4], true)) {
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
                $crc = md5($params['batch_timestamp'] . $params['currency_id'] . $params['amount'] . $params['payment_method'] . $params['order_id'] . $params['site_order_id'] . $params['transaction_id'] . $params['payment_type'] . $params['rrn'] . $this->config['secret']);
                if ($crc === $params['wsb_signature'] && in_array($params['payment_type'], [1, 4], true)) {
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

    /**
     * @param $text
     * @param array $request
     */
    public function paymentError($text, $request = [])
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, '[miniShop2:WebPay] ' . $text . ', request: ' . print_r($request, 1));
        header("HTTP/1.0 400 Bad Request");

        die('ERROR: ' . $text);
    }
}
