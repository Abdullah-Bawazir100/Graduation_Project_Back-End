<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\User\UseCases\UpdateUserUseCase;
use App\Application\User\UseCases\DeleteUserUseCase;
use App\Application\User\UseCases\GetAllUsersUseCase;
use App\Application\User\UseCases\FindUserByIdUseCase;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\DTOs\UserResponseDTO;
use App\Application\User\Services\UploadFileService;
use Illuminate\Support\Facades\Auth;
use App\Domain\User\Entities\User as DomainUser;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\User\UpdateUserRequest;
use App\Domain\Department\Entities\Department;
use App\Domain\User\Enums\UserRole;
use Illuminate\Auth\AuthenticationException;
use DomainException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UpdateUserUseCase $updateUser,
        private DeleteUserUseCase $deleteUser,
        private GetAllUsersUseCase $getUsers,
        private FindUserByIdUseCase $findUser,
        private UploadFileService $uploadFile
    ) {}

   // GET /users
    public function index(Request $request): ApiResponse
    {
        $actor = $this->getActor();
        $search = $request->query('search');

        $users = $this->getUsers->execute($actor, $search);
        return ApiResponse::ok($users, 'تم جلب المستخدمين بنجاح.');
    }

    // GET /users/{id}
    public function show(int $id): ApiResponse
    {
        /** @var UserResponseDTO|null $user */
        $actor = $this->getActor();

        $userData = $this->findUser->execute($actor , $id);
        if(!$userData) {
            return ApiResponse::notFound([] , 'المستخدم مع ال ID [' . $id . '] غير موجود.');
        }

        return ApiResponse::ok($userData, 'تم جلب بيانات المستخدم بنجاح.');
    }

    // PUT /users/{id}
    public function update(UpdateUserRequest $request, int $id): ApiResponse
    {
        $actor = $this->getActor();

        $existingUser = $this->findUser->execute($actor , $id);

        if (!$existingUser) {
            return ApiResponse::notFound([] , 'المستخدم مع ال ID [' . $id . '] غير موجود.');
        }

        $firstName = $request->firstName ?? $existingUser->firstName;
        $lastName = $request->lastName ?? $existingUser->lastName;

        $idCardUrl = $existingUser->idCard;
        if($request->hasFile('idCard')){
            $idCardUrl = $this->uploadFile->uploadFile($request->file('idCard') , 'id-cards');
        }

        $imageUrl = $existingUser->image;
        if ($request->hasFile('image')) {
            $imageUrl = $this->uploadFile->uploadFile(
                $request->file('image'),
                'profile-images'
            );
        }

        $roleInput = $request->input('role');
        $role = $roleInput !== null ? UserRole::from($roleInput) : UserRole::from($existingUser->role);

        $dto = new UserDTO(
            id: $id,
            firstName: $firstName,
            lastName: $lastName,
            idCard: $idCardUrl,
            userName: $request->userName ?? $existingUser->userName,
            password: $request->password ?? null,
            phone: $request->phone ?? $existingUser->phone,
            image: $imageUrl,
            departmentID: (int)($request->departmentID ?? $existingUser->departmentID),
            createdBy: $existingUser->createdBy,
            role: $role
        );

        $user = $this->updateUser->execute($actor, $id, $dto);

        return ApiResponse::ok($user, 'تم تحديث بيانات المستخدم بنجاح.');
    }

    // DELETE /users/{id}
    public function destroy(int $id): ApiResponse
    {
        $actor = $this->getActor();

        $existingUser = $this->findUser->execute($actor,$id);
        if(!$existingUser)
        {
            return ApiResponse::notFound([] , 'المستخدم مع ال ID [' . $id . '] غير موجود.');

        }

        $this->deleteUser->execute($actor, $id);
        return ApiResponse::ok([], 'تم حذف المستخدم مع ال ID [' . $id . '] بنجاح.');
    }

    private function getActor(): DomainUser
    {
        $authUser = Auth::user() ?? throw new AuthenticationException();

        $department = new Department(
            id: $authUser->department_id,
            name: $authUser->department?->name ?? ''
        );

        return new DomainUser(
            id: $authUser->id,
            firstName: $authUser->first_name,
            lastName: $authUser->last_name,
            idCard: $authUser->id_card,
            userName: $authUser->user_name,
            phone: $authUser->phone,
            image: $authUser->image,
            password: $authUser->password,
            createdBy: $authUser->created_by,
            department: $department,
            role: $authUser->role,
            mustChangePassword: $authUser->must_change_password,
        );
    }
}
