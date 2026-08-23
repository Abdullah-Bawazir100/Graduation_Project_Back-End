<?php

namespace App\Http\Controllers\Api;

use App\Application\Company\DTOs\CompanyDTOs;
use App\Application\Company\UseCases\CreateCompanyFileToExistingTaxPayerUseCase;
use App\Application\Company\UseCases\CreateCompanyUseCase;
use App\Application\Company\UseCases\DeleteCompanyUseCase;
use App\Application\Company\UseCases\FindByIdUseCase;
use App\Application\Company\UseCases\ListCompaniesUseCase;
use App\Application\Company\UseCases\UpdateCompanyUseCase;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayer\StoreTaxPayerRequest;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\TaxPayer\StoreFileToExistingTaxPayerRequest;
use App\Http\Responses\ApiResponse;
use DomainException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService,
        private FindByIdUseCase $findByIdUseCase
    )
    {}

    public function index(ListCompaniesUseCase $useCase)
    {
        $companies = $useCase->execute(Auth::id());
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
                fileId: $request->fileId,
                tradeName: $request->tradeName,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
                source: 'Manually',
                regionId: $request->regionId,
                districtId: $request->districtId,
            );

            $companyDTO = new CompanyDTOs(
                articlesOfIncorporation: $articlesOfIncorporationUrl,
                govemorLicense: $govemorLicenseUrl,
                partnersIDCards: $partnersIDCardsUrl,
            );

            $result = $useCase->execute($companyDTO , $taxPayerDTO , $userDTO , $actor);

            return ApiResponse::created($result , 'تم إنشاء مكلف مع ملف شركة بنجاح.');

        } catch (Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function createCompanyFileToExistingTaxPayer(
        StoreFileToExistingTaxPayerRequest $request , CreateCompanyFileToExistingTaxPayerUseCase $useCase)
    {
        try {
            $authUser = Auth::user();

            if(!$authUser) {
                return ApiResponse::unauthorized();
            }

            $commercialRecordUrl = $this->uploadFileService->uploadFile($request->file('commercialRecord') , 'commercial-records');
            $activityLicenseUrl = $this->uploadFileService->uploadFile($request->file('activityLicense') , 'activity-licenses');
            $tradePictUrl = $this->uploadFileService->uploadFile($request->file('tradePict') , 'trade-picts');
            $insuranceCardUrl = $this->uploadFileService->uploadFile($request->file('insuranceCard') , 'insurance-cards');
            $propertyDocPictUrl = $this->uploadFileService->uploadFile($request->file('propertyDocPict') , 'property-docs-picts');

            $articlesOfIncorporationUrl = $this->uploadFileService->uploadFile($request->file('articlesOfIncorporation') , 'articles-of-incorporation');
            $govemorLicenseUrl = $this->uploadFileService->uploadFile($request->file('govemorLicense') , 'govemor-license');
            $partnersIDCardsUrl = $this->uploadFileService->uploadFile($request->file('partnersIDCards') , 'partners-id-cards');

            // Map Arabic label back to enum value
            $taxPayerDTO = new TaxPayerDTOs(
                fileId: $request->fileId,
                tradeName: $request->tradeName,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
                source: 'Manually',
                regionId: $request->regionId,
                districtId: $request->districtId,
            );

            $companyDTO = new CompanyDTOs(
                articlesOfIncorporation: $articlesOfIncorporationUrl,
                govemorLicense: $govemorLicenseUrl,
                partnersIDCards: $partnersIDCardsUrl,
            );

            $result = $useCase->execute($companyDTO , $taxPayerDTO , $request->fileId, Auth::id());

            return ApiResponse::created($result , 'تم إنشاء ملف شركة لمكلف موجود بنجاح.');

        } catch (DomainException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ في الخادم، يرجى المحاولة لاحقاً.'
            ], 500);
        }
    }


    public function show(int $id , FindByIdUseCase $useCase)
    {
        $company = $useCase->execute($id, Auth::id());
        return ApiResponse::ok($company , "تم جلب ملف الشركة بنجاح.");
    }


    public function update(int $id , UpdateCompanyRequest $request , UpdateCompanyUseCase $useCase)
    {
        try {
            $findCompany = $this->findByIdUseCase->execute($id, Auth::id());
            $existingCompany = $findCompany['companyInfo'];

            if (!$existingCompany) {
                return ApiResponse::notFound([], "ملف الشركة مع ال ID [{$id}] غير موجود.");
            }

            $articlesOfIncorporationUrl = $existingCompany->articlesOfIncorporation;
            $govemorLicenseUrl = $existingCompany->govemorLicense;
            $partnersIDCardsUrl = $existingCompany->partnersIDCards;


            if($request->hasFile('articlesOfIncorporation')){
                $articlesOfIncorporationUrl = $this->uploadFileService->uploadFile($request->file('articlesOfIncorporation') , 'articles-of-incorporation');
            }

            if($request->hasFile('govemorLicense')){
                $govemorLicenseUrl = $this->uploadFileService->uploadFile($request->file('govemorLicense') , 'govemor-license');
            }

            if($request->hasFile('partnersIDCards')){
                $partnersIDCardsUrl = $this->uploadFileService->uploadFile($request->file('partnersIDCards') , 'partners-id-cards');
            }

            $companyDTO = new CompanyDTOs(
                articlesOfIncorporation: $articlesOfIncorporationUrl ?? $existingCompany->articlesOfIncorporation,
                govemorLicense: $govemorLicenseUrl ?? $existingCompany->govemorLicense,
                partnersIDCards: $partnersIDCardsUrl ?? $existingCompany->partnersIDCards,
            );

            $result = $useCase->execute($companyDTO, $existingCompany->id, Auth::id());

            return ApiResponse::ok($result, 'تم تحديث بيانات ملف الشركة بنجاح.');

        } catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('Company update error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return ApiResponse::serverError($e->getMessage());
        }
    }


    public function destroy(int $id , DeleteCompanyUseCase $useCase)
    {
        $useCase->execute($id, Auth::id());
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
