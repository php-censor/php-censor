<?php

declare(strict_types=1);

\error_reporting(E_ALL & ~E_DEPRECATED);

\define('ROOT_DIR', \dirname(__DIR__) . '/');

const SRC_DIR     = ROOT_DIR . 'src/';
const PUBLIC_DIR  = ROOT_DIR . 'public/';
const APP_DIR     = ROOT_DIR . 'app/';
const RUNTIME_DIR = ROOT_DIR . 'runtime/';
const APP_URL     = ROOT_DIR . 'https://php-censor.localhost/';

require_once(ROOT_DIR . 'vendor/autoload.php');
