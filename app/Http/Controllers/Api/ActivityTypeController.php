<?php

namespace App\Http\Controllers\Api;

use App\Application\Activity_Type\DTOs\ActivityTypeDTOs;
use App\Application\Activity_Type\UseCases\CreateActivityTypeUseCase;
use App\Application\Activity_Type\UseCases\DeleteActivityTypeUseCase;
use App\Application\Activity_Type\UseCases\ListActivityTypesUseCase;
use App\Application\Activity_Type\UseCases\ShowActivityTypeUseCase;
use App\Application\Activity_Type\UseCases\UpdateActivityTypeUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityType\StoreActivityTypeRequest;
use App\Http\Requests\ActivityType\UpdateActivityTypeRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{

    public function index(ListActivityTypesUseCase $useCase)
    {
        $activitiesTypes = $useCase->execute();
        return ApiResponse::ok(
            data: $activitiesTypes,
            message: 'Activities Types retrieved successfully'
        );
    }


    public function store(StoreActivityTypeRequest $request , CreateActivityTypeUseCase $useCase)
    {
        $dto = new ActivityTypeDTOs(
            name: $request->name
        );

        $activityType = $useCase->execute($dto);

        return ApiResponse::created(
            data: $activityType,
            message: 'Activity Type created successfully>'
        );
    }


    public function show(int $id , ShowActivityTypeUseCase $useCase)
    {
        $activityType = $useCase->execute($id);

        return ApiResponse::ok(
            data: $activityType,
            message: 'Activity Type with ID [' . $id . '] fetched successfully.'
        );
    }


    public function update(
        int $id ,
        UpdateActivityTypeRequest $request ,
        UpdateActivityTypeUseCase $useCase
        )
    {
        $dto = new ActivityTypeDTOs(
            name: $request->validated('name')
        );

        $activityType = $useCase->execute($id , $dto);

        return ApiResponse::ok(
            data: $activityType,
            message: 'Activity Type with ID [' . $id . '] updated successfully.'
        );
    }


    public function destroy(int $id , DeleteActivityTypeUseCase $useCase)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            data: null,
            message: 'Activity Type with ID [' . $id . '] deleted successfully'
        );
    }
}
