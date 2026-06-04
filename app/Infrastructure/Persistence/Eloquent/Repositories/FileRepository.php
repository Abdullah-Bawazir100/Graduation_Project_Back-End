<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Department\Entities\Department;
use App\Domain\District\Entities\District;
use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\Region\Entities\Region;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Infrastructure\Persistence\Eloquent\Models\FileModel;
use App\Infrastructure\Persistence\Eloquent\Models\TaxPayerModel;
use Override;

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


        return new File(
            id: $fileModel->id,
            taxNumber: $fileModel->tax_number,
            inventoryNumber: $fileModel->inventory_number,
            activityStartDate: $fileModel->activity_start_date,
            docsCount: $fileModel->docs_count,
            note: $fileModel->note,
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

    public function update(File $file, int $id): ?File
    {
        $fileModel = FileModel::with(
            'taxPayer',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'region',
            'district',
            'creator'
        )->find($id);

        if (!$fileModel) {
            return null;
        }

        $fileModel->update([
            'tax_number' => $file->taxNumber,
            'inventory_number' => $file->inventoryNumber,
            'activity_start_date' => $file->activityStartDate,
            'docs_count' => $file->docsCount,
            'note' => $file->note,
            'tax_payer_id' => $file->taxPayer->id,
            'department_id' => $file->department->id,
            'file_status_id' => $file->fileStatus->id,
            'activity_type_id' => $file->activityType->id,
            'payment_type_id' => $file->paymentType->id,
            'region_id' => $file->region->id,
            'district_id' => $file->district->id,
            'created_by' => $file->creator?->id,
        ]);

        $fileModel->refresh();

        $fileModel->load(
            'taxPayer',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'region',
            'district',
            'creator'
        );

        return $this->mapToDomain($fileModel);
    }

    public function getAll(?string $search = null, ?int $departmentId = null, ?int $activityTypeId = null, ?int $regionId = null, ?int $districtId = null)
    {
        $query = FileModel::with(
            'taxPayer',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'region',
            'district',
            'creator'
        );

        if ($search) {
            $query->whereHas('taxPayer', function ($q) use ($search) {
                $q->where('trade_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        if ($activityTypeId !== null) {
            $query->where('activity_type_id', $activityTypeId);
        }

        if ($regionId !== null) {
            $query->where('region_id', $regionId);
        }

        if ($districtId !== null) {
            $query->where('district_id', $districtId);
        }

        $files = $query->get();
        return $files->map(fn(FileModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findById(int $id): ?File
    {
        $file = FileModel::find($id);
        if(!$file)
        {
            return null;
        }
        return $this->mapToDomain($file);
    }

    public function getFileByTaxPayerId(int $taxPayerId , enFileType $fileType)
    {
        $file = FileModel::with('taxPayer')
            ->whereHas('taxPayer', function ($query) use ($fileType, $taxPayerId) {
                $query->where('tax_payer_id', $taxPayerId);
                $query->where('file_type', $fileType);
            })
            ->first();

        return $file ? $this->mapToDomain($file) : null;
    }

    public function delete(int $id): void
    {
        FileModel::findOrFail($id)->delete();
    }

    public function existsTaxPayer(int $taxPayerId , enFileType $fileType)
    {
        return FileModel::query()
            ->where('tax_payer_id', $taxPayerId)
            ->whereHas('taxPayer', function ($query) use ($fileType) {
                $query->where('file_type', $fileType->value);
            })
            ->exists();
    }

    public function countFiles(?int $departmentId = null): int
    {
        $query = FileModel::query();
        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }
        return $query->count();
    }

    public function countFilesByType(enFileType $type, ?int $departmentId = null): int
    {
        $query = FileModel::whereHas('taxPayer', function ($query) use ($type) {
            $query->where('file_type', $type->value);
        });

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        return $query->count();
    }

    private function mapToDomain(FileModel $model): File
    {
        $region = new Region(
            id: $model->region->id,
            name: $model->region->name,
        );

        $department = new Department(
            id: $model->department->id,
            name: $model->department->name,
        );

        return new File(
            id: $model->id,
            taxNumber: $model->tax_number,
            inventoryNumber: $model->inventory_number,
            activityStartDate: $model->activity_start_date,
            docsCount: $model->docs_count,
            note: $model->note,

            taxPayer: new TaxPayer(
                id: $model->taxPayer->id,
                userId: $model->taxPayer->user_id,
                tradeName: $model->taxPayer->trade_name,
                commercialRecord: $model->taxPayer->commercial_record,
                activityLicense: $model->taxPayer->activity_license,
                tradePict: $model->taxPayer->trade_pict,
                insuranceCard: $model->taxPayer->insurance_card,
                propertyDocPict: $model->taxPayer->property_doc_pict,
                fileType: $model->taxPayer->file_type,
                source: $model->taxPayer->source,
            ),

            department: $department,

            fileStatus: new FileStatus(
                id: $model->fileStatus->id,
                statusName: $model->fileStatus->status_name,
                statusDescription: $model->fileStatus->status_description,
            ),

            activityType: new Activity_Type(
                id: $model->activityType->id,
                name: $model->activityType->name,
            ),

            paymentType: new PaymentType(
                id: $model->paymentType->id,
                name: $model->paymentType->name,
                note: $model->paymentType->note,
            ),

            region: $region,

            district: new District(
                id: $model->district->id,
                name: $model->district->name,
                region: $region
            ),

            creator: $model->creator
                ? new User(
                    id: $model->creator->id,
                    firstName: $model->creator->first_name,
                    lastName: $model->creator->last_name,
                    idCard: $model->creator->id_card,
                    userName: $model->creator->user_name,
                    phone: $model->creator->phone,
                    image: $model->creator->image,
                    password: $model->creator->password,
                    createdBy: $model->creator->created_by,
                    department: $department,
                    role: $model->creator->role,
                    mustChangePassword: $model->creator->must_change_password,
                )
                : null,
        );
    }
}
