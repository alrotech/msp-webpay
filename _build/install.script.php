<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

define('MODX_API_MODE', true);

require_once __DIR__ . '/../../../index.php';

$modx->initialize('mgr');

$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget();

$modx->runProcessor('workspace/packages/scanlocal');
$answer = $modx->runProcessor('workspace/packages/install',
    ['signature' => 'mspWebPay-2.2.1-stable']
);

$response = $answer->getResponse();

echo $response['message'], PHP_EOL;

$modx->getCacheManager()->refresh(['system_settings' => []]);
$modx->reloadConfig();
