<?php

namespace App\Http\Controllers\Api;

use App\Application\TaxPayerMobile\UseCases\CreateTaxPayerMobileUseCase;
use App\Application\TaxPayerMobile\UseCases\UpdateTaxPayerMobileUseCase;
use App\Application\User\DTOs\LoginDTO;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Application\User\UseCases\LoginUseCase;
use App\Application\User\UseCases\LogoutUseCase;
use App\Domain\Department\Entities\Department;
use App\Http\Controllers\Controller;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Http\Requests\TaxPayerMobile\StoreTaxPayerMobileRequest;
use App\Http\Requests\TaxPayerMobile\UpdateTaxPayerMobileRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxPayerMobileController extends Controller
{
    public function __construct(
        private UploadFileService  $uploadFileService,
        private UserRepositoryInterface  $userRepository,
    )
    {}

    public function index()
    {
        //
    }


    public function store(StoreTaxPayerMobileRequest  $request , CreateTaxPayerMobileUseCase $useCase)
    {
        try {

            $idCardUrl = $this->uploadFileService->uploadFile($request->file('idCard') , 'tax-payer-id-cards');
            $imageUrl = $this->uploadFileService->uploadFile($request->file('image') , 'tax-payer-profile-images');

            $dto = new UserDTO(
                id: null,
                firstName: $request->firstName,
                lastName: $request->lastName,
                idCard: $idCardUrl,
                userName: $request->userName,
                password: $request->password,
                phone: $request->phone,
                image: $imageUrl,
                departmentID: 1,
                createdBy: null,
                role: UserRole::Tax_Payer,
            );

            $result = $useCase->execute($dto);

            return ApiResponse::created($result , 'تم إنشاء المكلف بنجاح.');
        }
        catch(\Throwable $e) {
            throw $e;
        }
    }


    public function show(string $id)
    {
        //
    }


    public function update(
        int $id,
        UpdateTaxPayerMobileRequest $request,
        UpdateTaxPayerMobileUseCase $useCase
    ) {
        try {
            $existingUser = $this->userRepository->findById($id);

            if (!$existingUser) {
                return ApiResponse::notFound(
                    [],
                    'المكلف مع ال ID [' . $id . '] غير موجود.'
                );
            }

            // ID Card
            $idCardUrl = $existingUser->idCard;

            if ($request->hasFile('idCard')) {
                $idCardUrl = $this->uploadFileService->uploadFile(
                    $request->file('idCard'),
                    'tax-payer-id-cards'
                );
            }

            // Image
            $imageUrl = $existingUser->image;
            if ($request->hasFile('image')) {
                $imageUrl = $this->uploadFileService->uploadFile(
                    $request->file('image'),
                    'tax-payer-profile-images'
                );
            }

            // DTO
            $dto = new UserDTO(
                id: $existingUser->id,
                firstName: $request->firstName ?? $existingUser->firstName,
                lastName: $request->lastName ?? $existingUser->lastName,
                idCard: $idCardUrl,
                userName: $request->userName ?? $existingUser->userName,
                password: $request->password ?? $existingUser->password,
                phone: $request->phone ?? $existingUser->phone,
                image: $imageUrl,
                departmentID: 1,
                createdBy: $existingUser->createdBy,
                role: $existingUser->role,
            );

            $updatedTaxPayer = $useCase->execute($dto);

            return ApiResponse::ok(
                $updatedTaxPayer,
                'تم تحديث بيانات المكلف بنجاح.'
            );

        } catch (\Throwable $e) {
            throw $e;
        }
    }


    public function destroy(string $id)
    {
        //
    }

    public function TaxPayerMobileLogin(LoginRequest $request , LoginUseCase $useCase)
    {

        try {

            $dto = new LoginDTO(
                userName: $request->userName,
                password: $request->password
            );

            $result = $useCase->execute($dto);
            $token = $result['token'];

            return ApiResponse::ok([

                'access_token' => $token,
                'user' => $result['user'] ?? null

            ], 'تم تسجيل الدخول بنجاح.');

        } catch (\Throwable $e) {

            return response()->json([
            'error' => $e->getMessage()
            ],401);
        }
    }

    public function TaxPayerMobileLogout(LogoutUseCase $useCase)
    {
        try{

            $token = request()->bearerToken();
            if(!$token){
                return ApiResponse::unauthorized([] , 'لم يتم إرسال التوكن.');
            }

            $useCase->execute($token);

            return ApiResponse::ok('تم تسجيل الخروج بنجاح.');

        } catch(\Throwable $e)
        {
            return response()->json([
                'error' => $e->getMessage()
            ] , 500);
        }
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
