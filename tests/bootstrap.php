<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2013, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         http://www.phptesting.org/
 */

use PHPCI\Logging\LoggerConfig;

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (!defined('PHPCI_DIR')) {
    define('PHPCI_DIR', ROOT_DIR . 'src' . DIRECTORY_SEPARATOR . 'PHPCI' . DIRECTORY_SEPARATOR);
}

if (!defined('PHPCI_PUBLIC_DIR')) {
    define('PHPCI_PUBLIC_DIR', ROOT_DIR . 'public' . DIRECTORY_SEPARATOR);
}

if (!defined('PHPCI_APP_DIR')) {
    define('PHPCI_APP_DIR', ROOT_DIR . 'app' . DIRECTORY_SEPARATOR);
}

if (!defined('PHPCI_BIN_DIR')) {
    define('PHPCI_BIN_DIR', ROOT_DIR . 'bin' . DIRECTORY_SEPARATOR);
}

if (!defined('PHPCI_RUNTIME_DIR')) {
    define('PHPCI_RUNTIME_DIR', ROOT_DIR . 'runtime' . DIRECTORY_SEPARATOR);
}

if (!defined('PHPCI_BUILDS_DIR')) {
    define('PHPCI_BUILDS_DIR', ROOT_DIR . 'runtime' . DIRECTORY_SEPARATOR . 'builds' . DIRECTORY_SEPARATOR);
}

if (!defined('IS_WIN')) {
    define('IS_WIN', ((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? true : false));
}

require_once(ROOT_DIR . 'vendor/autoload.php');

\PHPCI\ErrorHandler::register();

if (defined('PHPCI_IS_CONSOLE') && PHPCI_IS_CONSOLE) {
    $loggerConfig = LoggerConfig::newFromFile(PHPCI_APP_DIR . "loggerconfig.php");
}

// Load configuration if present:
$conf = [];
$conf['b8']['app']['namespace']          = 'PHPCI';
$conf['b8']['app']['default_controller'] = 'Home';
$conf['b8']['view']['path']              = PHPCI_DIR . 'View' . DIRECTORY_SEPARATOR;

$config = new b8\Config($conf);

$configFile = PHPCI_APP_DIR . 'config.yml';
if (file_exists($configFile)) {
    $config->loadYaml($configFile);
}

if (!defined('PHPCI_URL') && !empty($config)) {
    define('PHPCI_URL', $config->get('phpci.url', '') . '/');
}

if (!defined('PHPCI_IS_CONSOLE')) {
    define('PHPCI_IS_CONSOLE', false);
}

\PHPCI\Helper\Lang::init($config);
