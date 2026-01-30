<?php

/**
 * Vercel Serverless Entry Point for Laravel 12
 */

// 1. Setup /tmp directories FIRST
foreach (['/tmp/views', '/tmp/cache', '/tmp/sessions', '/tmp/logs'] as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
}

// 2. Set environment
$_ENV['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';
putenv('VIEW_COMPILED_PATH=/tmp/views');

define('LARAVEL_START', microtime(true));

try {
    // 3. Autoload
    require __DIR__ . '/../vendor/autoload.php';

    // 4. Bootstrap Laravel 12
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. Override view compiled path setelah app dibuat
    $app->useStoragePath('/tmp');
    
    // 6. Manually boot the application jika belum
    if (!$app->hasBeenBootstrapped()) {
        $app->bootstrapWith([
            \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
            \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
            \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
            \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
            \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
            \Illuminate\Foundation\Bootstrap\BootProviders::class,
        ]);
    }

    // 7. Override config setelah bootstrap
    config(['view.compiled' => '/tmp/views']);

    // 8. Handle request menggunakan kernel
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $request = \Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
    ], JSON_PRETTY_PRINT);
}
