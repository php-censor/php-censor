<?php

declare(strict_types=1);

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\Lang;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\SecretStore;
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

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load(APP_DIR . 'services.yaml');

$container->compile();

/** @var ConfigurationInterface $configuration */
$configuration = $container->get(ConfigurationInterface::class);

/** @var DatabaseManager $databaseManager */
$databaseManager = $container->get(DatabaseManager::class);

/** @var StoreRegistry $storeRegistry */
$storeRegistry = $container->get(StoreRegistry::class);

/** @var SessionInterface $session */
$session  = $container->get(SessionInterface::class);

$session->start();

/** @var UserStore $userStore */
$userStore = $container->get(UserStore::class);
/** @var ProjectStore $projectStore */
$projectStore = $container->get(ProjectStore::class);
/** @var ProjectGroupStore $projectGroupStore */
$projectGroupStore = $container->get(ProjectGroupStore::class);
/** @var BuildStore $buildStore */
$buildStore = $container->get(BuildStore::class);
/** @var BuildErrorStore $buildErrorStore */
$buildErrorStore = $container->get(BuildErrorStore::class);
/** @var SecretStore $secretStore */
$secretStore = $container->get(SecretStore::class);
/** @var EnvironmentStore $environmentStore */
$environmentStore = $container->get(EnvironmentStore::class);

\define('APP_URL', $configuration->get('php-censor.url', '') . '/');
\define('REALTIME_UI', $configuration->get('php-censor.realtime_ui', true));

Lang::init($configuration, $userStore, null, $session->get('php-censor-user-id'));
