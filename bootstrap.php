<?php

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', __DIR__ . '/');
}

if (!defined('SRC_DIR')) {
    define('SRC_DIR', ROOT_DIR . 'src/');
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

use PHPCensor\Config;
use PHPCensor\Helper\Lang;

$config = new Config();

$configFile = APP_DIR . 'config.yml';
if (file_exists($configFile)) {
    $config->loadYaml($configFile);
}

Lang::init($config);
