<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

set_time_limit(0);
error_reporting(E_ALL | E_STRICT); ini_set('display_errors',true);

ini_set('date.timezone', 'Europe/Minsk');

define('PKG_NAME', 'mspWebPay');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '2.1.0');
define('PKG_RELEASE', 'pl');
define('PKG_SUPPORTS_PHP', '7.1');
define('PKG_SUPPORTS_MODX', '2.7');
define('PKG_SUPPORTS_MS2', '2.4');

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

$signature = implode('-', [PKG_NAME, PKG_VERSION, PKG_RELEASE]);

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
$xpdo->loadClass('transport.xPDOVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOFileVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOScriptVehicle', XPDO_CORE_PATH, true, true);

require_once __DIR__ . '/helpers/ArrayXMLConverter.php';
require_once __DIR__ . '/implants/encryptedvehicle.class.php';

$credentials = file_get_contents(__DIR__ . '/../.encryption');
[$username, $key] = explode(':', $credentials);

if (empty($username) || empty($key)) {
    $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Credentials not found');
    exit;
}

$params = [
    'api_key' => $key,
    'username' => $username,
    'http_host' => 'anysite.docker',
    'package' => PKG_NAME,
    'version' => PKG_VERSION . '-' . PKG_RELEASE,
    'vehicle_version' => '2.0.0'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://modstore.pro/extras/package/encode');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
curl_setopt($ch, CURLOPT_POSTFIELDS, ArrayXMLConverter::toXML($params,'request'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = trim(curl_exec($ch));
curl_close($ch);

$answer = ArrayXMLConverter::toArray($result);

if (isset($answer['message'])) {
    $xpdo->log(xPDO::LOG_LEVEL_ERROR, $answer['message']);
    echo $answer['message'];
    exit;
}

define('PKG_ENCODE_KEY', $answer['key']);

$package = new xPDOTransport($xpdo, $signature, $directory);

$xpdo->setPackage('modx', __DIR__ . '/vendor/modx/revolution/core/model/');
$xpdo->loadClass(modAccess::class);
$xpdo->loadClass(modAccessibleObject::class);
$xpdo->loadClass(modAccessibleSimpleObject::class);
$xpdo->loadClass(modPrincipal::class);
$xpdo->loadClass(modElement::class);
$xpdo->loadClass(modScript::class);

$package->put([
    'source' => $sources['implants'] . 'encryptedvehicle.class.php',
    'target' => "return MODX_CORE_PATH . 'components/" . PKG_NAME_LOWER . "/';",
], ['vehicle_class' => xPDOFileVehicle::class]);

$package->put([
    'source' => $sources['resolvers'] . 'resolve.encryption.php'
], ['vehicle_class' => xPDOScriptVehicle::class]);

$namespace = $xpdo->newObject(modNamespace::class);
$namespace->fromArray([
    'id' => PKG_NAME_LOWER,
    'name' => PKG_NAME_LOWER,
    'path' => '{core_path}components/' . PKG_NAME_LOWER . '/',
]);

$package->put($namespace, [
    'vehicle_class' => EncryptedVehicle::class,
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::NATIVE_KEY => PKG_NAME_LOWER,
    'namespace' => PKG_NAME_LOWER
]);

$settings = include $sources['data'] . 'transport.settings.php';
foreach ($settings as $setting) {
    $package->put($setting, [
        'vehicle_class' => EncryptedVehicle::class,
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
        'class' => modSystemSetting::class,
        'namespace' => PKG_NAME_LOWER
    ]);
}

$category = $xpdo->newObject(modCategory::class);
$category->fromArray(['id' => 1, 'category' => PKG_NAME, 'parent' => 0]);

//$plugins = include $sources['data'] . 'transport.plugins.php';
//if (is_array($plugins)) {
//    $category->addMany($plugins, 'Plugins');
//}

$validators = [];
array_push($validators,
    ['type' => 'php', 'source' => $sources['validators'] . 'validate.phpversion.php'],
    ['type' => 'php', 'source' => $sources['validators'] . 'validate.modxversion.php'],
    ['type' => 'php', 'source' => $sources['validators'] . 'validate.bcmath.php']
);

$resolvers = [];
foreach ($sources['assets'] as $file) {
    $directory = dirname($file);
    $resolvers[] = [
        'type' => 'file',
        'source' => $root . 'assets/' . $file,
        'target' => "return MODX_ASSETS_PATH . '$directory/';",
    ];
}
foreach ($sources['core'] as $file) {
    $directory = dirname($file);
    $resolvers[] = [
        'type' => 'file',
        'source' => $root . 'core/' . $file,
        'target' => "return MODX_CORE_PATH . '$directory/';"
    ];
}

$resolvers[] = ['type' => 'php', 'source' => $sources['resolvers'] . 'resolve.service.php'];


// todo: remove category and replace by file resolvers (in future probably it will be possible to encrypt files too);
$package->put($category, [
    'vehicle_class' => EncryptedVehicle::class,
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
    xPDOTransport::RELATED_OBJECTS => false,
    xPDOTransport::NATIVE_KEY => true,
    'package' => 'modx',
    'resolve' => $resolvers,
    'validate' => $validators
]);

$package->setAttribute('changelog', file_get_contents($sources['docs'] . 'changelog.txt'));
$package->setAttribute('license', file_get_contents($sources['docs'] . 'license.txt'));
$package->setAttribute('readme', file_get_contents($sources['docs'] . 'readme.txt'));
$package->setAttribute('requires', [
    'php' => '>=' . PKG_SUPPORTS_PHP,
    'modx' => '>=' . PKG_SUPPORTS_MODX,
    'miniShop2' => '>=' . PKG_SUPPORTS_MS2,
    'msPaymentProps' => '*'
]);

if ($package->pack()) {
    $xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package built');
}
