<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Department\Entities\Department;
use App\Domain\File\Entities\File;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\TaxInformation\Entities\TaxInformation;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxType\Entities\TaxType;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\TaxInformationModel;

class TaxInformationRepository implements TaxInformationRepositoryInterface
{
    public function create(TaxInformation $taxInformation): TaxInformation
    {
        $taxInformationModel = TaxInformationModel::create([
            'tax_type_id' => $taxInformation->taxTypeId,
            'file_id' => $taxInformation->fileId,
            'tax_amount' => $taxInformation->taxAmount,
            'last_payment' => $taxInformation->lastPayment,
            'attachment' => $taxInformation->attachment
        ]);
        $taxInformationModel->load('taxType' , 'MainFile');

        return $this->mapToDomain($taxInformationModel);
    }

    public function update(TaxInformation $taxInformation): ?TaxInformation
    {
        $taxInformationModel = TaxInformationModel::find($taxInformation->id);

        if (!$taxInformationModel) {
            return null;
        }

        $taxInformationModel->update([
            'file_id' => $taxInformation->fileId,
            'tax_type_id' => $taxInformation->taxTypeId,
            'tax_amount' => $taxInformation->taxAmount,
            'last_payment' => $taxInformation->lastPayment,
            'attachment' => $taxInformation->attachment
        ]);
        $taxInformationModel->load('taxType' , 'MainFile');

        return $this->mapToDomain($taxInformationModel);
    }

    public function delete(int $id): void
    {
        TaxInformationModel::find($id)->delete();
    }

    public function findById(int $id): ?TaxInformation
    {
        $taxInformationModel = TaxInformationModel::find($id);

        if (!$taxInformationModel) {
            return null;
        }

        return $this->mapToDomain($taxInformationModel);
    }

    public function getTaxInformationByFileId(int $fileId)
    {
        $taxInfo = TaxInformationModel::with('MainFile')
            ->where('file_id' , $fileId)->get();

        if(!$taxInfo)
            return null;

        return $taxInfo->map(fn(TaxInformationModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function getAll(): array
    {
        $taxCollectors = TaxInformationModel::with('taxType' , 'MainFile')->get();
        return $taxCollectors->map(fn(TaxInformationModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function moveTaxInformationToAnotherTaxType(int $oldTaxTypeId, int $newTaxTypeId)
    {
        TaxInformationModel::where('tax_type_id', $oldTaxTypeId)
            ->update(['tax_type_id' => $newTaxTypeId]);
    }

    private function mapToDomain(TaxInformationModel $model): TaxInformation
    {
        $taxType = new TaxType(
            id: $model->taxType?->id ?? 0,
            name: $model->taxType?->name ?? ''
        );

        $fileModel = $model->MainFile ?? $model->file;

        $file = null;
        if ($fileModel) {
            $department = new Department(
                id: $fileModel->department->id ?? 0,
                name: $fileModel->department->name ?? '',
            );

            $userDept = $fileModel->user?->department
                ? new Department(id: $fileModel->user->department->id, name: $fileModel->user->department->name)
                : $department;

            $file = new File(
                id: $fileModel->id,
                taxNumber: $fileModel->tax_number,
                inventoryNumber: $fileModel->inventory_number,
                activityStartDate: $fileModel->activity_start_date,
                docsCount: $fileModel->docs_count,
                note: $fileModel->note,
                user: new User(
                    id: $fileModel->user->id,
                    firstName: $fileModel->user->first_name,
                    lastName: $fileModel->user->last_name,
                    idCard: $fileModel->user->id_card,
                    userName: $fileModel->user->user_name,
                    phone: $fileModel->user->phone,
                    image: $fileModel->user->image,
                    password: $fileModel->user->password,
                    createdBy: $fileModel->user->created_by,
                    department: $userDept,
                    role: $fileModel->user->role,
                    mustChangePassword: $fileModel->user->must_change_password,
                ),
                department: $department,
                fileStatus: new FileStatus(
                    id: $fileModel->fileStatus->id,
                    statusName: $fileModel->fileStatus->status_name,
                    statusDescription: $fileModel->fileStatus->status_description,
                ),
                activityType: new Activity_Type(
                    id: $fileModel->activityType->id,
                    name: $fileModel->activityType->name,
                ),
                paymentType: new PaymentType(
                    id: $fileModel->paymentType->id,
                    name: $fileModel->paymentType->name,
                    note: $fileModel->paymentType->note,
                ),
                creator: $fileModel->creator
                    ? new User(
                        id: $fileModel->creator->id,
                        firstName: $fileModel->creator->first_name,
                        lastName: $fileModel->creator->last_name,
                        idCard: $fileModel->creator->id_card,
                        userName: $fileModel->creator->user_name,
                        phone: $fileModel->creator->phone,
                        image: $fileModel->creator->image,
                        password: $fileModel->creator->password,
                        createdBy: $fileModel->creator->created_by,
                        department: $department,
                        role: $fileModel->creator->role,
                        mustChangePassword: $fileModel->creator->must_change_password,
                    ) : null,
            );
        }

        return new TaxInformation(
            id: $model->id,
            taxTypeId: $model->tax_type_id,
            fileId: $model->file_id,
            taxAmount: $model->tax_amount,
            lastPayment: $model->last_payment,
            attachment: $model->attachment,
            taxType: $taxType,
            file: $file,
        );
    }

}
