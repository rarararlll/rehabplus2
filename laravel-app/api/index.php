<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Debug: Test database connection
try {
    $pdo = DB::connection()->getPdo();
    error_log("DB Connection OK: " . $pdo->query("SELECT VERSION()")->fetchColumn());
} catch (\Throwable $e) {
    error_log("DB Connection FAILED: " . $e->getMessage());
    error_log("DATABASE_URL: " . ($_ENV['DATABASE_URL'] ?? 'NOT SET'));
}

$app->handleRequest(Request::capture());
