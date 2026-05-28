<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Infrastructure\Persistence\Eloquent\Models\TaxPayerModel;
use Override;

class TaxPayerRepository implements TaxPayerRepositoryInterface
{

    public function create(TaxPayer $taxPayer): TaxPayer
    {
        $model = TaxPayerModel::create([
            'user_id' => $taxPayer->userId,
            'trade_name' => $taxPayer->tradeName,
            'commercial_record' => $taxPayer->commercialRecord,
            'activity_license' => $taxPayer->activityLicense,
            'trade_pict' => $taxPayer->tradePict,
            'insurance_card' => $taxPayer->insuranceCard,
            'property_doc_pict' => $taxPayer->propertyDocPict,
            'file_type' => $taxPayer->fileType->value,
            'source' => $taxPayer->source
        ]);

        $model->load('user');

        return $this->mapToDomain($model);
    }

    public function update(TaxPayer $taxPayer , int $id): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::find($id);
        if (!$taxPayerModel) {
            return null;
        }

        $taxPayerModel->update([
            'user_id' => $taxPayer->userId,
            'trade_name' => $taxPayer->tradeName,
            'commercial_record' => $taxPayer->commercialRecord,
            'activity_license' => $taxPayer->activityLicense,
            'trade_pict' => $taxPayer->tradePict,
            'insurance_card' => $taxPayer->insuranceCard,
            'property_doc_pict' => $taxPayer->propertyDocPict,
            'file_type' => $taxPayer->fileType->value,
            'source' => $taxPayer->source
        ]);

        $taxPayerModel->load('user');

        return $this->mapToDomain($taxPayerModel);
    }

    public function delete(int $id): void
    {
        $taxPayer = TaxPayerModel::findOrFail($id);
        $taxPayer->delete();
    }

    public function findById(int $id): ?TaxPayer
    {

        $taxPayerModel = TaxPayerModel::with('user')->find($id);

        if (!$taxPayerModel) {
            return null;
        }

        return $this->mapToDomain($taxPayerModel);
    }

    public function getAll(?int $departmentId = null)
    {
        // $taxPayers = TaxPayerModel::with('user')
        //         ->where('file_type', 'Individual')
        //         ->get();

        // return $taxPayers->map(fn(TaxPayerModel $model) => $this->mapToDomain($model))->toArray();

        $query = TaxPayerModel::with('user')
        ->where('file_type', 'Individual');

        if ($departmentId !== null) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->get()
            ->map(fn(TaxPayerModel $model) => $this->mapToDomain($model))
            ->toArray();
    }

    public function getAllTaxPayers(?int $departmentId)
    {
        $query = TaxPayerModel::with('user');

        if ($departmentId !== null) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->get()
            ->map(fn(TaxPayerModel $model) => $this->mapToDomain($model))
            ->toArray();
    }

    public function findByUserId(int $userId): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('user')->where('user_id', $userId)->first();
        return $this->mapToDomain($taxPayerModel);
    }

    public function findByUserName(string $userName): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('user')->where('user_name', $userName)->first();
        if(!$taxPayerModel)
        {
            return null;
        }
        return $this->mapToDomain($taxPayerModel);
    }

    public function findByTradeName(string $tradeName): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('user')->where('trade_name', $tradeName)->first();
        if(!$taxPayerModel)
        {
            return null;
        }
        return $this->mapToDomain($taxPayerModel);
    }

    public function getTaxPayersWithSpecialInfo(?string $search = null, ?int $departmentId = null)
    {
        $query = TaxPayerModel::with('user', 'companies', 'charitable_companies');

        // اذا يوجد بحث
        if ($search !== null) {
            $query->where('trade_name', 'LIKE', '%' . $search . '%');
        }

        if ($departmentId !== null) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $taxPayers = $query->get();

        return $taxPayers
            ->map(fn(TaxPayerModel $model) => $this->mapToDomain($model))
            ->toArray();

    }

    public function createFileToExistingTaxPayer(TaxPayer $taxPayer , int $userId): ?TaxPayer
    {

        $model = TaxPayerModel::create([
            'user_id' => $userId,
            'trade_name' => $taxPayer->tradeName,
            'commercial_record' => $taxPayer->commercialRecord,
            'activity_license' => $taxPayer->activityLicense,
            'trade_pict' => $taxPayer->tradePict,
            'insurance_card' => $taxPayer->insuranceCard,
            'property_doc_pict' => $taxPayer->propertyDocPict,
            'file_type' => $taxPayer->fileType->value,
            'source' => $taxPayer->source
        ]);

        $model->load('user');

        return $this->mapToDomain($model);
    }

    private function mapToDomain(TaxPayerModel $model): TaxPayer
    {
        return new TaxPayer(
            id: $model->id,
            userId: $model->user_id,
            tradeName: $model->trade_name,
            commercialRecord: $model->commercial_record,
            activityLicense: $model->activity_license,
            tradePict: $model->trade_pict,
            insuranceCard: $model->insurance_card,
            propertyDocPict: $model->property_doc_pict,
            fileType: $model->file_type,
            source: $model->source
        );
    }
}
