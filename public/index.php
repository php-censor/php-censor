<?php

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\StoreRegistry;
use PHPCensor\Application;
use PHPCensor\Http\Request;

\session_start();

/** @var $configuration ConfigurationInterface */
/** @var $databaseManager DatabaseManager */
/** @var $storeRegistry StoreRegistry */
require_once(\dirname(__DIR__) . '/bootstrap.php');

$application = new Application($configuration, $storeRegistry, new Request());

echo $application->handleRequest();
