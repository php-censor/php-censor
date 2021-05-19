<?php

use PHPCensor\Configuration;
use PHPCensor\Database;
use PHPCensor\Helper\Lang;

const ROOT_DIR    = __DIR__ . '/';
const SRC_DIR     = ROOT_DIR . 'src/';
const PUBLIC_DIR  = ROOT_DIR . 'public/';
const APP_DIR     = ROOT_DIR . 'app/';
const RUNTIME_DIR = ROOT_DIR . 'runtime/';

require_once(ROOT_DIR . 'vendor/autoload.php');

$configurationPath = APP_DIR . 'config.yml';
$configuration = new Configuration($configurationPath);

Database::$configuration = $configuration;

\define('APP_URL', $configuration->get('php-censor.url', '') . '/');

Lang::init($configuration);
