<?php

/**
 * Vercel Serverless Entry Point for Laravel 12
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Set the base path for Laravel
define('LARAVEL_START', microtime(true));

// Pastikan direktori /tmp tersedia untuk Vercel serverless
$tmpDirs = ['/tmp/views', '/tmp/cache', '/tmp/sessions', '/tmp/logs'];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Set environment variables untuk Vercel
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';
putenv('VIEW_COMPILED_PATH=/tmp/views');

try {
    // Register the Composer autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // Bootstrap Laravel 12 dan handle request (cara baru)
    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Laravel 12 menggunakan handleRequest() method
    $app->handleRequest(Request::capture());
    
} catch (Throwable $e) {
    // Tampilkan error untuk debugging
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ], JSON_PRETTY_PRINT);
}
