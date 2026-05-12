<?php

namespace App\Domain\FileStatus\Repositories;

use App\Domain\FileStatus\Entities\FileStatus;

interface FileStatusRepositoryInterface
{
    public function create(FileStatus $fileStatus);
    //public function update(FileStatus $fileStatus);
}
