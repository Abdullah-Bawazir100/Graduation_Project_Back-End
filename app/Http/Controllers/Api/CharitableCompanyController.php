<?php

namespace App\Http\Controllers\Api;

use App\Application\CharitableCompany\DTOs\CharitableCompanyDTOs;
use App\Application\CharitableCompany\UseCases\CreateCharitableCompanyUseCase;
use App\Application\CharitableCompany\UseCases\DeleteCharitableCompanyUseCase;
use App\Application\CharitableCompany\UseCases\FindCharitableCompanyByIdUseCase;
use App\Application\CharitableCompany\UseCases\ListCharitableCompaniesUseCase;
use App\Application\CharitableCompany\UseCases\UpdateCharitableCompanyUseCase;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayer\StoreTaxPayerRequest;
use App\Http\Responses\ApiResponse;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Http\Requests\CheritableCompany\UpdateCharitableCompanyRequest;
use DomainException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CharitableCompanyController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService,
        private FindCharitableCompanyByIdUseCase $findCharitableCompanyByIdUseCase
    )
    {}
    /**
     * Display a listing of the resource.
     */
    public function index(ListCharitableCompaniesUseCase $useCase)
    {
        $charitableCompanies = $useCase->execute();
        return ApiResponse::ok($charitableCompanies , "تم جلب ملفات الشركات الخيرية بنجاح.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaxPayerRequest $request , CreateCharitableCompanyUseCase $useCase)
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

            $byLawsCopyUrl = $this->uploadFileService->uploadFile($request->file('byLawsCopy') , 'by-laws-copy');

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

            $charitableCompanyDTO = new CharitableCompanyDTOs(
                byLawsCopy: $byLawsCopyUrl,
            );

            $result = $useCase->execute($charitableCompanyDTO , $taxPayerDTO , $userDTO , $actor);

            return ApiResponse::created($result , 'تم إنشاء مكلف مع ملف شركة بنجاح.');

        } catch (Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $charitableCompany = $this->findCharitableCompanyByIdUseCase->execute($id);
        return ApiResponse::ok($charitableCompany , "تم جلب ملف الشركة  الخيرية بنجاح.");

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id , UpdateCharitableCompanyRequest $request , UpdateCharitableCompanyUseCase $useCase)
    {
        try{
            $findCharitableCompany = $this->findCharitableCompanyByIdUseCase->execute($id);
            $existingCharitableCompany = $findCharitableCompany['charitableCompany'];
            if (!$existingCharitableCompany) {
                return ApiResponse::notFound([], "ملف الشركة الخيرية مع ال ID [{$id}] غير موجود.");
            }

            $byLawsCopyUrl = $existingCharitableCompany->byLawsCopy;
            if($request->hasFile('byLawsCopy'))
            {
                $byLawsCopyUrl = $this->uploadFileService->uploadFile($request->file('byLawsCopy') , 'by-laws-copy');
            }

            $charitableCompanyDTO = new CharitableCompanyDTOs(
                byLawsCopy: $byLawsCopyUrl,
            );

            $result = $useCase->execute($charitableCompanyDTO , $id);
            return ApiResponse::ok($result, 'تم تحديث بيانات ملف الشركة الخيرية بنجاح.');
        }
        catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('Charitable Company update error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return ApiResponse::serverError($e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id , DeleteCharitableCompanyUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok([] , "تم حذف ملف الشركة الخيرية مع ال ID [{$id}] بنجاح.");
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
