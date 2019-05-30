<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

function loadExtraJs($modx, $files) {
    $ms2connector = $modx->getOption('minishop2.assets_url', null, $modx->getOption('assets_url') . 'components/minishop2/') . 'connector.php';
    $ownConnector = $modx->getOption('assets_url') . 'components/mspwebpay/connector.php';

    $modx->controller->addLexiconTopic('minishop2:default');
    $modx->controller->addLexiconTopic('core:propertyset');
    $modx->controller->addJavascript(MODX_ASSETS_URL . 'components/mspwebpay/js/mgr/webpay.js');
    $modx->controller->addHtml('<script>WebPayPayment.ms2Connector = "' . $ms2connector . '";</script>');
    $modx->controller->addHtml('<script>WebPayPayment.ownConnector = "' . $ownConnector . '";</script>');

    foreach ($files as $file) {
        $modx->controller->addLastJavascript(MODX_ASSETS_URL . 'components/mspwebpay/js/mgr/' . $file);
    }
}

switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':

        switch ($_GET['a']) {
            case 'system/settings':
                loadExtraJs($modx, [
                    'language.combo.js',
                    'status.combo.js',
                    'resource.combo.js'
                ]); break;
            case 'mgr/settings':
                loadExtraJs($modx, [
                    'language.combo.js',
                    'status.combo.js',
                    'resource.combo.js',
                    'settings.combo.js',
                    'property.window.js',
                    'properties.grid.js',
                    'properties.tab.js'
                ]);
                break;
        }
        break;
}
