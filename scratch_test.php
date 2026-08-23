<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$uc = app(\App\Application\File\UseCases\ListFilesUseCase::class);
// Use Auth ID 1 for testing
$res = $uc->execute(null, 1);
echo json_encode($res, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
