<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

if (!$object->xpdo) {
    return false;
}

if (PHP_VERSION_ID <= 70200) {
    $object->xpdo->log(modX::LOG_LEVEL_ERROR, 'Invalid php version. Minimal supported version – 7.2, because less versions not supported more by PHP core team. Details here: http://php.net/supported-versions.php');

    return false;
}

return true;
