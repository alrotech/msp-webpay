<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

if (!$object->xpdo && !$object->xpdo instanceof modX) {
    return true;
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        /** @var miniShop2 $shop */
        if ($shop = $object->xpdo->getService('miniShop2')) {
            $shop->addService('payment', WebPay::class, '{core_path}components/mspwebpay/WebPay.class.php');
        }

        break;

    case xPDOTransport::ACTION_UNINSTALL:

        /** @var miniShop2 $shop */
        if ($shop = $object->xpdo->getService('miniShop2')) {
            $shop->removeService('payment', WebPay::class);
        }

        break;
}
