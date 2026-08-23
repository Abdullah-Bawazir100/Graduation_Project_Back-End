<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Api\FileController;
use App\Application\File\UseCases\ListFilesUseCase;

// Mock Auth bypass for testing without loading UserModel directly
Auth::shouldReceive('id')->andReturn(1);
Auth::shouldReceive('user')->andReturn(null);

$controller = app(FileController::class);
$useCase = app(ListFilesUseCase::class);

$request = Request::create('/api/files', 'GET');
$response = $controller->index($request, $useCase);

echo $response->toResponse($request)->getContent();
