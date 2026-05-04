<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;
use App\Domain\Company\Entities\Company;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\CompanyModel;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function create(Company $company): Company
    {
        $model = CompanyModel::create([
            'tax_payer_id' => $company->tax_payer_id,
            'articles_of_incorporation' => $company->articlesOfIncorporation,
            'govemor_license' => $company->govemorLicense,
            'partners_id_cards' => $company->partnersIDCards,
        ]);

        $model->load('taxPayer');

        return $this->mapToDomain($model);
    }

    public function update(Company $company, int $id): ?Company
    {
        $companyModel = CompanyModel::with('taxPayer')->find($id);
        if(!$companyModel)
        {
            return null;
        }

        $companyModel->update([
            'tax_payer_id' => $company->tax_payer_id,
            'articles_of_incorporation' => $company->articlesOfIncorporation,
            'govemor_license' => $company->govemorLicense,
            'partners_id_cards' => $company->partnersIDCards
        ]);

        $companyModel->load('taxPayer');
        return $this->mapToDomain($companyModel);

    }

    public function getAll()
    {
        $taxPayers = CompanyModel::with('taxPayer')->get();
        return $taxPayers->map(fn(CompanyModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findById(int $id): ?Company
    {
        $taxPayerModel = CompanyModel::with('taxpayer')->find($id);
        if (!$taxPayerModel) {
            return null;
        }
        return $this->mapToDomain($taxPayerModel);
    }

    public function delete(int $id)
    {
        CompanyModel::findOrFail($id)->delete();
    }

    private function mapToDomain(CompanyModel $model): Company
    {
        return new Company(
            id: $model->id,
            tax_payer_id: $model->tax_payer_id,
            articlesOfIncorporation: $model->articles_of_incorporation,
            govemorLicense: $model->govemor_license,
            partnersIDCards: $model->partners_id_cards,
        );
    }
}
