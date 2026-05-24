<?php

namespace App\Application\Request\UseCases;

use App\Application\Request\DTOs\TaxPayerRequestDTOs;
use App\Domain\Request\Entities\TaxPayerRequest;
use App\Domain\Request\Enums\enRequestStatus;
use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateRequestUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(TaxPayerRequestDTOs $dto)
    {
        $user = $this->checkUser($dto->userId);
        $taxPayerRequest = $this->mapToDomain($dto, $user->id);
        $createdRequest = $this->tax_payer_request_repository->create($taxPayerRequest);

        return [
            'RequestInfo' => $createdRequest,
            'UserInfo' => $user->toArray()
        ];
    }

    private function mapToDomain(TaxPayerRequestDTOs $dto, int $userId): TaxPayerRequest
    {
        $specificData = match ($dto->fileType) {
            enFileType::Individual => [
                'articlesOfIncorporation' => null,
                'govemorLicense' => null,
                'partnersIDCards' => null,
                'byLawsCopy' => null,
            ],
            enFileType::Company => [
                'articlesOfIncorporation' => $dto->articlesOfIncorporation,
                'govemorLicense' => $dto->govemorLicense,
                'partnersIDCards' => $dto->partnersIDCards,
                'byLawsCopy' => null,
            ],
            enFileType::CharitableCompany => [
                'articlesOfIncorporation' => null,
                'govemorLicense' => null,
                'partnersIDCards' => null,
                'byLawsCopy' => $dto->byLawsCopy,
            ],
        };

        return new TaxPayerRequest(
            id: null,
            userId: $userId,
            tradeName: $dto->tradeName,
            commercialRecord: $dto->commercialRecord,
            activityLicense: $dto->activityLicense,
            tradePict: $dto->tradePict,
            insuranceCard: $dto->insuranceCard,
            propertyDocPict: $dto->propertyDocPict,
            fileType: $dto->fileType,
            articlesOfIncorporation: $specificData['articlesOfIncorporation'],
            govemorLicense: $specificData['govemorLicense'],
            partnersIDCards: $specificData['partnersIDCards'],
            byLawsCopy: $specificData['byLawsCopy'],
            requestStatus: enRequestStatus::Pending,
            note: $dto->note,
            source: $dto->source
        );
    }

    private function checkUser(int $userId)
    {
        $existingUser = $this->user_repository->findById($userId);
        if(!$existingUser)
        {
            throw new DomainException("لا يوجد مستخدم مع ال ID [$userId].");
        }
        if($existingUser->role !== UserRole::Tax_Payer)
        {
            throw new DomainException("المستخدم الموجود مع ال ID [$userId] ليس مكلف.");
        }

        return $existingUser;
    }
}
