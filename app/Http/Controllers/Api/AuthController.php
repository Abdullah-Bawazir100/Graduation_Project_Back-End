<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Application\User\DTOs\SignUpDTO;
use App\Application\User\DTOs\LoginDTO;
use App\Application\User\DTOs\ResetPasswordDTO;
use App\Application\User\DTOs\UserDTO;

use App\Application\User\UseCases\ChangePasswordUseCase;
use App\Application\User\UseCases\CompleteProfileUseCase;
use App\Application\User\UseCases\SignUpUseCase;
use App\Application\User\UseCases\LoginUseCase;
use App\Application\User\UseCases\CreateUserUseCase;

use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Enums\UserRole;
use App\Domain\Department\Entities\Department;

use App\Http\Requests\User\SignUpRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\CompleteProfileRequest;

use App\Http\Responses\ApiResponse;


class AuthController extends Controller
{
    public function __construct(
        private SignUpUseCase $signUpUseCase,
        private LoginUseCase $loginUseCase,
        private ChangePasswordUseCase $resetPasswordUseCase,
        private CompleteProfileUseCase $completeProfileUseCase,
        private CreateUserUseCase $createUserUseCase
    ) {}

    /** Admin / Manager signs up a user */
    public function signUp(SignUpRequest $request)
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) return ApiResponse::unauthorized();

            $actor = $this->convertToDomainUser($authUser);

            $dto = new SignUpDTO(
                firstName: $request->first_name,
                lastName: $request->last_name,
                departmentID: $request->department_id
            );

            $result = $this->signUpUseCase->execute($actor, $dto);

            return ApiResponse::created($result, 'User signed up successfully');
        } catch (\Throwable $e) {
            return ApiResponse::unprocessable([], $e->getMessage());
        }
    }

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
                'user' => $result['user']

            ], 'Login successful');

        } catch (\Throwable $e) {

            return ApiResponse::unauthorized([], $e->getMessage());
        }
    }

    /** Reset Password */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) return ApiResponse::unauthorized();

            $domainUser = $this->convertToDomainUser($authUser);

            $dto = new ResetPasswordDTO(
                newPassword: $request->new_password
            );

            $this->resetPasswordUseCase->execute($domainUser, $dto);

            return ApiResponse::ok([], 'Password updated successfully');
        } catch (\Throwable $e) {
            return ApiResponse::unprocessable([], $e->getMessage());
        }
    }

    public function completeProfile(CompleteProfileRequest $request)
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return ApiResponse::unauthorized();
            }

            $domainUser = $this->convertToDomainUser($authUser);

            // إذا ما زال المستخدم مُلزم بتغيير الباسوورد
            if ($domainUser->mustChangePassword) {
                return ApiResponse::forbidden([], 'You must change your password before completing your profile.');
            }

            // تحضير البيانات لإكمال البروفايل
            $profileData = [
                'dateOfBirth' => $request->date_of_birth ?? null,
                'idCard' => $request->id_card ?? null,
                'phone' => $request->phone ?? null,
            ];

            $updatedUser = $this->completeProfileUseCase->execute($domainUser, $profileData);

            return ApiResponse::ok($updatedUser, 'Profile completed successfully');
        } catch (\Throwable $e) {
            return ApiResponse::unprocessable([], $e->getMessage());
        }
    }

    /** Admin / Manager creates user with full info */
    public function createUser(StoreUserRequest $request)
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) return ApiResponse::unauthorized();

            $actor = $this->convertToDomainUser($authUser);

            $dto = new UserDTO(
                firstName: $request->first_name,
                lastName: $request->last_name,
                dateOfBirth: isset($request->date_of_birth) ? new \DateTime($request->date_of_birth) : null,
                idCard: $request->id_card ?? null,
                userName: $request->user_name ?? null,
                phone: $request->phone ?? null,
                departmentID: $request->department_id,
                role: $request->role ?? null
            );

            $result = $this->createUserUseCase->execute($actor, $dto);

            return ApiResponse::created($result, 'User created successfully');
        } catch (\Throwable $e) {
            return ApiResponse::unprocessable([], $e->getMessage());
        }
    }

    /** Helper: Convert Laravel Auth user to Domain User */
    private function convertToDomainUser($authUser): DomainUser
    {
        return new DomainUser(
            id: $authUser->id,
            firstName: $authUser->first_name,
            lastName: $authUser->last_name,
            dateOfBirth: $authUser->date_of_birth ? new \DateTime($authUser->date_of_birth) : null,
            idCard: $authUser->id_card ?? '',
            userName: $authUser->user_name,
            phone: $authUser->phone ?? '',
            password: $authUser->password,
            createdBy: $authUser->created_by ?? 0,
            department: new Department($authUser->department_id ?? 0, ''),
            role: UserRole::from($authUser->role),
            mustChangePassword: $authUser->must_change_password ?? true
        );
    }
}