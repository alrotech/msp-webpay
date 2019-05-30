<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

if (!$object->xpdo) {
    return false;
}

if (!version_compare(PHP_VERSION, PKG_SUPPORTS_PHP, '>=')) {
    $object->xpdo->log(modX::LOG_LEVEL_ERROR, sprintf('Invalid php version. Minimal supported version – %s, because less versions not supported more by PHP core team. Details here: http://php.net/supported-versions.php', PKG_SUPPORTS_PHP));

    return false;
}

return true;
