<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\User\UseCases\CreateUserUseCase;
use App\Application\User\UseCases\UpdateUserUseCase;
use App\Application\User\UseCases\DeleteUserUseCase;
use App\Application\User\UseCases\GetAllUsersUseCase;
use App\Application\User\UseCases\FindUserByIdUseCase;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\DTOs\UserResponseDTO;
use Illuminate\Support\Facades\Auth;
use App\Domain\User\Entities\User as DomainUser;
use App\Http\Responses\ApiResponse;
use App\Domain\User\Enums\UserRole;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Domain\Department\Entities\Department;
use Illuminate\Auth\AuthenticationException;

class UserController extends Controller
{
    public function __construct(
        private CreateUserUseCase $createUser,
        private UpdateUserUseCase $updateUser,
        private DeleteUserUseCase $deleteUser,
        private GetAllUsersUseCase $getUsers,
        private FindUserByIdUseCase $findUser
    ) {}

   // GET /users
    public function index(): ApiResponse
    {
        $users = $this->getUsers->execute(); // يجب أن يرجع array of UserResponseDTO
        return ApiResponse::ok($users, 'Users fetched successfully');
    }

    // GET /users/{id}
    public function show(int $id): ApiResponse
    {
        /** @var UserResponseDTO|null $user */

        $userData = $this->findUser->execute($id);
        if (!$userData) {
            return ApiResponse::notFound([], 'User with ID [' . $id . '] not found');
        }

        return ApiResponse::ok($userData, 'User fetched successfully');
    }

    // POST /users
    public function store(StoreUserRequest $request): ApiResponse
    {
        $actor = $this->getActor();

        $userName = $this->generateUserName($request->first_name, $request->last_name);

        $dto = new UserDTO(
            firstName: $request->first_name,
            lastName: $request->last_name,
            dateOfBirth: $request->date_of_birth,
            idCard: $request->file('id_card')->store('id_cards'),
            userName: $userName,
            phone: $request->phone,
            email: $request->email,
            password: $request->password,
            departmentId: (int)$request->department_id,
            role: $request->role
        );

        /** @var UserResponseDTO $user */
        $user = $this->createUser->execute($actor, $dto);

        return ApiResponse::created($user, 'User created successfully');
    }

    // PUT /users/{id}
    public function update(UpdateUserRequest $request, int $id): ApiResponse
    {
        $actor = $this->getActor();

        /** @var UserResponseDTO|null $existingUser */
        $existingUser = $this->findUser->execute($id);

        if (!$existingUser) {
            return ApiResponse::notFound([], 'User with ID [' . $id . '] not found');
        }

        $firstName = $request->first_name ?? $existingUser->firstName;
        $lastName = $request->last_name ?? $existingUser->lastName;
        $userName = $this->generateUserName($firstName, $lastName);

        $dto = new UserDTO(
            firstName: $firstName,
            lastName: $lastName,
            dateOfBirth: $request->date_of_birth ?? $existingUser->dateOfBirth,
            idCard: $request->file('id_card')?->store('id_cards') ?? $existingUser->idCard,
            userName: $userName,
            phone: $request->phone ?? $existingUser->phone,
            email: $request->email ?? $existingUser->email,
            password: $request->password ?? $existingUser->password,
            departmentId: (int)($request->department_id ?? $existingUser->departmentId),
            role: $request->role ?? $existingUser->role
        );

        /** @var UserResponseDTO $user */
        $user = $this->updateUser->execute($actor, $id, $dto);

        return ApiResponse::ok($user, 'User updated successfully');
    }

    // DELETE /users/{id}
    public function destroy(int $id): ApiResponse
    {
        $actor = $this->getActor();
        $this->deleteUser->execute($actor, $id);

        return ApiResponse::ok([], 'User deleted successfully');
    }

    // تحويل المستخدم المصادق عليه إلى Domain Entity
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
            dateOfBirth: new \DateTime($authUser->date_of_birth),
            idCard: $authUser->id_card,
            userName: $authUser->user_name,
            phone: $authUser->phone,
            email: $authUser->email,
            password: $authUser->password,
            createdBy: $authUser->created_by,
            department: $department,
            role: UserRole::from($authUser->role)
        );
    }

    // توليد userName تلقائيًا من الاسم الأول و الأخير
    private function generateUserName(string $firstName, string $lastName): string
    {
        return strtolower(trim($firstName . '.' . $lastName));
    }
}
