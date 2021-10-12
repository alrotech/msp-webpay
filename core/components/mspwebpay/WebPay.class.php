<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

use Sabre\Xml\Service;

if (!class_exists('ConfigurablePaymentHandler')) {
    $path = MODX_CORE_PATH. 'components/mspaymentprops/ConfigurablePaymentHandler.class.php';
    if (is_readable($path)) {
        require_once $path;
    }
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Class for handling requests to WebPay API
 */
class WebPay extends ConfigurablePaymentHandler
{
    public const OPTION_STORE_NAME = 'store_name';
    public const OPTION_STORE_ID = 'store_id';
    public const OPTION_SECRET_KEY = 'secret_key';
    public const OPTION_LOGIN = 'login';
    public const OPTION_PASSWORD = 'password';
    public const OPTION_CHECKOUT_URL = 'checkout_url';
    public const OPTION_GATE_URL = 'gate_url';
    public const OPTION_LANGUAGE = 'language';
    public const OPTION_VERSION = 'version';
    public const OPTION_CURRENCY = 'currency';

    public const OPTION_DEVELOPER_MODE = 'developer_mode';
    public const OPTION_CONFIRMATION_MODE = 'confirmation_mode';
    public const OPTION_CHECKOUT_URL_TEST = 'checkout_url_test';
    public const OPTION_GATE_URL_TEST = 'gate_url_test';

    public const OPTION_SUCCESS_STATUS = 'success_status';
    public const OPTION_FAILURE_STATUS = 'failure_status';
    public const OPTION_SUCCESS_PAGE = 'success_page';
    public const OPTION_FAILURE_PAGE = 'failure_page';
    public const OPTION_UNPAID_PAGE = 'unpaid_page';

    /** @var modX */
    public $modx;

    /**
     * @return xPDO
     */
    protected function getMODX(): xPDO
    {
        return $this->modx;
    }

    /**
     * @return string
     */
    public static function getPrefix(): string
    {
        return 'ms2_payment_' . strtolower(__CLASS__);
    }

    /**
     * WebPay constructor.
     * @param xPDOObject $object
     * @param array $config
     */
    public function __construct(xPDOObject $object, $config = [])
    {
        parent::__construct($object, $config);

        $this->config = $config;
    }

    /**
     * @param msOrder $order
     *
     * @return array|string
     * @throws ReflectionException|JsonException
     */
    public function send(msOrder $order)
    {
        $this->config = $this->getProperties($order->getOne('Payment'));

        if ($this->config[self::OPTION_CONFIRMATION_MODE]) {
            return parent::send($order);
        }

        if (!$link = $this->getPaymentLink($order)) {
            return $this->error('Token and redirect url can not be requested. Please, look at error log.');
        }

        return $this->success('', ['redirect' => $link]);
    }

    /**
     * @param msOrder $order
     *
     * @return string
     * @throws ReflectionException
     * @throws JsonException
     */
    public function getPaymentLink(msOrder $order)
    {
        /** @var msPayment $payment */
        $payment = $order->getOne('Payment');

        /** @var msDelivery $delivery */
        $delivery = $order->getOne('Delivery');

        /** @var modUser $user */
        $user = $order->getOne('User');

        if ($user) {
            /** @var modUserProfile $user */
            $user = $user->getOne('Profile');
        }

        $this->config = $this->getProperties($payment);
        $this->adjustCheckoutUrls();

        $this->config['return_url'] = rtrim($this->getMODX()->getOption('site_url'), '/')
            . $this->getMODX()->getOption('assets_url') . 'components/mspwebpay/webpay.php';

        if (empty($this->config[self::OPTION_STORE_NAME])) {
            $this->config[self::OPTION_STORE_NAME] = $this->modx->getOption('site_name', null);
        }

        $seed = md5(substr(md5(time()), 5, 10));

        $request = [
            'wsb_seed' => $seed,
            'wsb_storeid' => $this->config[self::OPTION_STORE_ID],
            'wsb_order_num' => $order->get('id'),
            'wsb_test' => $this->config[self::OPTION_DEVELOPER_MODE],
            'wsb_currency_id' => $this->config[self::OPTION_CURRENCY],
            'wsb_total' => $order->get('cost'),

            '*scart' => '',
            'wsb_store' => $this->config[self::OPTION_STORE_NAME],
            'wsb_version' => $this->config[self::OPTION_VERSION],
            'wsb_language_id' => $this->config[self::OPTION_LANGUAGE],
            'wsb_return_url' => $this->config['return_url'] . '?action=success',
            'wsb_cancel_return_url' => $this->config['return_url'] . '?action=cancel',
            'wsb_notify_url' => $this->config['return_url'] . '?action=notify',
            'wsb_shipping_name' => $delivery->get('name'),
            'wsb_shipping_price' => $delivery->get('price'),
            'wsb_email' => $user->get('email')
        ];

        $products = $this->modx->getCollection(msOrderProduct::class, ['order_id' => $order->get('id')]);

        $i = 0;
        foreach ($products as $product) {
            /** @var msOrderProduct $product */
            $request["wsb_invoice_item_name[$i]"] = $product->get('name');
            $request["wsb_invoice_item_quantity[$i]"] = $product->get('count');
            $request["wsb_invoice_item_price[$i]"] = $product->get('price');
            $i++;
        }

        $signatureParts = array_intersect_key($request, array_flip([
            'wsb_seed',
            'wsb_storeid',
            'wsb_order_num',
            'wsb_test',
            'wsb_currency_id',
            'wsb_total'
        ]));
        $signatureParts[] = $this->config[self::OPTION_SECRET_KEY];

        $request['wsb_signature'] = sha1(implode('', $signatureParts));

        return $this->config['return_url'] . '?' . http_build_query(
                [
                    'action' => 'payment',
                    'wsb_order_num' => $order->get('id'),
                    'request' => json_encode($request, JSON_THROW_ON_ERROR)
                ]
            );
    }

    public function adjustCheckoutUrls(): void
    {
        if ($this->config[self::OPTION_DEVELOPER_MODE]) {
            $this->config[self::OPTION_CHECKOUT_URL] = $this->config[self::OPTION_CHECKOUT_URL_TEST];
            $this->config[self::OPTION_GATE_URL] = $this->config[self::OPTION_GATE_URL_TEST];
        }
    }

    /**
     * @param msOrder $order
     * @param array   $params
     *
     * @return array|string|void
     * @throws JsonException
     */
    public function receive(msOrder $order, $params = [])
    {
        /** @var msPayment $payment */
        $payment = $order->getOne('Payment');

        $this->config = $this->getProperties($payment);
        $this->adjustCheckoutUrls();

        switch ($params['action']) {
            case 'success':
                if (empty($params['wsb_tid'])) {
                    $this->paymentError("Could not get transaction id. Process stopped.");
                }

                $transaction_id = $params['wsb_tid'];

                $xml = (new Service)->write('wsb_api_request', [
                    'command' => 'get_transaction',
                    'authorization' => [
                        'username' => $this->config['login'],
                        'password' => md5($this->config['password'])
                    ],
                    'fields' => [
                        'transaction_id' => $transaction_id
                    ]
                ]);

                $postdata = '*API=&API_XML_REQUEST=' . urlencode($xml);

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

                    $crc = md5(
                        $fields['transaction_id'] .
                        $fields['batch_timestamp'] .
                        $fields['currency_id'] .
                        $fields['amount'] .
                        $fields['payment_method'] .
                        $fields['payment_type'] .
                        $fields['order_id'] .
                        $fields['rrn'] .
                        $this->config[static::OPTION_SECRET_KEY]
                    );

                    if ($crc === $fields['wsb_signature'] && in_array((int) $fields['payment_type'], [1, 4], true)) {
                        $miniShop2 = $this->modx->getService('miniShop2');
                        @$this->modx->context->key = 'mgr';
                        if (!empty($miniShop2)) {
                            $miniShop2->changeOrderStatus($order->get('id'), 2);
                        }
                    } else {
                        $this->paymentError('Transaction with id ' . $transaction_id . ' is not valid.', array_merge($params, $fields?? [], $this->config));
                    }
                } else {
                    $this->paymentError('Could not check transaction with id ' . $transaction_id, $params);
                }
                break;
            case 'notify':
                $crc = md5($params['batch_timestamp'] . $params['currency_id'] . $params['amount'] . $params['payment_method'] . $params['order_id'] . $params['site_order_id'] . $params['transaction_id'] . $params['payment_type'] . $params['rrn'] . $this->config['secret']);
                if ($crc === $params['wsb_signature'] && in_array($params['payment_type'], [1, 4], true)) {
                    $miniShop2 = $this->modx->getService('miniShop2');
                    @$this->modx->context->key = 'mgr';
                    if (!empty($miniShop2)) {
                        $miniShop2->changeOrderStatus($order->get('id'), 2); // todo: get status from config
                    }
                    header("HTTP/1.0 200 OK");
                    exit;
                }

                $this->paymentError('Transaction with id ' . $params['transaction_id'] . ' is not valid.', $params);

                break;
            case 'cancel':
                $miniShop2 = $this->modx->getService('miniShop2');
                @$this->modx->context->key = 'mgr';
                if (!empty($miniShop2)) {
                    $miniShop2->changeOrderStatus($order->get('id'), 4); // todo: get status from config
                }
                break;
        }
    }

    /**
     * @param       $text
     * @param array $request
     *
     * @throws JsonException
     */
    public function paymentError($text, $request = []): void
    {
        if (!empty($request)) {
            $logMessage = $text . sprintf(', request: %s', json_encode($request, JSON_THROW_ON_ERROR));
        }

        $this->log($logMessage ?? $text);

        header('HTTP/1.0 400 Bad Request');
        die('ERROR: ' . $text);
    }
}
