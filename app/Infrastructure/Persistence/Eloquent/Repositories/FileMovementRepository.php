<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\FileMovement\Entities\FileMovement;
use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\FileMovementModel;

class FileMovementRepository implements FileMovementRepositoryInterface
{
    public function create(FileMovement $fileMovement): FileMovement
    {
        $fileMovementModel = FileMovementModel::create([
            'file_id' => $fileMovement->file->id,
            'tax_collector_id' => $fileMovement->taxCollector->id,
            'status' => $fileMovement->status,
            'date' => $fileMovement->date,
            'department_id' => $fileMovement->department->id,
            'created_by' => $fileMovement->creator?->id,
        ]);

        $fileMovementModel->load('file' , 'taxCollector' , 'department' , 'creator');

        return new FileMovement(
            id: $fileMovementModel->id,
            status: $fileMovement->status,
            date: $fileMovement->date,

            file: $fileMovement->file,
            taxCollector: $fileMovement->taxCollector,
            department: $fileMovement->department,
            creator: $fileMovement->creator
        );
    }
}
