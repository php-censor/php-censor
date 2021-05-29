<?php

use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\StoreRegistry;

\session_start();

/** @var $configuration ConfigurationInterface */
/** @var $databaseManager DatabaseManager */
/** @var $storeRegistry StoreRegistry */
require_once(dirname(__DIR__) . '/bootstrap.php');

$fc = new PHPCensor\Application($configuration, $databaseManager, $storeRegistry, new PHPCensor\Http\Request());
print $fc->handleRequest();
