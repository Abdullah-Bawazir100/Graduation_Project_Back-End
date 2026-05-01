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

        $model->load('users');

        return $this->mapToDomain($model);
    }

    public function update(array $data, int $id): TaxPayer
    {
        $taxPayerModel = TaxPayerModel::find($id);

        $taxPayerModel->update([
            'user_id' => $data['user_id'],
            'commercial_record' => $data['commercial_record'],
            'activity_license' => $data['activity_license'],
            'trade_pict' => $data['trade_pict'],
            'insurance_card' => $data['insurance_card'],
            'property_doc_pict' => $data['property_doc_pict'],
            'file_type' => $data['file_type'],
        ]);

        $taxPayerModel->load('users');

        return $this->mapToDomain($taxPayerModel);
    }

    public function delete(int $id): void
    {
        $taxPayer = TaxPayerModel::findOrFail($id);
        $taxPayer->delete();
    }

    public function findById(int $id): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('users')->find($id);
        if (!$taxPayerModel) {
        return null;
    }
        return $this->mapToDomain($taxPayerModel);
    }

    public function getAll()
    {
        $taxPayers = TaxPayerModel::with('users')->get();
        return $taxPayers->map(fn(TaxPayerModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findByUserId(int $userId): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('users')->where('user_id', $userId)->first();
        return $this->mapToDomain($taxPayerModel);
    }

    public function findByUserName(string $userName): ?TaxPayer
    {
        $taxPayerModel = TaxPayerModel::with('users')->where('user_name', $userName)->first();
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
