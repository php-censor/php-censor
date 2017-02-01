<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2013, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         http://www.phptesting.org/
 */

use PHPCensor\Logging\LoggerConfig;

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (!defined('SRC_DIR')) {
    define('SRC_DIR', ROOT_DIR . 'src' . DIRECTORY_SEPARATOR . 'PHPCensor' . DIRECTORY_SEPARATOR);
}

if (!defined('PUBLIC_DIR')) {
    define('PUBLIC_DIR', ROOT_DIR . 'public' . DIRECTORY_SEPARATOR);
}

if (!defined('APP_DIR')) {
    define('APP_DIR', ROOT_DIR . 'app' . DIRECTORY_SEPARATOR);
}

if (!defined('BIN_DIR')) {
    define('BIN_DIR', ROOT_DIR . 'bin' . DIRECTORY_SEPARATOR);
}

if (!defined('RUNTIME_DIR')) {
    define('RUNTIME_DIR', ROOT_DIR . 'runtime' . DIRECTORY_SEPARATOR);
}

if (!defined('IS_WIN')) {
    define('IS_WIN', ((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? true : false));
}

require_once(ROOT_DIR . 'vendor/autoload.php');

// Load configuration if present:
$conf = [];
$conf['b8']['app']['namespace']          = 'PHPCensor';
$conf['b8']['app']['default_controller'] = 'Home';
$conf['b8']['view']['path']              = SRC_DIR . 'View' . DIRECTORY_SEPARATOR;

$config = new b8\Config($conf);

$configFile = APP_DIR . 'config.yml';
if (file_exists($configFile)) {
    $config->loadYaml($configFile);
}

if (!defined('APP_URL') && !empty($config)) {
    define('APP_URL', $config->get('php-censor.url', '') . '/');
}

\PHPCensor\Helper\Lang::init($config, 'en');
