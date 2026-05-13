<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\FileModel;

class FileRepository implements FileRepositoryInterface
{
    public function create(File $file): File
    {
        $fileModel = FileModel::create([
            'tax_number' => $file->taxNumber,
            'inventory_number' => $file->inventoryNumber,
            'activity_start_date' => $file->activityStartDate,
            'docs_count' => $file->docsCount,
            'note' => $file->note,
            'full_address' => $file->fullAddress,
            'tax_payer_id' => $file->taxPayer->id,
            'department_id' => $file->department->id,
            'file_status_id' => $file->fileStatus->id,
            'activity_type_id' => $file->activityType->id,
            'payment_type_id' => $file->paymentType->id,
            'region_id' => $file->region->id,
            'district_id' => $file->district->id,
            'created_by' => $file->creator?->id,
        ]);

        $fileModel->load('taxPayer' , 'department' , 'fileStatus' ,
        'activityType' , 'paymentType' , 'region' , 'district' , 'creator');

        $fileModel->save();


        // Refresh the file entity with the saved model's ID
        return new File(
            id: $fileModel->id,
            taxNumber: $fileModel->tax_number,
            inventoryNumber: $fileModel->inventory_number,
            activityStartDate: $fileModel->activity_start_date,
            docsCount: $fileModel->docs_count,
            note: $fileModel->note,
            fullAddress: $fileModel->full_address,
            taxPayer: $file->taxPayer,
            department: $file->department,
            fileStatus: $file->fileStatus,
            activityType: $file->activityType,
            paymentType: $file->paymentType,
            region: $file->region,
            district: $file->district,
            creator: $file->creator,
        );
    }
}
