<?php

declare(strict_types=1);

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\Store\UserStore;
use PHPCensor\StoreRegistry;
use PHPCensor\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var ConfigurationInterface $configuration */
/** @var DatabaseManager $databaseManager */
/** @var StoreRegistry $storeRegistry */
/** @var Session $session */
/** @var UserStore $userStore */
/** @var ProjectStore $projectStore */
/** @var ProjectGroupStore $projectGroupStore */
/** @var BuildStore $buildStore */
/** @var BuildErrorStore $buildErrorStore */
/** @var SecretStore $secretStore */
/** @var EnvironmentStore $environmentStore */
require_once(\dirname(__DIR__) . '/bootstrap.php');

$request = Request::createFromGlobals();
$application = new Application($configuration, $storeRegistry, $userStore, $request, $session);

$application->handleRequest()->send();
