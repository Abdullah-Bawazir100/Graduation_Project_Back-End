<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Department\UseCases\CreateDepartmentUseCase;
use App\Application\Department\UseCases\ListDepartmentUseCase;
use App\Application\Department\UseCases\ShowDepartmentUseCase;
use App\Application\Department\UseCases\UpdateDepartmentUseCase;
use App\Application\Department\UseCases\DeleteDepartmentUseCase;
use App\Http\Resources\Department\DepartmentResource;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Application\Department\DTOs\DepartmentDTO;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListDepartmentUseCase $useCase)
    {
        $departmentsData = $useCase->execute();
        return DepartmentResource::collection($departmentsData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request , CreateDepartmentUseCase $useCase)
    {
        $dto = new DepartmentDTO($request->validated()['name']);
        $departmentData = $useCase->execute($dto);
        return new DepartmentResource($departmentData);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id , ShowDepartmentUseCase $useCase)
    {
        $departmentData = $useCase->execute($id);
        return new DepartmentResource($departmentData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id , UpdateDepartmentRequest $request, UpdateDepartmentUseCase $useCase)
    {
        $dto = new DepartmentDTO($request->validated()['name']);
        $departmentData = $useCase->execute($id , $dto);
        return new DepartmentResource($departmentData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id , DeleteDepartmentUseCase $useCase)
    {
        $useCase->execute($id);
        return response()->json(['message' => 'Department deleted successfully']);
    }
}
