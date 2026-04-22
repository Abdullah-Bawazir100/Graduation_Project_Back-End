<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Department\UseCases\CreateDepartmentUseCase;
use App\Application\Department\UseCases\ListDepartmentUseCase;
use App\Application\Department\UseCases\ShowDepartmentUseCase;
use App\Application\Department\UseCases\UpdateDepartmentUseCase;
use App\Application\Department\UseCases\DeleteDepartmentUseCase;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Application\Department\DTOs\DepartmentDTO;
use App\Http\Responses\ApiResponse;

class DepartmentController extends Controller
{
    public function index(ListDepartmentUseCase $useCase)
    {
        $departments = $useCase->execute();

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
        $department = $useCase->execute($id);

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
        $dto = new DepartmentDTO(
            name: $request->validated('name')
        );

        $department = $useCase->execute($id, $dto);

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
}
