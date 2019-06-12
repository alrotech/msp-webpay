<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

$_lang['area_ms2_payment_webpay'] = 'WebPay';

$_lang['setting_ms2_payment_webpay_store_id'] = 'Идентификатор магазина в системе WebPay';
$_lang['setting_ms2_payment_webpay_store_id_desc'] = 'Cодержит уникальный идентификатор магазина. Данный идентификатор создается при регистрации в системе WebPay и высылается в письме.';

$_lang['setting_ms2_payment_webpay_secret_key'] = 'Секретный ключ';
$_lang['setting_ms2_payment_webpay_secret_key_desc'] = 'Последовательность случайных символов, задаваемая в панели WebPay. Участвует в формировании электронной подписи и используется для проверки платежей.';

$_lang['setting_ms2_payment_webpay_login'] = 'Логин в системе WebPay';
$_lang['setting_ms2_payment_webpay_login_desc'] = 'Логин, с которым вы входите в панель управления WebPay. Нужен для проверки платежа.';

$_lang['setting_ms2_payment_webpay_password'] = 'Пароль в системе WebPay';
$_lang['setting_ms2_payment_webpay_password_desc'] = 'Логин, с которым вы входите в панель управления WebPay. Нужен для проверки платежа.';

$_lang['setting_ms2_payment_webpay_checkout_url'] = 'Адрес для выполнения запросов';
$_lang['setting_ms2_payment_webpay_checkout_url_desc'] = 'Адрес, куда будет отправляться пользователь для выполнения оплаты заказа.';

$_lang['setting_ms2_payment_webpay_gate_url'] = 'Адрес для выполнения проверки платежа';
$_lang['setting_ms2_payment_webpay_gate_url_desc'] = 'Адрес, куда будет отправляться запрос на проверку платежа.';

$_lang['setting_ms2_payment_webpay_version'] = 'Версия формы оплаты';
$_lang['setting_ms2_payment_webpay_version_desc'] = 'Текущий номер версии = 2.';

$_lang['setting_ms2_payment_webpay_developer_mode'] = 'Режим совершения тестовых платежей';
$_lang['setting_ms2_payment_webpay_developer_mode_desc'] = 'При значении "Да", все запросы оплаты будут отправляться на тестовую среду обработки платежей WebPay. При включении данного режима настройки checkout_url и gate_url игнорируются.';

$_lang['setting_ms2_payment_webpay_currency'] = 'Предлагаемая валюта платежа';
$_lang['setting_ms2_payment_webpay_currency_desc'] = 'Пользователь может изменить ее в процессе оплаты. Буквенный трехзначный код валюты согласно ISO4271. Доступны варианты: BYN, USD, EUR, RUB. В режиме тестирования доступна только BYN.';

$_lang['setting_ms2_payment_webpay_language'] = 'Язык WebPay';
$_lang['setting_ms2_payment_webpay_language_desc'] = 'Укажите код языка, на котором показывать сайт WebPay при оплате. Доступны варианты: <strong>russian</strong>, <strong>english</strong>.';

$_lang['setting_ms2_payment_webpay_success_id'] = 'Страница успешной оплаты WebPay';
$_lang['setting_ms2_payment_webpay_success_id_desc'] = 'Пользователь будет отправлен на эту страницу после завершения оплаты. Рекомендуется указать id страницы с корзиной, для вывода заказа.';

$_lang['setting_ms2_payment_webpay_failure_id'] = 'Страница отказа от оплаты WebPay';
$_lang['setting_ms2_payment_webpay_failure_id_desc'] = 'Пользователь будет отправлен на эту страницу при неудачной оплате. Рекомендуется указать id страницы с корзиной, для вывода заказа';
