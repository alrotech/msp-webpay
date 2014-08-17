<?php

/**
 * Build the setup options form.
 */
$exists = false;
$output = null;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $exists = $modx->getCount('modSystemSetting', array('key:LIKE' => '%_webpay_%'));
        break;

    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

if (!$exists) {
    if ($modx->getOption('manager_language') == 'ru') {
        $text = '
            Для полноценной работы оплаты WebPay необходимо заполнить параметры, выданные вам после заключения договора.
            <label for="webpay-store-id">Идентификатор магазина:</label>
            <input type="text" name="webpay-store-id" id="webpay-store-id" width="300" value="" />

            <label for="webpay-login">Логин в системе WebPay:</label>
            <input type="text" name="webpay-login" id="webpay-login" width="300" value="" />

            <label for="webpay-password">Пароль в системе WebPay:</label>
            <input type="text" name="webpay-password" id="webpay-password" width="300" value="" />

            <label for="webpay-secret-key">Секретный ключ:</label>
            <input type="text" name="webpay-secret-key" id="webpay-secret-key" width="300" value="" />

			<small>Вы можете пропустить этот шаг и заполнить эти поля позже в системных настройках.</small>';
    }
    else {
        $text = '
            To complete the work necessary to complete the payment WebPay options given to you after the conclusion of the contract.
            <label for="webpay-store-id">Store ID:</label>
            <input type="text" name="webpay-store-id" id="webpay-store-id" width="300" value="" />

            <label for="webpay-login">Login in WebPay System:</label>
            <input type="text" name="webpay-login" id="webpay-login" width="300" value="" />

            <label for="webpay-password">Password in WebPay System:</label>
            <input type="text" name="webpay-password" id="webpay-password" width="300" value="" />

            <label for="webpay-secret-key">Secret Key:</label>
            <input type="text" name="webpay-secret-key" id="webpay-secret-key" width="300" value="" />

			<small>You can skip this step and complete these fields later in the system settings.</small>';
    }

    $output = '
		<style>
			#setup_form_wrapper {font: normal 12px Arial;line-height:18px;}
			#setup_form_wrapper ul {margin-left: 5px; font-size: 10px; list-style: disc inside;}
			#setup_form_wrapper a {color: #08C;}
			#setup_form_wrapper small {font-size: 10px; color:#555; font-style:italic;}
			#setup_form_wrapper label {color: black; font-weight: bold;}
		</style>
		<div id="setup_form_wrapper">'.$text.'</div>
	';
}

return $output;
