<?php

use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;

\session_start();

/** @var $configuration ConfigurationInterface */
/** @var $databaseManager DatabaseManager */
require_once(dirname(__DIR__) . '/bootstrap.php');

$fc = new PHPCensor\Application($configuration, new PHPCensor\Http\Request());
print $fc->handleRequest();
