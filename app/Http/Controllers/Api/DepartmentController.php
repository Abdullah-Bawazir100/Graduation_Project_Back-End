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
            message: 'Departments retrieved successfully'
        );
    }

    public function store(
        StoreDepartmentRequest $request,
        CreateDepartmentUseCase $useCase
    ) {
        $dto = new DepartmentDTO(
            name: $request->validated('name')
        );

        $department = $useCase->execute($dto);

        return ApiResponse::created(
            data: $department,
            message: 'Department created successfully'
        );
    }

    public function show(int $id, ShowDepartmentUseCase $useCase)
    {
        $department = $useCase->execute($id);

        if (!$department) {
            return new ApiResponse(
                httpStatusCode: 404,
                errorMessage: 'Department not found'
            );
        }

        return ApiResponse::ok(
            data: $department,
            message: 'Department fetched successfully'
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
            message: 'Department updated successfully'
        );
    }

    public function destroy(int $id, DeleteDepartmentUseCase $useCase)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            data: null,
            message: 'Department with ID [' . $id . '] deleted successfully'
        );
    }
}