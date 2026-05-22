<?php

namespace App\Domain\FileMovement\Repositories;

use App\Domain\FileMovement\Entities\FileMovement;

interface FileMovementRepositoryInterface
{
    public function create(FileMovement $fileMovement): FileMovement;
}
