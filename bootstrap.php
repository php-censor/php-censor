<?php

declare(strict_types=1);

use PHPCensor\Configuration;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\Lang;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

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
$session         = new Session();

$session->start();

/** @var UserStore $userStore */
$userStore = $storeRegistry->get('User');

\define('APP_URL', $configuration->get('php-censor.url', '') . '/');
\define('REALTIME_UI', $configuration->get('php-censor.realtime_ui', true));

Lang::init($configuration, $userStore, null, $session->get('php-censor-user-id'));
