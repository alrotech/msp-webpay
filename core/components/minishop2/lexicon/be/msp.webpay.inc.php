<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

$_lang['area_ms2_payment_webpay'] = 'WebPay';

$_lang['setting_ms2_payment_webpay_store_id'] = 'Ідэнтыфікатар крамы ў сістэме WebPay';
$_lang['setting_ms2_payment_webpay_store_id_desc'] = 'Ўтрымоўвае унікальны ідэнтыфікатар крамы. Дадзены ідэнтыфікатар ствараецца пры рэгістрацыі ў сістэме WebPay і высылаецца ў лісце.';

$_lang['setting_ms2_payment_webpay_secret_key'] = 'Сакрэтны ключ';
$_lang['setting_ms2_payment_webpay_secret_key_desc'] = 'Паслядоўнасць выпадковых сімвалаў, якая задаецца ў панэлі WebPay. Удзельнічае ў фарміраванні электроннага подпісу і выкарыстоўваецца для праверкі плацяжоў.';

$_lang['setting_ms2_payment_webpay_login'] = 'Лагін ў сістэме WebPay';
$_lang['setting_ms2_payment_webpay_login_desc'] = 'Лагін, з якім вы ўваходзіце ў панэль кіравання WebPay. Патрэбен для праверкі плацяжу.';

$_lang['setting_ms2_payment_webpay_password'] = 'Пароль у сістэме WebPay';
$_lang['setting_ms2_payment_webpay_password_desc'] = 'Пароль, с которым вы входите в панель управления WebPay. Нужен для проверки платежа.';

$_lang['setting_ms2_payment_webpay_checkout_url'] = 'Адрас для выканання запытаў';
$_lang['setting_ms2_payment_webpay_checkout_url_desc'] = 'Адрас, куды будзе адпраўляцца карыстальнік для выканання аплаты замовы.';

$_lang['setting_ms2_payment_webpay_gate_url'] = 'Адрас для выканання праверкі плацяжу';
$_lang['setting_ms2_payment_webpay_gate_url_desc'] = 'Адрас, куды будзе адпраўляцца запыт на праверку плацяжу.';

$_lang['setting_ms2_payment_webpay_version'] = 'Версія формы аплаты';
$_lang['setting_ms2_payment_webpay_version_desc'] = 'Бягучы нумар версіі = 2.';

$_lang['setting_ms2_payment_webpay_developer_mode'] = 'Рэжым здзяйснення тэставых плацяжоў';
$_lang['setting_ms2_payment_webpay_developer_mode_desc'] = 'Пры значэнні "Так", усе запыты аплаты будуць адпраўляцца на тэставую сераду апрацоўкі плацяжоў WebPay. Пры ўключэнні дадзенага рэжыму налады checkout_url і gate_url ігнаруюцца.';

$_lang['setting_ms2_payment_webpay_currency'] = 'Прапанаваная валюта плацяжу';
$_lang['setting_ms2_payment_webpay_currency_desc'] = 'Карыстальнік можа змяніць яе ў працэсе аплаты. Літарны трохзначны код валюты згодна ISO4271. Даступныя варыянты: BYN, USD, EUR, RUB. У рэжыме тэставання даступная толькі BYN.';

$_lang['setting_ms2_payment_webpay_language'] = 'Мова WebPay';
$_lang['setting_ms2_payment_webpay_language_desc'] = 'Пакажыце код мовы, на якой паказваць сайт WebPay пры аплаце. Даступныя варыянты: <strong>russian</strong>, <strong>english</strong>.';

$_lang['setting_ms2_payment_webpay_success_id'] = 'Старонка паспяховай аплаты WebPay';
$_lang['setting_ms2_payment_webpay_success_id_desc'] = 'Карыстальнік будзе адпраўлены на гэты артыкул пасля завяршэння аплаты. Рэкамендуецца пазначыць id старонкі з кошыкам, для вываду замовы.';

$_lang['setting_ms2_payment_webpay_failure_id'] = 'Старонка адмовы ад аплаты WebPay';
$_lang['setting_ms2_payment_webpay_failure_id_desc'] = 'Карыстальнік будзе адпраўлены на гэтую старонку пры няўдалай аплаце. Рэкамендуецца пазначыць id старонкі з кошыкам, для вываду замовы';
