<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\TaxInformation\Entities\TaxInformation;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxType\Entities\TaxType;
use App\Infrastructure\Persistence\Eloquent\Models\TaxInformationModel;

class TaxInformationRepository implements TaxInformationRepositoryInterface
{
    public function create(TaxInformation $taxInformation): TaxInformation
    {
        $taxInformationModel = TaxInformationModel::create([
            'tax_type_id' => $taxInformation->taxTypeId,
            'tax_payer_id' => $taxInformation->taxPayerId,
            'tax_amount' => $taxInformation->taxAmount,
            'last_payment' => $taxInformation->lastPayment,
            'attachment' => $taxInformation->attachment
        ]);
        $taxInformationModel->load('taxType' , 'taxPayer');

        return $this->mapToDomain($taxInformationModel);
    }

    public function update(TaxInformation $taxInformation): ?TaxInformation
    {
        $taxInformationModel = TaxInformationModel::find($taxInformation->id);

        if (!$taxInformationModel) {
            return null;
        }

        $taxInformationModel->update([
            'tax_payer_id' => $taxInformation->taxPayerId,
            'tax_type_id' => $taxInformation->taxTypeId,
            'tax_amount' => $taxInformation->taxAmount,
            'last_payment' => $taxInformation->lastPayment,
            'attachment' => $taxInformation->attachment
        ]);
        $taxInformationModel->load('taxType' , 'taxPayer');

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

    public function getTaxInformationByTaxPayerId(int $taxPayerId)
    {
        $taxInfo = TaxInformationModel::with('taxPayer')
            ->where('tax_payer_id' , $taxPayerId)->get();

        if(!$taxInfo)
            return null;

        return $taxInfo->map(fn(TaxInformationModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function getAll(): array
    {
        $taxCollectors = TaxInformationModel::with('taxType' , 'taxPayer')->get();
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

        $taxPayerModel = $model->taxPayer;

        $taxPayer = new TaxPayer(
            id: $taxPayerModel?->id,
            userId: $taxPayerModel?->user_id,
            tradeName: $taxPayerModel?->trade_name,
            commercialRecord: $taxPayerModel?->commercial_record,
            activityLicense: $taxPayerModel?->activity_license,
            tradePict: $taxPayerModel?->trade_pict,
            insuranceCard: $taxPayerModel?->insurance_card,
            propertyDocPict: $taxPayerModel?->property_doc_pict,
            fileType: $taxPayerModel?->file_type,
            source: $taxPayerModel->source
        );

        return new TaxInformation(
            id: $model->id,
            taxTypeId: $model->tax_type_id,
            taxPayerId: $model->tax_payer_id,
            taxAmount: $model->tax_amount,
            lastPayment: $model->last_payment,
            attachment: $model->attachment,
            taxType: $taxType,
            taxPayer: $taxPayer,
        );
    }

}
