<?php

namespace App\Http\Controllers\Api;

use App\Application\JobType\UseCases\UpdateJobTypeUseCase;
use App\Application\JobType\DTOs\JobTypeDTOs;
use App\Application\JobType\UseCases\CreateJobTypeUseCase;
use App\Application\JobType\UseCases\DeleteJobTypeUseCase;
use App\Application\JobType\UseCases\ListJobTypeUseCase;
use App\Application\JobType\UseCases\MoveTaxCollectorsToAnotherJobTypeUseCase;
use App\Application\JobType\UseCases\ShowJobTypeUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobType\StoreJobTypeRequest;
use App\Http\Requests\JobType\UpdateJobTypeRequest;
use App\Http\Requests\TaxCollector\MoveTaxCollectorsRequest;
use App\Http\Responses\ApiResponse;

class JobTypeController extends Controller
{

    public function index(ListJobTypeUseCase $useCase)
    {
        $jobsTypes = $useCase->execute();
        return ApiResponse::ok(
            data: $jobsTypes,
            message: 'تم جلب أنواع الوظائف بنجاح.'
        );
    }


    public function store(StoreJobTypeRequest $request , CreateJobTypeUseCase $useCase)
    {
        $dto = new JobTypeDTOs(
            name: $request->name
        );

        $department = $useCase->execute($dto);

        return ApiResponse::created(
            data: $department,
            message: 'تم إنشاء نوع وظيفة جديد بنجاح.'
        );
    }


    public function show(int $id , ShowJobTypeUseCase $useCase)
    {
        $jobType = $useCase->execute($id);
        return ApiResponse::ok(
            data: $jobType,
            message: 'تم جلب نوع وظيفة بنجاح.'
        );
    }


    public function update(int $id , UpdateJobTypeRequest $request , UpdateJobTypeUseCase $useCase)
    {
        $dto = new JobTypeDTOs(
            name: $request->validated('name')
        );

        $jobType = $useCase->execute($id, $dto);

        return ApiResponse::ok(
            data: $jobType,
            message: 'تم تحديث بيانات نوع الوظيفة بنجاح.'
        );
    }


    public function destroy(int $id , DeleteJobTypeUseCase $useCase)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            message: 'تم حذف نوع الوظيفة بنجاح.'
        );
    }

    public function moveTaxCollectorsToAnotherJobType(int $id ,
    MoveTaxCollectorsRequest $request , MoveTaxCollectorsToAnotherJobTypeUseCase $useCase)
    {
        $newJobTypeId = $request->newJobTypeId;
        $useCase->execute($id , $newJobTypeId);
        return ApiResponse::ok(
            data: null,
            message: 'تم نقل جميع المأمورين من نوع الوظيفة [' . $id . '] إلى نوع الوظيفة [' . $newJobTypeId . '] بنجاح.'
        );
    }
}
