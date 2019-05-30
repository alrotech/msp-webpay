<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

/**
 * MODX version checker for MODX less then 2.4
 *
 * @author Ivan Klimchuk <ivan@klimchuk.com>
 * @package mspBePaid
 * @subpackage build
 */

if (!$object->xpdo) {
    return false;
}

$version_data = $object->xpdo->getVersionData();
$version = implode('.', [$version_data['version'], $version_data['major_version'], $version_data['minor_version']]);

if (!version_compare($version, PKG_SUPPORTS_MODX, '>=')) {
    $object->xpdo->log(modX::LOG_LEVEL_ERROR, sprintf('Invalid MODX version. Minimal supported version is %s.', PKG_SUPPORTS_MODX));

    return false;
}

return true;
