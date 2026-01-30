<?php

// Vercel Serverless Entry Point for Laravel

// Set the base path for Laravel
define('LARAVEL_START', microtime(true));

// Pastikan direktori /tmp tersedia sebelum Laravel bootstrap
$tmpDirs = ['/tmp/views', '/tmp/cache', '/tmp/sessions', '/tmp/logs'];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Set environment variables SEBELUM Laravel load
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';
putenv('VIEW_COMPILED_PATH=/tmp/views');

try {
    // Register the Composer autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // Bootstrap Laravel dan handle request
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
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
    ]);
}
