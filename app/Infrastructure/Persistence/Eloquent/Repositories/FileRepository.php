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
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Infrastructure\Persistence\Eloquent\Models\FileModel;
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
            'user_id' => $file->user->id,
            'department_id' => $file->department->id,
            'file_status_id' => $file->fileStatus->id,
            'activity_type_id' => $file->activityType->id,
            'payment_type_id' => $file->paymentType->id,
            'created_by' => $file->creator?->id,
        ]);


        $fileModel->load('user', 'department', 'fileStatus',
            'activityType', 'paymentType', 'creator');

        return new File(
            id: $fileModel->id,
            taxNumber: $fileModel->tax_number,
            inventoryNumber: $fileModel->inventory_number,
            activityStartDate: $fileModel->activity_start_date,
            docsCount: $fileModel->docs_count,
            note: $fileModel->note,
            user: $file->user,
            department: $file->department,
            fileStatus: $file->fileStatus,
            activityType: $file->activityType,
            paymentType: $file->paymentType,
            creator: $file->creator,
        );
    }

    public function update(File $file, int $id): ?File
    {
        $fileModel = FileModel::with(
            'user',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
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
            'user_id' => $file->user->id,
            'department_id' => $file->department->id,
            'file_status_id' => $file->fileStatus->id,
            'activity_type_id' => $file->activityType->id,
            'payment_type_id' => $file->paymentType->id,
            'created_by' => $file->creator?->id,
        ]);

        $fileModel->refresh();

        $fileModel->load(
            'user',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'creator'
        );

        return $this->mapToDomain($fileModel);
    }

    public function getAll(?string $search = null, ?int $departmentId = null, ?int $activityTypeId = null, ?int $regionId = null, ?int $districtId = null)
    {
        $query = FileModel::with(
            'user',
            'taxPayers.region',
            'taxPayers.district',
            'taxPayers.companies',
            'taxPayers.charitable_companies',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'creator'
        );

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'LIKE', '%' . $search . '%')
                ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                ->orWhere('user_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        if ($activityTypeId !== null) {
            $query->where('activity_type_id', $activityTypeId);
        }

        if ($regionId !== null) {
            $query->whereHas('taxPayers', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        if ($districtId !== null) {
            $query->whereHas('taxPayers', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        }

        $files = $query->get();
        return $files->map(fn(FileModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findById(int $id): ?File
    {
        $file = FileModel::with(
            'user',
            'taxPayers.region',
            'taxPayers.district',
            'taxPayers.companies',
            'taxPayers.charitable_companies',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'creator'
        )->find($id);

        if(!$file)
        {
            return null;
        }
        return $this->mapToDomain($file);
    }

    public function getFileByUserId(int $userId): ?File
    {
        $file = FileModel::with(
            'user',
            'department',
            'fileStatus',
            'activityType',
            'paymentType',
            'creator'
        )->where('user_id', $userId)->first();

        return $file ? $this->mapToDomain($file) : null;
    }

    public function delete(int $id): void
    {
        FileModel::findOrFail($id)->delete();
    }

    public function existsUserFile(int $userId): bool
    {
        return FileModel::query()->where('user_id', $userId)->exists();
    }

    public function countFiles(?int $departmentId = null): int
    {
        $query = FileModel::query();
        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }
        return $query->count() ?? 0;
    }

    public function countFilesByType(enFileType $type, ?int $departmentId = null): int
    {
        $query = FileModel::query();
        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        return $query->count() ?? 0;
    }

    private function mapToDomain(FileModel $model): File
    {

        $department = new Department(
            id: $model->department->id,
            name: $model->department->name,
        );

        $userDept = $model->user->department
            ? new Department(id: $model->user->department->id, name: $model->user->department->name)
            : $department;

        return new File(
            id: $model->id,
            taxNumber: $model->tax_number,
            inventoryNumber: $model->inventory_number,
            activityStartDate: $model->activity_start_date,
            docsCount: $model->docs_count,
            note: $model->note,

            user: new User(
                id: $model->user->id,
                firstName: $model->user->first_name,
                lastName: $model->user->last_name,
                idCard: $model->user->id_card,
                userName: $model->user->user_name,
                phone: $model->user->phone,
                image: $model->user->image,
                password: $model->user->password,
                createdBy: $model->user->created_by,
                department: $userDept,
                role: $model->user->role,
                mustChangePassword: $model->user->must_change_password,
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
            taxPayers: $model->relationLoaded('taxPayers') ? $model->taxPayers->map(function ($tp) {
                $region = $tp->relationLoaded('region') && $tp->region ? new Region($tp->region->id, $tp->region->name) : null;
                $district = $tp->relationLoaded('district') && $tp->district && $region ? new District($tp->district->id, $tp->district->name, $region) : null;

                $companies = $tp->relationLoaded('companies') && $tp->companies ? $tp->companies->map(fn($c) => $c->toArray())->toArray() : [];
                $charitableCompanies = $tp->relationLoaded('charitable_companies') && $tp->charitable_companies ? $tp->charitable_companies->map(fn($c) => $c->toArray())->toArray() : [];

                return new \App\Domain\TaxPayer\Entities\TaxPayer(
                    id: $tp->id,
                    fileId: $tp->file_id,
                    tradeName: $tp->trade_name,
                    commercialRecord: $tp->commercial_record,
                    activityLicense: $tp->activity_license,
                    tradePict: $tp->trade_pict,
                    insuranceCard: $tp->insurance_card,
                    propertyDocPict: $tp->property_doc_pict,
                    fileType: $tp->file_type,
                    source: $tp->source,
                    region: $region,
                    district: $district,
                    companies: $companies,
                    charitableCompanies: $charitableCompanies,
                );
            })->toArray() : [],
        );
    }
}
