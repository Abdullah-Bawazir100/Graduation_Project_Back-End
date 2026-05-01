<?php

namespace App\Http\Controllers\Api;

use App\Application\User\Services\UploadFileService;
use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Support\Facades\Auth;

use App\Application\User\DTOs\LoginDTO;
use App\Application\User\DTOs\ResetPasswordDTO;
use App\Application\User\DTOs\UserDTO;
use App\Domain\User\Enums\UserRole;
use App\Application\User\UseCases\ChangePasswordUseCase;
use App\Application\User\UseCases\LoginUseCase;
use App\Application\User\UseCases\CreateUserUseCase;
use App\Application\User\UseCases\ForgotPasswordUseCase;
use App\Application\User\UseCases\LogoutUseCase;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\Department\Entities\Department;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\ForgotPasswordRequest;

use App\Http\Responses\ApiResponse;

class AuthController extends Controller
{
    public function __construct(
        private LoginUseCase $loginUseCase,
        private ChangePasswordUseCase $resetPasswordUseCase,
        private CreateUserUseCase $createUserUseCase,
        private UploadFileService $uploadFile,
        private LogoutUseCase $logoutUseCase
    ) {}

    /** Login user */
    public function login(LoginRequest $request)
    {

        try {

            $dto = new LoginDTO(
                userName: $request->userName,
                password: $request->password
            );

            $result = $this->loginUseCase->execute($dto);
            $token = $result['token'];

            return ApiResponse::ok([

                'access_token' => $token,
                'must_change_password' => $result['must_change_password'],
                'user' => $result['user'] ?? null

            ], 'تم تسجيل الدخول بنجاح.');

        } catch (\Throwable $e) {

            return response()->json([
            'error' => $e->getMessage()
            ],401);
        }
    }

    /** Reset Password */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) return ApiResponse::unauthorized([] , 'غير مصرح بتغيير كلمة المرور.');
            $domainUser = $this->convertToDomainUser($authUser);

            $dto = new ResetPasswordDTO(
                newPassword: $request->new_password
            );

            $this->resetPasswordUseCase->execute($domainUser, $dto->newPassword);

            return ApiResponse::ok([
                'user_id' => $authUser->id
            ], 'تم تحديث كلمة المرور بنجاح.');
        } catch(\Throwable $e) {
            throw $e;
        }
    }

    /*
    public function completeProfile(CompleteProfileRequest $request)
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return ApiResponse::unauthorized();
            }

            $domainUser = $this->convertToDomainUser($authUser);

            if($request->hasFile('idCard')) {
                $idCardPath = $request->file('idCard')->store('id_cards', 'public');
            }

            // Prepare the profile data for completion
            $profileData = [
                'dateOfBirth' => $request->dateOfBirth ?? null,
                'idCard' =>  $idCardPath ?? null,
                'phone' => $request->phone ?? null,
            ];

            $updatedUser = $this->completeProfileUseCase->execute($domainUser, $profileData);

            return ApiResponse::ok($updatedUser, 'Profile completed successfully');

        } catch(\Throwable $e) {
            throw $e;
        }
    }
    */


    /** Admin / Manager creates user with full info */
    public function createUser(StoreUserRequest $request)
    {
        try {

            $authUser = Auth::user();
            if(!$authUser) {
                return ApiResponse::unauthorized();
            }

            $actor = $this->convertToDomainUser($authUser);

            $idCardUrl = $this->uploadFile->uploadFile($request->file('idCard') , 'id-cards');
            $imageUrl = $this->uploadFile->uploadFile($request->file('image') , 'profile-images');

            $dto = new UserDTO(
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
                role: UserRole::from($request->role),
            );

            $result = $this->createUserUseCase->execute($actor , $dto);

            return ApiResponse::created($result , 'تم إنشاء المستخدم بنجاح.');
        }
        catch(\Throwable $e) {
            throw $e;
        }

    }

    public function forgetPassword(ForgotPasswordRequest $request , ForgotPasswordUseCase $useCase)
    {
        try {
            $userName = $request->userName;
            $phone = $request->phone;
            $new_password = $request->new_password;

            $useCase->execute($userName , $phone , $new_password);

            return ApiResponse::ok('تم إعادة تعيين كلمة المرور بنجاح.');

        } catch (DomainException $e) {
            return ApiResponse::badRequest([], $e->getMessage());
        } catch (\Throwable $e) {
            return ApiResponse::serverError([], 'حدث خطأ أثناء معالجة طلبك.');
        }
    }

    public function logout()
    {
        try{

            $token = request()->bearerToken();
            if(!$token){
                return ApiResponse::unauthorized([] , 'لم يتم إرسال التوكن.');
            }

            $this->logoutUseCase->execute($token);

            return ApiResponse::ok('تم تسجيل الخروج بنجاح.');

        } catch(\Throwable $e)
        {
            return response()->json([
                'error' => $e->getMessage()
            ] , 500);
        }
    }

    /** Helper: Convert Laravel Auth user to Domain User */
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
