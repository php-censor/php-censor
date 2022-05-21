<?php

declare(strict_types=1);

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\Lang;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

const ROOT_DIR    = __DIR__ . '/';
const SRC_DIR     = ROOT_DIR . 'src/';
const PUBLIC_DIR  = ROOT_DIR . 'public/';
const APP_DIR     = ROOT_DIR . 'app/';
const RUNTIME_DIR = ROOT_DIR . 'runtime/';

require_once(ROOT_DIR . 'vendor/autoload.php');

$containerBuilder = new ContainerBuilder();
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load(APP_DIR . 'services.yaml');

$containerBuilder->compile();

/** @var ConfigurationInterface $configuration */
$configuration = $containerBuilder->get(ConfigurationInterface::class);

/** @var DatabaseManager $databaseManager */
$databaseManager = $containerBuilder->get(DatabaseManager::class);

/** @var StoreRegistry $storeRegistry */
$storeRegistry = $containerBuilder->get(StoreRegistry::class);

/** @var SessionInterface $session */
$session  = $containerBuilder->get(SessionInterface::class);

$session->start();

/** @var UserStore $userStore */
$userStore = $storeRegistry->get('User');

\define('APP_URL', $configuration->get('php-censor.url', '') . '/');
\define('REALTIME_UI', $configuration->get('php-censor.realtime_ui', true));

Lang::init($configuration, $userStore, null, $session->get('php-censor-user-id'));
