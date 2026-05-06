<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\TaxType\Entities\TaxType;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\TaxTypeModel;
use Override;

class TaxTypeRepository implements TaxTypeRepositoryInterface
{
    public function create(TaxType $taxType): TaxType
    {
        $taxTypeModel = TaxTypeModel::create([
            'name' => $taxType->name,
        ]);

        return new TaxType(
            $taxTypeModel->id,
            $taxTypeModel->name
        );
    }

    public function update(TaxType $taxType): ?TaxType
    {
        $taxTypeModel = TaxTypeModel::find($taxType->id);

        if (!$taxTypeModel) {
            return null;
        }

        $taxTypeModel->name = $taxType->name;
        $taxTypeModel->save();

        return new TaxType(
            $taxTypeModel->id,
            $taxTypeModel->name
        );
    }

    public function delete(int $id): void
    {
        TaxTypeModel::findOrFail($id)->delete();
    }

    public function findById(int $id): ?TaxType
    {
        $taxTypeModel = TaxTypeModel::find($id);

        if (!$taxTypeModel) {
            return null;
        }

        return new TaxType(
            $taxTypeModel->id,
            $taxTypeModel->name
        );
    }

    public function getAll(): array
    {
        return TaxTypeModel::all()
            ->map(fn ($taxTypeModel) =>
                new TaxType(
                    $taxTypeModel->id,
                    $taxTypeModel->name
                )
            )
            ->toArray();
    }

    public function existsByName(string $name): bool
    {
        return TaxTypeModel::where('name', $name)->exists();
    }

    // public function moveTaxCollectorsToAnotherJobType(int $oldJobTypeId, int $newJobTypeId)
    // {
    //     TaxCollectorModel::where('job_type_id', $oldJobTypeId)
    //         ->update(['job_type_id' => $newJobTypeId]);
    // }

}
