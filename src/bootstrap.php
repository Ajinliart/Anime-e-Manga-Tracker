<?php
declare(strict_types=1);

require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/repositories/UserRepository.php';
require __DIR__ . '/repositories/TitleRepository.php';
require __DIR__ . '/repositories/ListRepository.php';
require __DIR__ . '/catalog_query.php';

if (config('debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

date_default_timezone_set('Europe/Rome');

start_session();
