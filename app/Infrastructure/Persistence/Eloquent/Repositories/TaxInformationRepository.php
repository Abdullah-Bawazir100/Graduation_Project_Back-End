<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\TaxInformation\Entities\TaxInformation;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
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
        ]);

        return new TaxInformation(
            $taxInformationModel->id,
            $taxInformationModel->tax_type_id,
            $taxInformationModel->tax_payer_id,
            $taxInformationModel->tax_amount,
            $taxInformationModel->last_payment,
        );
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
        ]);
        $taxInformationModel->load('taxType' , 'taxPayer');

        return new TaxInformation(
            $taxInformationModel->id,
            $taxInformationModel->tax_type_id,
            $taxInformationModel->tax_payer_id,
            $taxInformationModel->tax_amount,
            $taxInformationModel->last_payment,
        );
    }

    public function delete(int $id): void
    {
        TaxInformationModel::findOrFail($id)->delete();
    }

    public function findById(int $id): ?TaxInformation
    {
        $taxInformationModel = TaxInformationModel::find($id);

        if (!$taxInformationModel) {
            return null;
        }

        return new TaxInformation(
            $taxInformationModel->id,
            $taxInformationModel->tax_type_id,
            $taxInformationModel->tax_payer_id,
            $taxInformationModel->tax_amount,
            $taxInformationModel->last_payment,
        );
    }

    public function getAll(): array
    {
        return TaxInformationModel::all()
            ->map(fn ($taxInformationModel) =>
                new TaxInformation(
                    $taxInformationModel->id,
                    $taxInformationModel->tax_type_id,
                    $taxInformationModel->tax_payer_id,
                    $taxInformationModel->tax_amount,
                    $taxInformationModel->last_payment,
                )
            )
            ->toArray();
    }

    public function moveTaxInformationToAnotherTaxType(int $oldTaxTypeId, int $newTaxTypeId)
    {
        TaxInformationModel::where('tax_type_id', $oldTaxTypeId)
            ->update(['tax_type_id' => $newTaxTypeId]);
    }

}
