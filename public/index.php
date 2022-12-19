<?php

declare(strict_types=1);

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildMetaStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\Store\UserStore;
use PHPCensor\Application;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var ConfigurationInterface $configuration */
/** @var DatabaseManager $databaseManager */
/** @var Session $session */
/** @var UserStore $userStore */
/** @var ProjectStore $projectStore */
/** @var ProjectGroupStore $projectGroupStore */
/** @var BuildStore $buildStore */
/** @var BuildErrorStore $buildErrorStore */
/** @var BuildMetaStore $buildMetaStore */
/** @var SecretStore $secretStore */
/** @var EnvironmentStore $environmentStore */
/** @var ContainerInterface $container */
require_once(\dirname(__DIR__) . '/bootstrap.php');

$request = Request::createFromGlobals();
$application = new Application($container, $configuration, $userStore, $request, $session);

$application->handleRequest()->send();
