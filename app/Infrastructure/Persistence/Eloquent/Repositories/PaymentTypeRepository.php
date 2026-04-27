<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\PaymentTypeModel;

class PaymentTypeRepository implements PaymentTypeRepositoryInterface
{
    public function create(PaymentType $paymentType)
    {
        $paymentTypeModel = PaymentTypeModel ::create([
            'name' => $paymentType->name,
            'note' => $paymentType->note
        ]);

        return new PaymentType(
            $paymentTypeModel->id,
            $paymentTypeModel->name,
            $paymentTypeModel->note
        );

    }
    public function update(PaymentType $paymentType)
    {

        $paymentTypeModel = PaymentTypeModel::find($paymentType->id);

        if (!$paymentTypeModel) {
            throw new \Exception("No Payment Type found with ID: [$paymentTypeModel->id]");
        }

        $paymentTypeModel->name = $paymentType->name;
        $paymentTypeModel->note = $paymentType->note;

        $paymentTypeModel->save();

        return new PaymentType(
            $paymentTypeModel->id,
            $paymentTypeModel->name,
            $paymentTypeModel->note
        );

    }
    public function delete(int $id)
    {
        PaymentTypeModel::findOrFail($id)->delete();
    }
    public function getAll(){

        return PaymentTypeModel::all()
            ->map(fn ($paymentTypeModel) =>
                new PaymentType(
                    $paymentTypeModel->id,
                    $paymentTypeModel->name,
                    $paymentTypeModel->note
                )
            )
            ->toArray();

    }
    public function findById(int $id)
    {
        $paymentTypeModel = PaymentTypeModel::find($id);

        if(!$paymentTypeModel) return null;

        return new PaymentType(
            $paymentTypeModel->id,
            $paymentTypeModel->name,
            $paymentTypeModel->note
        );

    }
    public function existsByName(string $name)
    {
        return PaymentTypeModel::where('name' , $name)->exists();
    }
    
    public function countPaymentsTypes(): int
    {
        return PaymentTypeModel::count();
    }
}
