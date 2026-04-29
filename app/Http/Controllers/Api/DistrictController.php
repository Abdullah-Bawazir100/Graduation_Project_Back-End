<?php

namespace App\Http\Controllers\Api;

use App\Application\District\DTOs\DistrictDTOs;
use App\Infrastructure\Persistence\Eloquent\Models\DistrictModel;
use App\Application\District\UseCases\CreateDistrictUseCase;
use App\Application\District\UseCases\DeleteDistrictUseCase;
use App\Application\District\UseCases\ListDistrictsUseCase;
use App\Application\District\UseCases\ShowDistrictUseCase;
use App\Application\District\UseCases\UpdateDistrictUseCase;
use App\Application\District\UseCases\CountDistrictsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\District\StoreDistrictRequest;
use App\Http\Requests\District\UpdateDistrictRequest;
use App\Http\Responses\ApiResponse;

class DistrictController extends Controller
{

    public function index(ListDistrictsUseCase $useCase)
    {
        $districts = $useCase->execute();
        return ApiResponse::ok(
            data: $districts,
            message: 'تم جلب الأحياء بنجاح.'
        );
    }


    public function store(StoreDistrictRequest $request , CreateDistrictUseCase $useCase)
    {
        $dto = new DistrictDTOs(
            name: $request->name,
            regionID: $request->regionID
        );

        $districtData = $useCase->execute($dto);

        return ApiResponse::created(
            data: $districtData,
            message: 'تم إنشاء حي جديد بنجاح.'
        );
    }


    public function show(ShowDistrictUseCase $useCase, int $id)
    {
        $district = $useCase->execute($id);

        return ApiResponse::ok(
            data: $district,
            message: 'تم جلب الحي بنجاح.'
        );
    }


    public function update(UpdateDistrictRequest $request, UpdateDistrictUseCase $useCase, int $id)
    {
        $dto = new DistrictDTOs(
            name: $request->name ?? null,
            regionID: $request->region_id ?? null
        );

        $district = $useCase->execute($id, $dto);

        return ApiResponse::ok(
            data: $district,
            message: 'تم تحديث الحي بنجاح.'
        );
    }


    public function destroy(DeleteDistrictUseCase $useCase, int $id)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            data: null,
            message: 'تم حذف الحي بنجاح.'
        );
    }

    public function getByRegion($regionId)
    {
        $districts = DistrictModel::where('region_id', $regionId)->get();

        return ApiResponse::ok(
            data: $districts,
            message: 'تم جلب الأحياء حسب المنطقة بنجاح.'
        );
    }

}
