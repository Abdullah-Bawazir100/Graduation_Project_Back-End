<?php

namespace App\Http\Controllers\Api;

use App\Application\Region\DTOs\RegionDTOs;
use App\Application\Region\UseCases\CreateRegionUseCase;
use App\Application\Region\UseCases\DeleteRegionUseCase;
use App\Application\Region\UseCases\ListRegionsUseCase;
use App\Application\Region\UseCases\ShowRegionUseCase;
use App\Application\Region\UseCases\UpdateRegionUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Region\StoreRegionRequest;
use App\Http\Requests\Region\UpdateRegionRequest;
use App\Http\Responses\ApiResponse;

class RegionController extends Controller
{

    public function index(ListRegionsUseCase $useCase)
    {
        $regions = $useCase->execute();
        return ApiResponse::ok(
            data: $regions,
            message: 'Regions retrieved successfully'
        );
    }


    public function store(StoreRegionRequest $request , CreateRegionUseCase $useCase)
    {
        $dto = new RegionDTOs(
            name: $request->name
        );

        $regionData = $useCase->execute($dto);

        return ApiResponse::created(
            data: $regionData,
            message: 'تم إنشاء منطقة جديدة بنجاح.'
        );
    }


    public function show(int $id , ShowRegionUseCase $useCase)
    {
        $region = $useCase->execute($id);

        return ApiResponse::ok(
            data: $region,
            message: 'Region with ID [' . $id . '] fetched successfully.'
        );
    }


    public function update(int $id , UpdateRegionRequest $request , UpdateRegionUseCase $useCase)
    {
        $dto = new RegionDTOs(
            name: $request->name
        );

        $regionData = $useCase->execute($id , $dto);

        return ApiResponse::ok(
            data: $regionData,
            message: 'Region with ID [' . $id . '] updated successfully.'
        );
    }


    public function destroy(int $id , DeleteRegionUseCase $useCase)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            data: null,
            message: 'Region with ID [' . $id . '] deleted successfully'
        );
    }
}
