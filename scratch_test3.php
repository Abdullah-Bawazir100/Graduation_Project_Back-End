<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Application\RecyclePin\UseCases\RestoreRecyclePinUseCase;
use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\RecyclePinRepository;
use App\Infrastructure\Persistence\Eloquent\Models\RecyclePinModel;

$r = RecyclePinModel::where('type', 'like', '%UserModel%')->latest()->first();
if ($r) {
    echo "Restoring ID: " . $r->id . "\n";
    $repo = new RecyclePinRepository(new RecyclePinModel());
    $useCase = new RestoreRecyclePinUseCase($repo);
    try {
        $useCase->execute($r->id);
        echo "Success\n";
    } catch (\Exception $e) {
        echo "Error Message: " . $e->getMessage() . "\n";
    }
} else {
    echo "No user recycle pin found";
}
