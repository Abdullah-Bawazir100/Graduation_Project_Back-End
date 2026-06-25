<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Department\UseCases\CreateDepartmentUseCase;
use App\Application\Department\UseCases\ListDepartmentUseCase;
use App\Application\Department\UseCases\ShowDepartmentUseCase;
use App\Application\Department\UseCases\UpdateDepartmentUseCase;
use App\Application\Department\UseCases\DeleteDepartmentUseCase;
use App\Application\Department\UseCases\CountDepartmentsUseCase;
use App\Application\Department\UseCases\MoveUsersUseCase;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Requests\Department\MoveUsersRequest;
use App\Application\Department\DTOs\DepartmentDTO;
use App\Http\Responses\ApiResponse;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\Department\Entities\Department;
use App\Domain\User\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index(ListDepartmentUseCase $useCase)
    {
        $actor = $this->getActor();
        $departments = $useCase->execute($actor);

        return ApiResponse::ok(
            data: $departments,
            message: 'تم جلب الأقسام بنجاح.'
        );
    }

    public function store(StoreDepartmentRequest $request, CreateDepartmentUseCase $useCase)
    {
        $dto = new DepartmentDTO(
            name: $request->name
        );

        $department = $useCase->execute($dto);

        return ApiResponse::created(
            data: $department,
            message: 'تم إنشاء قسم بنجاح.'
        );
    }

    public function show(int $id, ShowDepartmentUseCase $useCase)
    {
        $actor = $this->getActor();
        $department = $useCase->execute($actor , $id);

        return ApiResponse::ok(
            data: $department,
            message: 'تم جلب بيانات القسم بنجاح.'
        );
    }

    public function update(
        int $id,
        UpdateDepartmentRequest $request,
        UpdateDepartmentUseCase $useCase
    ) {
        $actor = $this->getActor();

        $dto = new DepartmentDTO(
            name: $request->validated('name')
        );

        $department = $useCase->execute($actor, $id, $dto);

        return ApiResponse::ok(
            data: $department,
            message: 'تم تحديث بيانات القسم بنجاح.'
        );
    }

    public function destroy(int $id, DeleteDepartmentUseCase $useCase)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            data: null,
            message: 'تم حذف القسم مع ال ID [' . $id . '] بنجاح.'
        );
    }


    public function moveUsersToAnotherDepartment(
        int $id,
        MoveUsersRequest $request,
        MoveUsersUseCase $useCase
    ) {
        $actor = $this->getActor();
        $newDepartmentId = $request->departmentID;

        $useCase->execute($actor , $id, $newDepartmentId);

        return ApiResponse::ok(
            data: null,
            message: 'تم نقل جميع المستخدمين من القسم [' . $id . '] إلى القسم [' . $newDepartmentId . '] بنجاح.'
        );
    }

    private function getActor(): DomainUser
    {
        $authUser = Auth::user() ?? throw new \Illuminate\Auth\AuthenticationException();

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
