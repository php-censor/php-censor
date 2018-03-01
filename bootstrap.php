<?php

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', __DIR__ . '/');
}

if (!defined('SRC_DIR')) {
    define('SRC_DIR', ROOT_DIR . 'src/PHPCensor/');
}

if (!defined('PUBLIC_DIR')) {
    define('PUBLIC_DIR', ROOT_DIR . 'public/');
}

if (!defined('APP_DIR')) {
    define('APP_DIR', ROOT_DIR . 'app/');
}

if (!defined('BIN_DIR')) {
    define('BIN_DIR', ROOT_DIR . 'bin/');
}

if (!defined('RUNTIME_DIR')) {
    define('RUNTIME_DIR', ROOT_DIR . 'runtime/');
}

require_once(ROOT_DIR . 'vendor/autoload.php');

// Load configuration if present:
$conf = [];
$conf['b8']['app']['namespace']          = 'PHPCensor';
$conf['b8']['app']['default_controller'] = 'Home';
$conf['b8']['view']['path']              = SRC_DIR . 'View/';

$config = new b8\Config($conf);

$configFile = APP_DIR . 'config.yml';
if (file_exists($configFile)) {
    $config->loadYaml($configFile);
}

if (!defined('APP_URL') && !empty($config)) {
    define('APP_URL', $config->get('php-censor.url', '') . '/');
}

\PHPCensor\Helper\Lang::init($config);
