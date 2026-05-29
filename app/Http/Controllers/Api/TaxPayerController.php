<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayer\StoreTaxPayerRequest;
use App\Http\Requests\TaxPayer\StoreFileToExistingTaxPayerRequest;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\TaxPayer\UseCases\CreateTaxPayerWithUserUseCase;
use App\Application\TaxPayer\UseCases\CreateFileToExistingTaxPayerUseCase;
use App\Application\TaxPayer\UseCases\DeleteTaxPayerUseCase;
use App\Application\TaxPayer\UseCases\FindTaxPayerByIdUseCase;
use App\Application\TaxPayer\UseCases\FindTaxPayerByUserIDUseCase;
use App\Application\TaxPayer\UseCases\ListAllTaxPayersWithSourceUseCase;
use App\Application\TaxPayer\UseCases\ListTaxPayersUseCase;
use App\Application\TaxPayer\UseCases\ListTaxPayersWithSpecialInfoUseCase;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaxPayerController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService,
        private FindTaxPayerByIdUseCase $findTaxPayerByIdUseCase,
        private FindUserByIdUseCase $findUserByIdUseCase,
    ) {}

    public function index(ListTaxPayersUseCase $useCase): ApiResponse
    {
        $authenticatedUser = Auth::user();
        $taxPayers = $useCase->execute($authenticatedUser->id);
        return ApiResponse::ok($taxPayers , 'تم جلب المكلفين بنجاح.');
    }

    public function store(StoreTaxPayerRequest $request , CreateTaxPayerWithUserUseCase $useCase)
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
                tradeName: $request->tradeName,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
                source: 'Manually'
            );

            $result = $useCase->execute($taxPayerDTO , $userDTO , $actor);

            return ApiResponse::created($result , 'تم إنشاء مكلف مع  ملف فرد بنجاح.');

        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }

    public function createFileToExistingTaxPayer(StoreFileToExistingTaxPayerRequest $request, CreateFileToExistingTaxPayerUseCase $useCase)
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

            $taxPayerDTO = new TaxPayerDTOs(
                userId: $request->userId,
                tradeName: $request->tradeName,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
                source: 'Manually'
            );
            $result = $useCase->execute($taxPayerDTO, $request->userId, $authUser->id);

            return ApiResponse::created($result, 'تم إنشاء ملف فرد جديد للمكلف الحالي بنجاح.');

        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }

    public function update(int $id , UpdateTaxPayerRequest $request , UpdateTaxPayerUseCase $useCase)
    {
        try {
            $authenticatedUser = Auth::user();
            $existingTaxPayer = $this->findTaxPayerByIdUseCase->execute($id, $authenticatedUser->id);
            if (!$existingTaxPayer) {
                return ApiResponse::notFound([], 'المكلف مع ال ID [' . $id . '] غير موجود.');
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
                tradeName:  $request->tradeName ?? $existingTaxPayer->tradeName,
                commercialRecord: $commercialRecordUrl ?? $existingTaxPayer->commercialRecord,
                activityLicense: $activityLicenseUrl ?? $existingTaxPayer->activityLicense,
                tradePict: $tradePictUrl ?? $existingTaxPayer->tradePict,
                insuranceCard: $insuranceCardUrl ?? $existingTaxPayer->insuranceCard,
                propertyDocPict: $propertyDocPictUrl ?? $existingTaxPayer->propertyDocPict,
                fileType: $existingTaxPayer->fileType,
                source: 'Manually'
            );

            $updatedTaxPayer = $useCase->execute($taxPayerDTO, $existingTaxPayer->id , $authenticatedUser->id);
            return ApiResponse::ok($updatedTaxPayer, 'تم تحديث بيانات المكلف بنجاح.');

        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }


    public function show(int $id , ShowTaxPayerUseCase $useCase)
    {
        $authenticatedUserId = Auth::id();
        $taxPayer = $useCase->execute($id, $authenticatedUserId);
        return ApiResponse::ok($taxPayer , 'تم جلب المكلف بنجاح.');
    }

    public function findTaxPayerByUserID(int $userID , FindTaxPayerByUserIDUseCase $useCase)
    {
        $authenticatedUserId = Auth::id();
        $taxPayer = $useCase->execute($userID, $authenticatedUserId);
        return ApiResponse::ok($taxPayer , "تم جلب المستخدم المكلف مع ال ID [{$userID}] بنجاح.");
    }

    public function destroy(int $id , DeleteTaxPayerUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(null , "تم حذف دافع الضرائب مع ال ID [{$id}] بنجاح.");
    }

    public function getTaxPayersWithSpecialInfo(
        Request $request
        , ListTaxPayersWithSpecialInfoUseCase $useCase)
    {
        $search = $request->query('search');
        $authenticatedUserId = Auth::id();
        $taxPayers = $useCase->execute($search, $authenticatedUserId);

        return ApiResponse::ok($taxPayers , "تم جلب المستخدمين المكلفين بنجاح.");
    }

    public function getAllTaxPayersWithSource(ListAllTaxPayersWithSourceUseCase $useCase)
    {
        $authenticatedUser = Auth::user();
        $taxPayers = $useCase->execute($authenticatedUser->id);
        return ApiResponse::ok($taxPayers , 'تم جلب المكلفين بنجاح.');
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
            department: new Department($authUser->department_id ?? 0, ''),
            role: $authUser->role,
            mustChangePassword: $authUser->mustChangePassword ?? true
        );
    }
}
