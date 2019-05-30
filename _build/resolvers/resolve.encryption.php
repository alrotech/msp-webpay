<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

/** @var xPDOTransport $transport */
$transport->xpdo->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
$transport->xpdo->loadClass('EncryptedVehicle', MODX_CORE_PATH . 'components/' . strtolower($transport->name) . '/', true, true);
