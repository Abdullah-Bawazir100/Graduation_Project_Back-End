<?php

namespace App\Http\Controllers\Api;

use App\Application\Company\DTOs\CompanyDTOs;
use App\Application\Company\UseCases\CreateCompanyUseCase;
use App\Application\Company\UseCases\DeleteCompanyUseCase;
use App\Application\Company\UseCases\FindByIdUseCase;
use App\Application\Company\UseCases\ListCompaniesUseCase;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayer\StoreTaxPayerRequest;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService
    )
    {}

    public function index(ListCompaniesUseCase $useCase)
    {
        $companies = $useCase->execute();
        return ApiResponse::ok($companies , "تم جلب ملفات الشركات بنجاح.");
    }


    public function store(StoreTaxPayerRequest $request , CreateCompanyUseCase $useCase)
    {
        try {
            $authUser = Auth::user();

            if(!$authUser) {
                return ApiResponse::unauthorized();
            }

            $actor = $this->convertToDomainUser($authUser);

            $idCardUrl = $this->uploadFileService->uploadFile($request->file('idCard') , 'id-cards');
            $imageUrl = $this->uploadFileService->uploadFile($request->file('image') , 'profile-images');

            $commercialRecordUrl = $this->uploadFileService->uploadFile($request->file('commercialRecord') , 'commercial-records');
            $activityLicenseUrl = $this->uploadFileService->uploadFile($request->file('activityLicense') , 'activity-licenses');
            $tradePictUrl = $this->uploadFileService->uploadFile($request->file('tradePict') , 'trade-picts');
            $insuranceCardUrl = $this->uploadFileService->uploadFile($request->file('insuranceCard') , 'insurance-cards');
            $propertyDocPictUrl = $this->uploadFileService->uploadFile($request->file('propertyDocPict') , 'property-docs-picts');

            $articlesOfIncorporationUrl = $this->uploadFileService->uploadFile($request->file('articlesOfIncorporation') , 'articles-of-incorporation');
            $govemorLicenseUrl = $this->uploadFileService->uploadFile($request->file('govemorLicense') , 'govemor-license');
            $partnersIDCardsUrl = $this->uploadFileService->uploadFile($request->file('partnersIDCards') , 'partners-id-cards');

            $userDTO = new UserDTO(
                id: null,
                firstName: $request->firstName,
                lastName: $request->lastName,
                idCard: $idCardUrl,
                userName: null,
                password: null,
                phone: $request->phone,
                image: $imageUrl,
                departmentID: $request->departmentID,
                createdBy: $actor->id,
                role: UserRole::from($request->role)
            );
            // Map Arabic label back to enum value
            $taxPayerDTO = new TaxPayerDTOs(
                userId: null,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
            );

            $companyDTO = new CompanyDTOs(
                articlesOfIncorporation: $articlesOfIncorporationUrl,
                govemorLicense: $govemorLicenseUrl,
                partnersIDCards: $partnersIDCardsUrl,
            );

            $result = $useCase->execute($companyDTO , $taxPayerDTO , $userDTO , $actor);

            return ApiResponse::created($result , 'تم إنشاء دافع الضرائب مع ملف شركة بنجاح.');

        } catch (Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }


    public function show(int $id , FindByIdUseCase $useCase)
    {
        $company = $useCase->execute($id);
        return ApiResponse::ok($company , "تم جلب ملف الشركة بنجاح.");
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(int $id , DeleteCompanyUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(null , "تم حذف ملف الشركة مع ال ID [{$id}] بنجاح.");
    }

    private function convertToDomainUser($authUser): DomainUser
    {
        return new DomainUser(
            id: $authUser->id,
            firstName: $authUser->firstName ?? '',
            lastName: $authUser->lastName ?? '',
            idCard: $authUser->idCard ?? '',
            userName: $authUser->userName ?? '',
            phone: $authUser->phone ?? '',
            image: $authUser->image ?? '',
            password: $authUser->password,
            createdBy: $authUser->createdBy ?? 0,
            department: new Department($authUser->departmentID ?? 0, ''),
            role: $authUser->role,
            mustChangePassword: $authUser->mustChangePassword ?? true
        );
    }
}
