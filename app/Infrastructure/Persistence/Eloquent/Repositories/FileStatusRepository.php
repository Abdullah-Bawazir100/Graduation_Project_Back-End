<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface as RepositoriesFileStatusRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\FileStatusModel;
use Override;

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

    public function update(FileStatus $fileStatus): ?FileStatus
    {
        $fileStatusModel = FileStatusModel::find($fileStatus->id);

        if (!$fileStatusModel) {
            return null;
        }

        $fileStatusModel->status_name = $fileStatus->statusName;
        $fileStatusModel->status_description = $fileStatus->statusDescription;
        $fileStatusModel->save();

        return new FileStatus(
            $fileStatusModel->id,
            $fileStatusModel->status_name,
            $fileStatusModel->status_description
        );
    }

    public function findById(int $id): ?FileStatus
    {
        $fileStatusModel = FileStatusModel::find($id);
        if(!$fileStatusModel)
        {
            return null;
        }

        return new FileStatus(
            $fileStatusModel->id,
            $fileStatusModel->status_name,
            $fileStatusModel->status_description
        );
    }

    public function getAll()
    {
        return FileStatusModel::all()
            ->map(fn ($fileStatusModel) =>
                new FileStatus(
                    $fileStatusModel->id,
                    $fileStatusModel->status_name,
                    $fileStatusModel->status_description
                )
            )
            ->toArray();
    }

    public function delete(int $id): void
    {
        FileStatusModel::findOrFail($id)->delete();
    }

    public function getFileStatusCount(): int
    {
        return FileStatusModel::count();
    }
}
