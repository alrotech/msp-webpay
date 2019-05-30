<?php

set_time_limit(0);
error_reporting(E_ALL | E_STRICT); ini_set('display_errors',true);

ini_set('date.timezone', 'Europe/Minsk');

define('PKG_NAME', 'mspWebPay');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '2.0.0');
define('PKG_RELEASE', 'pl');

require_once __DIR__ . '/vendor/modx/revolution/core/xpdo/xpdo.class.php';

/* instantiate xpdo instance */
$xpdo = new xPDO('mysql:host=localhost;dbname=modx;charset=utf8', 'root', '',
    [xPDO::OPT_TABLE_PREFIX => 'modx_', xPDO::OPT_CACHE_PATH => __DIR__ . '/../../../core/cache/'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]
);
$cacheManager= $xpdo->getCacheManager();
$xpdo->setLogLevel(xPDO::LOG_LEVEL_INFO);
$xpdo->setLogTarget();

$root = dirname(__DIR__) . '/';
$sources = [
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'docs' => $root . 'docs/',
    'resolvers' => $root . '_build/resolvers/',
    'validators' => $root . '_build/validators/',
    'implants' => $root . '_build/implants/',
    'plugins' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'assets' => [
        'components/mspwebpay/'
    ],
    'core' => [
        'components/mspwebpay/',
        'components/minishop2/lexicon/en/msp.webpay.inc.php',
        'components/minishop2/lexicon/ru/msp.webpay.inc.php'
    ],
];

$signature = implode('-', [PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE]);

$release = false;
if (!empty($argv) && $argc > 1) {
    $release = $argv[1];
}

$directory = $release === 'release' ? $root . '_packages/' : __DIR__ . '/../../../core/packages/';
$filename = $directory . $signature . '.transport.zip';

/* remove the package if it's already been made */
if (file_exists($filename)) {
    unlink($filename);
}
if (file_exists($directory . $signature) && is_dir($directory . $signature)) {
    $cacheManager = $xpdo->getCacheManager();
    if ($cacheManager) {
        $cacheManager->deleteTree($directory . $signature, true, false, []);
    }
}

$xpdo->loadClass('transport.xPDOTransport', XPDO_CORE_PATH, true, true);

$package = new xPDOTransport($xpdo, $signature, $directory);
$xpdo->log(xPDO::LOG_LEVEL_INFO, 'Transport package is created.');

$xpdo->setPackage('modx', __DIR__ . '/vendor/modx/revolution/core/model/');
$xpdo->loadClass('modAccess');
$xpdo->loadClass('modAccessibleObject');
$xpdo->loadClass('modAccessibleSimpleObject');
$xpdo->loadClass('modPrincipal');

$namespace = $xpdo->newObject('modNamespace');
$namespace->fromArray([
    'id' => PKG_NAME_LOWER,
    'name' => PKG_NAME_LOWER,
    'path' => '{core_path}components/' . PKG_NAME_LOWER . '/',
]);

$package->put($namespace, [
//    'vehicle_class' => EncryptedVehicle::class,
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::NATIVE_KEY => PKG_NAME_LOWER,
    'namespace' => PKG_NAME_LOWER
]);

$package->pack();

exit();

/* load system settings */
//if (defined('BUILD_SETTING_UPDATE')) {
//    $settings = include $sources['data'].'transport.settings.php';
//    if (!is_array($settings)) {
//        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
//    } else {
//        $attributes= array(
//            xPDOTransport::UNIQUE_KEY => 'key',
//            xPDOTransport::PRESERVE_KEYS => true,
//            xPDOTransport::UPDATE_OBJECT => BUILD_SETTING_UPDATE,
//        );
//        foreach ($settings as $setting) {
//            $vehicle = $builder->createVehicle($setting,$attributes);
//            $builder->putVehicle($vehicle);
//        }
//        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.');
//    }
//    unset($settings,$setting,$attributes);
//}

/* @var msPayment $payment */
$payment= $modx->newObject('msPayment');
$payment->fromArray(
    array(
        'name' => 'WebPay',
        'active' => 0,
        'class' => 'WebPay'
    )
);

/* create payment vehicle */
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => false
);
$vehicle = $builder->createVehicle($payment,$attributes);

$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to payment...');
foreach($sources['source_assets'] as $file) {
    $dir = dirname($file) . '/';
    $vehicle->resolve(
        'file',
        array(
            'source' => $root . 'assets/' . $file,
            'target' => "return MODX_ASSETS_PATH . '{$dir}';",
        )
    );
}
foreach($sources['source_core'] as $file) {
    $dir = dirname($file) . '/';
    $vehicle->resolve(
        'file',
        array(
            'source' => $root . 'core/'. $file,
            'target' => "return MODX_CORE_PATH . '{$dir}';"
        )
    );
}
unset($file, $attributes);

$resolvers = array('settings');
foreach ($resolvers as $resolver) {
    if ($vehicle->resolve('php', array('source' => $sources['resolvers'] . 'resolve.'.$resolver.'.php'))) {
        $modx->log(modX::LOG_LEVEL_INFO,'Added resolver "'.$resolver.'" to category.');
    } else {
        $modx->log(modX::LOG_LEVEL_INFO,'Could not add resolver "'.$resolver.'" to category.');
    }
}

flush();
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(
    array(
        'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
        'license' => file_get_contents($sources['docs'] . 'license.txt'),
        'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
        'setup-options' => array(
            'source' => $sources['build'] . 'setup.options.php'
        )
    ));
$modx->log(modX::LOG_LEVEL_INFO,'Added package attributes and setup options.');

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$modx->log(modX::LOG_LEVEL_INFO, 'Package Built.');
