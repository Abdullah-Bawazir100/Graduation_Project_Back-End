<?php

namespace App\Application\TaxPayerMobile\Mapper;

use App\Domain\File\Entities\File;
use App\Domain\TaxInformation\Entities\TaxInformation;
use App\Domain\TaxPayer\Entities\TaxPayer;

class TaxPayerInfoMapper
{
    public static function map(
        $taxPayer,
        $taxInformations,
        $file
    ): array {
        return [
            'taxPayer' => [
                'id' => $taxPayer->id,
                'user_id' => $taxPayer->userId,
                'trade_name' => $taxPayer->tradeName,
                'file_type' => $taxPayer->fileType,
            ],

            'tax_informations' => collect($taxInformations)
                ->map(fn ($taxInfo) => [
                    'id' => $taxInfo->id,
                    'tax_amount' => $taxInfo->taxAmount,
                    'last_payment' => $taxInfo->lastPayment,
                    'attachment' => $taxInfo->attachment,
                    // 'last_payment_date' => $taxInfo->created_at->format('Y-m-d'),
                    // 'created_at' => $taxInfo->created_at->format('Y-m-d H:i:s')
                ])
                ->values()
                ->toArray(),

                'file' => $file ? [
                    'id' => $file->id,
                    'tax_number' => $file->taxNumber,
                    'inventory_number' => $file->inventoryNumber,
                    'activity_start_date' => $file->activityStartDate,
                    'file_status' => $file->fileStatus?->statusName,
                    'payment_type' => $file->paymentType?->name,
                    'activity_type' => $file->activityType?->name,
                ] :
                [
                    'id' => null,
                    'tax_number' => null,
                    'inventory_number' => null,
                    'activity_start_date' => null,
                    'file_status' => null,
                    'payment_type' => null,
                    'activity_type' => null,
                ],
        ];
    }
}
