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
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

     /**
     * تحويل أي Entity أو VO إلى JSON-friendly array
     */
    private function transformDepartment(mixed $department): array
    {
        if (is_array($department)) {
            return array_map(fn($d) => $this->transformDepartment($d), $department);
        }

        if (is_object($department)) {
            $data = get_object_vars($department);
            foreach ($data as $key => $value) {
                // إذا كانت property VO تحتوي على method value()
                if (is_object($value) && method_exists($value, 'value')) {
                    $data[$key] = $value->value();
                } else if (is_object($value) || is_array($value)) {
                    $data[$key] = $this->transformDepartment($value);
                }
            }
            return $data;
        }

        return $department; // primitive
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListDepartmentUseCase $useCase)
    {
        $departmentsData = $this->transformDepartment($useCase->execute());
        return ApiResponse::ok($departmentsData , 'Departments retrived successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request , CreateDepartmentUseCase $useCase)
    {
        $dto = new DepartmentDTO($request->validated()['name']);
        $departmentData = $this->transformDepartment($useCase->execute($dto));
        return ApiResponse::created($departmentData , 'Department created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id , ShowDepartmentUseCase $useCase)
    {
        $departmentData = $this->transformDepartment($useCase->execute($id));
        
        if(!$departmentData) {
            return new ApiResponse(
                httpStatusCode: 404,
                errorMessage: 'Department not found'
            );
        }

        return ApiResponse::ok($departmentData , 'Department fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id , UpdateDepartmentRequest $request, UpdateDepartmentUseCase $useCase)
    {
        $dto = new DepartmentDTO($request->validated()['name']);
        $departmentData = $this->transformDepartment($useCase->execute($id , $dto));
        return ApiResponse::ok($departmentData , 'Department updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id , DeleteDepartmentUseCase $useCase)
    {
        $useCase->execute($id);
        return  ApiResponse::ok(null , 'Department deleted successfully');   
    }
}
