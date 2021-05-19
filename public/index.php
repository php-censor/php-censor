<?php

use PHPCensor\ConfigurationInterface;

\session_start();

/** @var $configuration ConfigurationInterface */
require_once(dirname(__DIR__) . '/bootstrap.php');

$fc = new PHPCensor\Application($configuration, new PHPCensor\Http\Request());
print $fc->handleRequest();
