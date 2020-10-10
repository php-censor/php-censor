<?php
// Define the root directory
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', __DIR__ . '/');
}

// Define the src directory
if (!defined('SRC_DIR')) {
    define('SRC_DIR', ROOT_DIR . 'src/');
}

// Define the public directory
if (!defined('PUBLIC_DIR')) {
    define('PUBLIC_DIR', ROOT_DIR . 'public/');
}

// Define the application directory
if (!defined('APP_DIR')) {
    define('APP_DIR', ROOT_DIR . 'app/');
}

// Define the BIN directory
if (!defined('BIN_DIR')) {
    define('BIN_DIR', ROOT_DIR . 'bin/');
}

// Define the runtime directory
if (!defined('RUNTIME_DIR')) {
    define('RUNTIME_DIR', ROOT_DIR . 'runtime/');
}

require_once(ROOT_DIR . 'vendor/autoload.php');

use PHPCensor\Config;
use PHPCensor\Helper\Lang;

$config = new Config();

$configFile = APP_DIR . 'config.yml';
if (file_exists($configFile)) {
    $config->loadYaml($configFile);
}

Lang::init($config);
