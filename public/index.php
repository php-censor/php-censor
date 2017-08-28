<?php

session_start();

require_once(dirname(__DIR__) . '/bootstrap.php');

$fc = new PHPCensor\Application($config, new b8\Http\Request());
print $fc->handleRequest();
