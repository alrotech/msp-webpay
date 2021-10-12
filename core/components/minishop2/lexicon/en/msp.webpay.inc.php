<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

$_lang['area_ms2_payment_webpay'] = 'WebPay';

$_lang['setting_ms2_payment_webpay_store_id'] = 'ID of shop in WebPay System';
$_lang['setting_ms2_payment_webpay_store_id_desc'] = 'It\'s contains an unique ID of shop. This ID was created after your registration in WebPay System and was sent by email.';

$_lang['setting_ms2_payment_webpay_secret_key'] = 'Secret Key';
$_lang['setting_ms2_payment_webpay_secret_key_desc'] = 'The sequence of random characters, given in panel WebPay. Participates in the formation of an signature and is used to verify the payment.';

$_lang['setting_ms2_payment_webpay_login'] = 'Login in WebPay System';
$_lang['setting_ms2_payment_webpay_login_desc'] = 'Login, with that you enter to control panel of WebPay. Needed for payment\'s check.';

$_lang['setting_ms2_payment_webpay_password'] = 'Password in WebPay System';
$_lang['setting_ms2_payment_webpay_password_desc'] = 'Password, with that you enter to control panel of WebPay. Needed for payment\'s check.';

$_lang['setting_ms2_payment_webpay_checkout_url'] = 'Address for checkout queries';
$_lang['setting_ms2_payment_webpay_checkout_url_desc'] = 'Address to be sent to the user to execute the payment order.';

$_lang['setting_ms2_payment_webpay_gate_url'] = 'Address for payment\'s check';
$_lang['setting_ms2_payment_webpay_gate_url_desc'] = 'Address to be sent to a request to check the payment. ';

$_lang['setting_ms2_payment_webpay_version'] = 'Version of the payment form';
$_lang['setting_ms2_payment_webpay_version_desc'] = 'Current version = 2.';

$_lang['setting_ms2_payment_webpay_developer_mode'] = 'Test mode of payments';
$_lang['setting_ms2_payment_webpay_developer_mode_desc'] = 'If the value "Yes", all requests payments will be send to a WebPay testing environment of payment processing. If you enabled this mode settings checkout_url and gate_url will be ignored.';

$_lang['setting_ms2_payment_webpay_confirmation_mode'] = 'Confirmation mode';
$_lang['setting_ms2_payment_webpay_confirmation_mode_desc'] = 'It the value "Yes", after order completed user won\'t be send to the payment page. Instead user will follow flow, when order should be confirmed before payment. After confirmation link to the payment page will be send.';

$_lang['setting_ms2_payment_webpay_currency'] = 'The proposed currency of payment';
$_lang['setting_ms2_payment_webpay_currency_desc'] = 'User can change it while paying. Literal-digit currency code according to ISO4271. Available variants: <strong>BYN</strong>, <strong>USD</strong>, <strong>EUR</strong>, <strong>RUB</strong>. In developer mode available only BYN.';

$_lang['setting_ms2_payment_webpay_language'] = 'WebPay language';
$_lang['setting_ms2_payment_webpay_language_desc'] = 'Specify the language code, which show\'s WebPay when paying. Available variants: <strong>russian</strong>, <strong>english</strong>.';

$_lang['setting_ms2_payment_webpay_success_id'] = 'WebPay successful page id';
$_lang['setting_ms2_payment_webpay_success_id_desc'] = 'The customer will be sent to this page after the completion of the payment. It is recommended to specify the id of the page with the shopping cart to order output.';

$_lang['setting_ms2_payment_webpay_failure_id'] = 'WebPay failure page id';
$_lang['setting_ms2_payment_webpay_failure_id_desc'] = 'The customer will be sent to this page if something went wrong. It is recommended to specify the id of the page with the shopping cart to order output.';
