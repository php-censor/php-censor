<?php

use PHPCensor\Configuration;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\Lang;
use PHPCensor\StoreRegistry;

const ROOT_DIR    = __DIR__ . '/';
const SRC_DIR     = ROOT_DIR . 'src/';
const PUBLIC_DIR  = ROOT_DIR . 'public/';
const APP_DIR     = ROOT_DIR . 'app/';
const RUNTIME_DIR = ROOT_DIR . 'runtime/';

require_once(ROOT_DIR . 'vendor/autoload.php');

$configurationPath = APP_DIR . 'config.yml';
$configuration   = new Configuration($configurationPath);
$databaseManager = new DatabaseManager($configuration);
$storeRegistry   = new StoreRegistry($databaseManager);

\define('APP_URL', $configuration->get('php-censor.url', '') . '/');
\define('REALTIME_UI', true);

Lang::init($configuration, $storeRegistry);
