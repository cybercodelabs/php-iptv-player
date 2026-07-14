<?php

declare(strict_types=1);

/**
 * Bootstrap de la aplicación: autoload, entorno y servicios base.
 */

use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

Config::load($root);

if (Config::get('APP_DEBUG', 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

date_default_timezone_set(Config::get('APP_TIMEZONE', 'UTC'));

Session::start();
