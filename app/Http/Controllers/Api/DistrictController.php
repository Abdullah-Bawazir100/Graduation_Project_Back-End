<?php

namespace App\Http\Controllers\Api;

use App\Application\District\DTOs\DistrictDTOs;
use App\Application\District\UseCases\CreateDistrictUseCase;
use App\Application\District\UseCases\DeleteDistrictUseCase;
use App\Application\District\UseCases\ListDistrictsUseCase;
use App\Application\District\UseCases\ShowDistrictUseCase;
use App\Application\District\UseCases\UpdateDistrictUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\District\UpdateDistrictRequest;
use App\Http\Requests\District\StoreDistrictRequest;
use App\Http\Responses\ApiResponse;

class DistrictController extends Controller
{

    public function index(ListDistrictsUseCase $useCase)
    {
        $districts = $useCase->execute();
        return ApiResponse::ok(
            data: $districts,
            message: "تم جلب الأحياء بنجاح."
        );
    }


    public function store(StoreDistrictRequest $request , CreateDistrictUseCase $useCase)
    {
        $dto = new DistrictDTOs(
            name: $request->name,
            regionID: $request->regionID,
        );

        $district = $useCase->execute($dto);

        return ApiResponse::created(
            data: $district,
            message: "تم إنشاء حي جديد بنجاح."
        );
    }


    public function show(int $id , ShowDistrictUseCase $useCase)
    {
        $districtData = $useCase->execute($id);
        return ApiResponse::ok(
            data: $districtData,
            message: "تم جلب الحي بنجاح."
        );
    }


    public function update(int $id , UpdateDistrictRequest $request , UpdateDistrictUseCase $useCase)
    {
        $dto = new DistrictDTOs(
            name: $request->name,
            regionID: $request->regionID
        );

        $districtData = $useCase->execute($id , $dto);

        return ApiResponse::ok(
            data: $districtData,
            message: "تم تحديث الحي مع ال ID [{$id}] بنجاح."
        );
    }


    public function destroy(int $id , DeleteDistrictUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            data: null,
            message: "تم حذف الحي مع ال ID [{$id}] بنجاح."
        );
    }
}
