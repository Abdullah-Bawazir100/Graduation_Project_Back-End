<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayer\StoreTaxPayerRequest;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\TaxPayer\UseCases\CreateTaxPayerWithUserUseCase;
use App\Application\TaxPayer\UseCases\DeleteTaxPayerUseCase;
use App\Application\TaxPayer\UseCases\FindTaxPayerByIdUseCase;
use App\Application\TaxPayer\UseCases\FindTaxPayerByUserIDUseCase;
use App\Application\TaxPayer\UseCases\ListTaxPayersUseCase;
use App\Application\TaxPayer\UseCases\ShowTaxPayerUseCase;
use App\Application\TaxPayer\UseCases\UpdateTaxPayerUseCase;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Application\User\UseCases\FindUserByIdUseCase;
use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Http\Requests\TaxPayer\UpdateTaxPayerRequest;
use App\Http\Responses\ApiResponse;
use DomainException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaxPayerController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService,
        private FindTaxPayerByIdUseCase $findTaxPayerByIdUseCase,
        private FindUserByIdUseCase $findUserByIdUseCase,
    ) {}

    public function index(ListTaxPayersUseCase $useCase): ApiResponse
    {
        $taxPayers = $useCase->execute();
        return ApiResponse::ok($taxPayers , 'تم جلب المكلفين بنجاح.');
    }

    public function store(StoreTaxPayerRequest $request , CreateTaxPayerWithUserUseCase $useCase): ApiResponse
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

            $result = $useCase->execute($taxPayerDTO , $userDTO , $actor);

            return ApiResponse::created($result , 'تم إنشاء مكلف مع بنجاح.');

        } catch (Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function update(int $id , UpdateTaxPayerRequest $request , UpdateTaxPayerUseCase $useCase)
    {
        try {
            $existingTaxPayer = $this->findTaxPayerByIdUseCase->execute($id);
            if (!$existingTaxPayer) {
                throw new DomainException("المكلف مع ال ID [{$id}] غير موجود.");
            }

            $commercialRecordUrl = $existingTaxPayer->commercialRecord;
            $activityLicenseUrl = $existingTaxPayer->activityLicense;
            $tradePictUrl = $existingTaxPayer->tradePict;
            $insuranceCardUrl = $existingTaxPayer->insuranceCard;
            $propertyDocPictUrl = $existingTaxPayer->propertyDocPict;

            if($request->hasFile('commercialRecord')){
                $commercialRecordUrl = $this->uploadFileService->uploadFile($request->file('commercialRecord') , 'commercial-records');
            }

            if($request->hasFile('activityLicense')){
                $activityLicenseUrl = $this->uploadFileService->uploadFile($request->file('activityLicense') , 'activity-licenses');
            }

            if($request->hasFile('tradePict')){
                $tradePictUrl = $this->uploadFileService->uploadFile($request->file('tradePict') , 'trade-picts');
            }

            if($request->hasFile('insuranceCard')){
                $insuranceCardUrl = $this->uploadFileService->uploadFile($request->file('insuranceCard') , 'insurance-cards');
            }

            if($request->hasFile('propertyDocPict')){
                $propertyDocPictUrl = $this->uploadFileService->uploadFile($request->file('propertyDocPict') , 'property-docs-picts');
            }

            $taxPayerDTO = new TaxPayerDTOs(
                userId: $existingTaxPayer->userId,
                commercialRecord: $commercialRecordUrl ?? $existingTaxPayer->commercialRecord,
                activityLicense: $activityLicenseUrl ?? $existingTaxPayer->activityLicense,
                tradePict: $tradePictUrl ?? $existingTaxPayer->tradePict,
                insuranceCard: $insuranceCardUrl ?? $existingTaxPayer->insuranceCard,
                propertyDocPict: $propertyDocPictUrl ?? $existingTaxPayer->propertyDocPict,
                fileType: $existingTaxPayer->fileType,
            );

            $updatedTaxPayer = $useCase->execute($taxPayerDTO, $existingTaxPayer->id);
            return ApiResponse::ok($updatedTaxPayer, 'تم تحديث بيانات المكلف بنجاح.');

        } catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('TaxPayer update error: ' . $e->getMessage());
            return ApiResponse::serverError('Internal server error');
        }
    }


    public function show(int $id , ShowTaxPayerUseCase $useCase)
    {
        $taxPayer = $useCase->execute($id);
        return ApiResponse::ok($taxPayer , 'تم جلب المكلف بنجاح.');
    }

    public function findTaxPayerByUserID(int $userID , FindTaxPayerByUserIDUseCase $useCase)
    {
        $taxPayer = $useCase->execute($userID);
        return ApiResponse::ok($taxPayer , "تم جلب المستخدم المكلف مع ال ID [{$userID}] بنجاح.");
    }

    public function destroy(int $id , DeleteTaxPayerUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(null , "تم حذف المكلف مع ال ID [{$id}] بنجاح.");
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
