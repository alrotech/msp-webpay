<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

if (!$object->xpdo && !$object->xpdo instanceof modX) {
    return true;
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:

        $object->xpdo->newObject(
            msPayment::class,
            [
                'name' => 'WebPay',
                'description' => null,
                'price' => 0,
                'logo' => MODX_ASSETS_URL . 'components/mspwebpay/webpay.png',
                'rank' => 0,
                'active' => 1,
                'class' => 'WebPay',
                'properties' => null // todo: setup minimal default properties
            ]
        )->save();

        break;

    case xPDOTransport::ACTION_UPGRADE:

        $collection = $object->xpdo->getCollection(msPayment::class, ['class' => 'WebPay']);

        /** @var msPayment $item */
        foreach ($collection as $item) {
            if (!$item->get('logo')) {
                $item->set('logo', MODX_ASSETS_URL . 'components/mspwebpay/webpay.png');
                $item->save();
            }
        }

        break;

    case xPDOTransport::ACTION_UNINSTALL:

        $object->xpdo->removeObject(msPayment::class, ['class' => 'WebPay']);

        break;
}
