<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\CharitableCompanyModel;

class CharitableCompanyRepository implements CharitableCompanyRepositoryInterface
{
    public function create(CharitableCompany $charitableCompany): CharitableCompany
    {
        $charitableCompanyModel = CharitableCompanyModel::create([
            'tax_payer_id' => $charitableCompany->tax_payer_id,
            'by_laws_copy' => $charitableCompany->byLawsCopy
        ]);
        $charitableCompanyModel->load('taxPayer');
        return $this->mapToDomain($charitableCompanyModel);
    }

    public function update(CharitableCompany $charitableCompany , int $id): ?CharitableCompany
    {
        $charitableCompanyModel = CharitableCompanyModel::with('taxPayer')->find($id);
        if(!$charitableCompany)
        {
            return null;
        }
        $charitableCompanyModel->update([
            'tax_payer_id' => $charitableCompany->tax_payer_id,
            'by_laws_copy' => $charitableCompany->byLawsCopy
        ]);
        $charitableCompanyModel->load('taxPayer');
        return $this->mapToDomain($charitableCompanyModel);
    }

    public function getAll()
    {
        $charitableCompanies = CharitableCompanyModel::with('taxPayer')->get();
        return $charitableCompanies->map(fn(CharitableCompanyModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findById(int $id): ?CharitableCompany
    {
        $charitableCompanyModel = CharitableCompanyModel::with('taxpayer')->find($id);
        if (!$charitableCompanyModel) {
            return null;
        }
        return $this->mapToDomain($charitableCompanyModel);
    }
    public function delete(int $id)
    {
        CharitableCompanyModel::findOrFail($id)->delete();
    }

    private function mapToDomain(CharitableCompanyModel $model): CharitableCompany
    {
        return new CharitableCompany(
            id: $model->id,
            tax_payer_id: $model->tax_payer_id,
            byLawsCopy: $model->by_laws_copy,
        );
    }
}
