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
            'commercial_record' => $taxPayer->commercialRecord,
            'activity_license' => $taxPayer->activityLicense,
            'trade_pict' => $taxPayer->tradePict,
            'insurance_card' => $taxPayer->insuranceCard,
            'property_doc_pict' => $taxPayer->propertyDocPict,
            'file_type' => $taxPayer->fileType->value,
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
            'commercial_record' => $taxPayer->commercialRecord,
            'activity_license' => $taxPayer->activityLicense,
            'trade_pict' => $taxPayer->tradePict,
            'insurance_card' => $taxPayer->insuranceCard,
            'property_doc_pict' => $taxPayer->propertyDocPict,
            'file_type' => $taxPayer->fileType->value,
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

    public function getAll()
    {
        $taxPayers = TaxPayerModel::with('user')->get();
        return $taxPayers->map(fn(TaxPayerModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findByUserId(int $userId): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('user')->where('user_id', $userId)->first();
        return $this->mapToDomain($taxPayerModel);
    }

    public function findByUserName(string $userName): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('user')->where('user_name', $userName)->first();
        return $this->mapToDomain($taxPayerModel);
    }

    private function mapToDomain(TaxPayerModel $model): TaxPayer
    {
        return new TaxPayer(
            id: $model->id,
            userId: $model->user_id,
            commercialRecord: $model->commercial_record,
            activityLicense: $model->activity_license,
            tradePict: $model->trade_pict,
            insuranceCard: $model->insurance_card,
            propertyDocPict: $model->property_doc_pict,
            fileType: $model->file_type,
        );
    }
}
