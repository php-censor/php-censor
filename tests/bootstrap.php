<?php

error_reporting(E_ALL & ~E_DEPRECATED);

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__) . '/');
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

if (!defined('APP_URL')) {
    define('APP_URL', 'http://php-censor.local/');
}

require_once(ROOT_DIR . 'vendor/autoload.php');
