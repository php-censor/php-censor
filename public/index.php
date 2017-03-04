<?php

session_set_cookie_params(43200); // Set session cookie to last 12 hours.
session_start();

require_once(dirname(__DIR__) . '/bootstrap.php');

$fc = new PHPCensor\Application($config, new b8\Http\Request());
print $fc->handleRequest();
