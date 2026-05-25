<?php

namespace App\Application\Request\UseCases;

use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\Request\Entities\TaxPayerRequest;
use App\Domain\Request\Enums\enRequestStatus;
use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\Company\Entities\Company;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class RejectRequestUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private CompanyRepositoryInterface $company_repository,
        private CharitableCompanyRepositoryInterface $charitable_company_repository
    )
    {}

    public function execute(int $requestId , ?string $note)
    {
        $request =  $this->tax_payer_request_repository->findRequestById($requestId);
        if(!$request)
        {
            throw new DomainException("لا يوجد طلب مع ال ID [$requestId].");
        }

        if($request->requestStatus === EnRequestStatus::Pending)
        {
            $acceptedRequest = $this->tax_payer_request_repository->rejectRequest($requestId , $note);
            $user = $this->user_repository->findById($request->userId);

            return [
                'RequestInfo' => $acceptedRequest,
                'UserInfo'  => $user->toArray()
            ];
        }
        else
        {
            throw new DomainException("لا يمكن رفض هذا الطلب لأن حالته الحالية ليست قيد الانتظار.");
        }
    }

    private function storeRequestToTaxPayerTable(TaxPayerRequest $request)
    {
        $existingTaxPayer = $this->tax_payer_repository->findByTradeName($request->tradeName);

        if ($existingTaxPayer) {
            throw new DomainException(
                "يوجد ملف مسجل مسبقاً بهذا الاسم التجاري."
            );
        }

        switch($request->fileType)
        {
            case enFileType::Individual :
            {
                $taxPayer = new TaxPayer(
                    id: null,
                    userId: $request->userId,
                    tradeName: $request->tradeName,
                    commercialRecord: $request->commercialRecord,
                    activityLicense: $request->activityLicense,
                    tradePict: $request->tradePict,
                    insuranceCard: $request->insuranceCard,
                    propertyDocPict: $request->propertyDocPict,
                    fileType: $request->fileType,
                    source: $request->source
                );


                return $this->tax_payer_repository->create($taxPayer);
            }

            case enFileType::Company :
            {
                $taxPayer = new TaxPayer(
                    id: null,
                    userId: $request->userId,
                    tradeName: $request->tradeName,
                    commercialRecord: $request->commercialRecord,
                    activityLicense: $request->activityLicense,
                    tradePict: $request->tradePict,
                    insuranceCard: $request->insuranceCard,
                    propertyDocPict: $request->propertyDocPict,
                    fileType: $request->fileType,
                    source: $request->source
                );
                $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

                $company = new Company(
                    id: null,
                    tax_payer_id: $createdTaxPayer->id,
                    articlesOfIncorporation: $request->articlesOfIncorporation,
                    govemorLicense: $request->govemorLicense,
                    partnersIDCards: $request->partnersIDCards,
                );
                $this->company_repository->create($company);
                break;
            }

            case enFileType::CharitableCompany :
            {
                $taxPayer = new TaxPayer(
                    id: null,
                    userId: $request->userId,
                    tradeName: $request->tradeName,
                    commercialRecord: $request->commercialRecord,
                    activityLicense: $request->activityLicense,
                    tradePict: $request->tradePict,
                    insuranceCard: $request->insuranceCard,
                    propertyDocPict: $request->propertyDocPict,
                    fileType: $request->fileType,
                    source: $request->source
                );
                $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

                $charitableCompany = new CharitableCompany(
                    id: null,
                    tax_payer_id: $createdTaxPayer->id,
                    byLawsCopy: $request->byLawsCopy,
                );
                $this->charitable_company_repository->create($charitableCompany);
                break;
            }
        }
    }
}
