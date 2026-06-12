<?php

use Spatie\LaravelPdf\Caching\DefaultPdfCache;
use Spatie\LaravelPdf\Encryption\DefaultPdfEncrypter;
use Spatie\LaravelPdf\Jobs\GeneratePdfJob;

$chromePath = env('LARAVEL_PDF_CHROME_PATH');

if (! $chromePath) {
    $windowsChromePath = 'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe';
    $windowsEdgePath = 'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe';

    $chromePath = file_exists($windowsChromePath)
        ? $windowsChromePath
        : (file_exists($windowsEdgePath) ? $windowsEdgePath : null);
}

return [
    'driver' => env('LARAVEL_PDF_DRIVER', 'browsershot'),

    'cache' => [
        'class' => DefaultPdfCache::class,
        'automatic' => env('LARAVEL_PDF_CACHE_AUTOMATIC', false),
        'store' => env('LARAVEL_PDF_CACHE_STORE'),
        'prefix' => 'laravel-pdf',
        'ttl' => env('LARAVEL_PDF_CACHE_TTL', 60 * 60 * 24),
    ],

    'browsershot' => [
        'node_binary' => env('LARAVEL_PDF_NODE_BINARY'),
        'npm_binary' => env('LARAVEL_PDF_NPM_BINARY'),
        'include_path' => env('LARAVEL_PDF_INCLUDE_PATH'),
        'chrome_path' => $chromePath,
        'node_modules_path' => env('LARAVEL_PDF_NODE_MODULES_PATH'),
        'bin_path' => env('LARAVEL_PDF_BIN_PATH'),
        'temp_path' => env('LARAVEL_PDF_TEMP_PATH'),
        'write_options_to_file' => env('LARAVEL_PDF_WRITE_OPTIONS_TO_FILE', true),
        'no_sandbox' => env('LARAVEL_PDF_NO_SANDBOX', true),
    ],

    'cloudflare' => [
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
    ],

    'gotenberg' => [
        'url' => env('GOTENBERG_URL', 'http://localhost:3000'),
        'username' => env('GOTENBERG_USERNAME'),
        'password' => env('GOTENBERG_PASSWORD'),
    ],

    'dompdf' => [
        'is_remote_enabled' => env('LARAVEL_PDF_DOMPDF_REMOTE_ENABLED', false),
        'chroot' => env('LARAVEL_PDF_DOMPDF_CHROOT'),
    ],

    'weasyprint' => [
        'binary' => env('LARAVEL_PDF_WEASYPRINT_BINARY', 'weasyprint'),
        'timeout' => 10,
    ],

    'chrome' => [
        'chrome_binary' => env('LARAVEL_PDF_CHROME_BINARY'),
        'no_sandbox' => env('LARAVEL_PDF_CHROME_NO_SANDBOX', true),
        'startup_timeout' => env('LARAVEL_PDF_CHROME_STARTUP_TIMEOUT', 30),
        'timeout' => env('LARAVEL_PDF_CHROME_TIMEOUT', 30000),
        'operation_timeout' => env('LARAVEL_PDF_CHROME_OPERATION_TIMEOUT', 5000),
        'user_data_dir' => env('LARAVEL_PDF_CHROME_USER_DATA_DIR'),
        'custom_flags' => [],
        'env_variables' => [],
    ],

    'job' => GeneratePdfJob::class,

    'encrypter' => DefaultPdfEncrypter::class,
];
