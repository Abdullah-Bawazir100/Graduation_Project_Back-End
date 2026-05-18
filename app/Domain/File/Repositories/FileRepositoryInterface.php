<?php

namespace App\Domain\File\Repositories;

use App\Domain\File\Entities\File;

interface FileRepositoryInterface
{
    public function create(File $file): File;
    public function getAll();
    
}
