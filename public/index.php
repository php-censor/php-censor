<?php

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once(dirname(__DIR__) . '/bootstrap.php');

$fc = new PHPCensor\Application($config, new PHPCensor\Http\Request());
print $fc->handleRequest();
