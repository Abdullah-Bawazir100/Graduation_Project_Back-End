<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
use DateTime;

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
            if (!$authUser) return ApiResponse::unauthorized([] , "Only [Admin , Manager] can create users.");

            $actor = $this->convertToDomainUser($authUser);

            $dto = new SignUpDTO(
                firstName: $request->firstName,
                lastName: $request->lastName,
                departmentID: $request->departmentID,
            );

            $result = $this->signUpUseCase->execute($actor, $dto);
            return ApiResponse::created($result, 'User signed up successfully');

        }
        catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
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
            if (!$authUser) return ApiResponse::unauthorized();
            $domainUser = $this->convertToDomainUser($authUser);
            echo 'Here';

            $dto = new ResetPasswordDTO(
                newPassword: $request->new_password
            );

            $this->resetPasswordUseCase->execute($domainUser, $dto->newPassword);

            return ApiResponse::ok([], 'Password updated successfully');
        } catch(\Throwable $e) {
            throw $e;
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

    /** Admin / Manager creates user with full info */
    public function createUser(StoreUserRequest $request)
    {
        try {

            $authUser = Auth::user();
            if(!$authUser) {
                return ApiResponse::unauthorized();
            }

            $actor = $this->convertToDomainUser($authUser);

            $file = $request->file('idCard');
            $fileName = Str::uuid() . '.pdf';
            $path = $file->storeAs('id-cards' , $fileName , 'public');
            $fileUrl = asset(Storage::url($path));

            $dto = new UserDTO(
                id: null,
                firstName: $request->firstName,
                lastName: $request->lastName,
                dateOfBirth: $request->dateOfBirth ? new DateTime($request->dateOfBirth) : null,
                idCard: $fileUrl,
                userName: null,
                password: null,
                phone: $request->phone,
                departmentID: $request->departmentID,
                createdBy: $actor->id,
                role: $request->role,
                mustChangePassword: false
            );

            $result = $this->createUserUseCase->execute($actor , $dto);

            return ApiResponse::created($result , 'User Created Successfully.');
        }
        catch(\Throwable $e) {
            throw $e;
        }

    }

    /** Helper: Convert Laravel Auth user to Domain User */
    private function convertToDomainUser($authUser): DomainUser
    {
        return new DomainUser(
            id: $authUser->id,
            firstName: $authUser->firstName ?? '',
            lastName: $authUser->lastName ?? '',
            dateOfBirth: $authUser->dateOfBirth ? new DateTime($authUser->dateOfBirth) : null,
            idCard: $authUser->idCard ?? '',
            userName: $authUser->userName ?? '',
            phone: $authUser->phone ?? '',
            password: $authUser->password,
            createdBy: $authUser->createdBy ?? 0,
            department: new Department($authUser->departmentID ?? 0, ''),
            role: UserRole::from($authUser->role),
            mustChangePassword: $authUser->mustChangePassword ?? true
        );
    }
}
