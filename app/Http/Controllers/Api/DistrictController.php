<?php

namespace App\Http\Controllers\Api;

use App\Application\District\DTOs\DistrictDTOs;
use App\Application\District\UseCases\CreateDistrictUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\District\StoreDistrictRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{

    public function index()
    {
        //
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


    public function show(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
