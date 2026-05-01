<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayer\StoreTaxPayerRequest;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\TaxPayer\UseCases\CreateTaxPayerUseCase;
use App\Application\TaxPayer\UseCases\CreateTaxPayerWithUserUseCase;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

class TaxPayerController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService,
    ) {}

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
                userId: $authUser->id,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
            );

            $result = $useCase->execute($taxPayerDTO , $userDTO , $actor);

            return ApiResponse::created($result , 'تم إنشاء دافع الضرائب بنجاح.');

        } catch (Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * Maps Arabic file type labels back to enum values
     */
    private function mapFileTypeToEnumValue(string $fileTypeInput): string
    {
        foreach (enFileType::cases() as $case) {
            if ($fileTypeInput === $case->value || $fileTypeInput === $case->label()) {
                return $case->value;
            }
        }

        // If no match found, throw an exception
        throw new Exception('Invalid file type value: ' . $fileTypeInput);
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
