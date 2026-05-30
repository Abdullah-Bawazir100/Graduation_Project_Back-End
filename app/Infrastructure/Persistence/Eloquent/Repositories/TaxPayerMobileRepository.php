<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\FileModel;
use App\Infrastructure\Persistence\Eloquent\Models\TaxPayerModel;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Override;

class TaxPayerMobileRepository implements TaxPayerMobileRepositoryInterface
{
    public function create(User $user)
    {

        $taxPayerData = UserModel::create([
            'first_name'    => $user->firstName,
            'last_name'     => $user->lastName,
            'id_card'       => $user->idCard,
            'user_name'     => $user->userName,
            'phone'         => $user->phone,
            'password'      => $user->password,
            'image'         => $user->image,
            'role'          => $user->role->value,
            'department_id' => $user->department->id,
            'created_by'    => $user->createdBy,
            'must_change_password' => $user->mustChangePassword
        ]);

        $taxPayerData->load('department');
        $taxPayerData->created_by = $taxPayerData->id;
        $taxPayerData->must_change_password = false;
        $taxPayerData->save();

        return $this->mapToDomain($taxPayerData);
    }
    public function update(User $user)
    {
        $taxPayerData = UserModel::findOrFail($user->id);

        $taxPayerData->update([
            'first_name'    => $user->firstName,
            'last_name'     => $user->lastName,
            'id_card'       => $user->idCard,
            'user_name'     => $user->userName,
            'phone'         => $user->phone,
            'image'         => $user->image,
            'password'      => $user->password,
            'role'          => $user->role->value,
            'department_id' => $user->department->id,
        ]);

        $taxPayerData->load('department');
        return $this->mapToDomain($taxPayerData);
    }
    public function getTaxPayerMobileFile(int $userId)
    {
        $taxPayerFiles = TaxPayerModel::query()
            ->with([
                'user',
                'companies',
                'charitable_companies',
                'tax_informations',
                'file'
            ])
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get();


            return $taxPayerFiles->map(function ($taxPayer) {

            return [
                'file' => [
                    'id' => $taxPayer->id,
                    'user_id' => $taxPayer->user_id,
                    'trade_name' => $taxPayer->trade_name,
                    'file_type' => $taxPayer->file_type,
                ],

                'tax_informations' => $taxPayer->tax_informations->map(function ($taxInfo) {
                    return [
                        'id' => $taxInfo->id,
                        'tax_amount' => $taxInfo->tax_amount,
                        'last_payment' => $taxInfo->last_payment,
                        'attachment' => $taxInfo->attachment,
                        'last_payment_date' => $taxInfo->created_at?->format('Y-m-d'),
                        'created_at' => $taxInfo->created_at?->format('Y-m-d H:i:s'),
                    ];
                })->values(),
                ];
            });
    }

    public function getTaxPayerFileById(int $taxPayerId): ?TaxPayer
    {
        $taxPayer = TaxPayerModel::with('companies' , 'charitable_companies' , 'tax_informations')->find($taxPayerId);
        if(!$taxPayer)
            return null;

        return new TaxPayer(
            id: $taxPayer->id,
            userId: $taxPayer->user_id,
            tradeName: $taxPayer->trade_name,
            commercialRecord: $taxPayer->commercial_record,
            activityLicense: $taxPayer->activity_license,
            tradePict: $taxPayer->trade_pict,
            insuranceCard: $taxPayer->insurance_card,
            propertyDocPict: $taxPayer->property_doc_pict,
            fileType: $taxPayer->file_type,
            source: $taxPayer->source
        );
    }

    private function mapToDomain(UserModel $userData): User
    {
        $department = new Department(
            id: $userData->department?->id ?? 0,
            name: $userData->department?->name ?? ''
        );


        return new User(
            id: $userData->id,
            firstName: $userData->first_name,
            lastName: $userData->last_name,
            idCard: $userData->id_card ?? '',
            userName: $userData->user_name,
            phone: $userData->phone,
            image: $userData->image ?? '',
            password: $userData->password,
            createdBy: $userData->created_by,
            department: $department,
            role: $userData->role,
            mustChangePassword: $userData->must_change_password ?? false
        );
    }
}
