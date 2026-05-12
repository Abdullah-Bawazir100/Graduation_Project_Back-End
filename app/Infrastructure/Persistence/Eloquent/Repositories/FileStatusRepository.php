<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface as RepositoriesFileStatusRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\FileStatusModel;

class FileStatusRepository implements RepositoriesFileStatusRepositoryInterface
{
    public function create(FileStatus $fileStatus)
    {
        $fileStatusModel = FileStatusModel::create([
            'status_name' => $fileStatus->statusName,
            'status_description' => $fileStatus->statusDescription,
        ]);

        return new FileStatus(
            $fileStatusModel->id,
            $fileStatusModel->status_name,
            $fileStatusModel->status_description
        );
    }
}
