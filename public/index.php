<?php

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\StoreRegistry;
use PHPCensor\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var $configuration ConfigurationInterface */
/** @var $databaseManager DatabaseManager */
/** @var $storeRegistry StoreRegistry */
/** @var $session Session */
require_once(\dirname(__DIR__) . '/bootstrap.php');

$request = Request::createFromGlobals();
$application = new Application($configuration, $storeRegistry, $request, $session);

$application->handleRequest()->send();
