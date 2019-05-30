<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

/**
 * @param modX $modx
 * @param $files
 */
function loadExtraJs($modx, $files) {
    $ms2connector = $modx->getOption('minishop2.assets_url', null, $modx->getOption('assets_url') . 'components/minishop2/') . 'connector.php';
    $ownConnector = $modx->getOption('assets_url') . 'components/mspbepaid/connector.php';

    $modx->controller->addLexiconTopic('minishop2:default');
    $modx->controller->addLexiconTopic('core:propertyset');
    $modx->controller->addJavascript(MODX_ASSETS_URL . 'components/mspbepaid/js/mgr/bepaid.js');
    $modx->controller->addHtml('<script>BePaidPayment.ms2Connector = "' . $ms2connector . '";</script>');
    $modx->controller->addHtml('<script>BePaidPayment.ownConnector = "' . $ownConnector . '";</script>');

    foreach ($files as $file) {
        $modx->controller->addLastJavascript(MODX_ASSETS_URL . 'components/mspbepaid/js/mgr/' . $file);
    }
}

switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':

        switch ($_GET['a']) {
            case 'system/settings':
                loadExtraJs($modx, [
                    'language.combo.js',
                    'country.combo.js',
                    'status.combo.js',
                    'resource.combo.js',
                    // for now I have troubles with integrate superboxselect type into system settings grid
                    // so special types not used
                    // 'readonly.combo.js',
                    // 'visible.combo.js'
                ]); break;
            case 'mgr/settings':
                loadExtraJs($modx, [
                    'language.combo.js',
                    'country.combo.js',
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
