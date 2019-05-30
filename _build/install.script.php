<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

define('MODX_API_MODE', true);

require_once __DIR__ . '/../../../index.php';

$modx->initialize('mgr');

$modx->setLogLevel(xPDO::LOG_LEVEL_ERROR);
$modx->setLogTarget();

$modx->runProcessor('workspace/packages/scanlocal');
$answer = $modx->runProcessor('workspace/packages/install',
    ['signature' => 'mspwebpay-2.0.0-pl']
);

$response = $answer->getResponse();

echo $response['message'], PHP_EOL;

//$sid = $modx->getObject('modSystemSetting', 'ms2_payment_bepaid_store_id');
//$sid->set('value', $id);
//$sid->save();
//
//$ssc = $modx->getObject('modSystemSetting', 'ms2_payment_bepaid_secret_key');
//$ssc->set('value', $sc);
//$ssc->save();

$modx->getCacheManager()->refresh(['system_settings' => []]);
$modx->reloadConfig();
