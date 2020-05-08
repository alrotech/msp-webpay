<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

if (!$object->xpdo) {
    return false;
}

if (!version_compare(PHP_VERSION, '7.1', '>=')) {
    $object->xpdo->log(modX::LOG_LEVEL_ERROR, 'Invalid php version. Minimal supported version – 7.1, because less versions not supported more by PHP core team. Details here: http://php.net/supported-versions.php');

    return false;
}

return true;
