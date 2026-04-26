<?php

namespace App\Http\Controllers\Api;

use App\Application\Address\DTOs\AddressDTOs;
use App\Application\Address\UseCases\CreateAddressUseCase;
use App\Application\Address\UseCases\DeleteAddressUseCase;
use App\Application\Address\UseCases\ListAddressUseCase;
use App\Application\Address\UseCases\ShowAddressUseCase;
use App\Application\Address\UseCases\UpdateAddressUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Responses\ApiResponse;

class AddressController extends Controller
{

    public function index(ListAddressUseCase $useCase)
    {
        $addresses = $useCase->execute();
        return ApiResponse::ok(
            data: $addresses,
            message: 'تم جلب العناوين بنجاح.'
        );
    }


    public function store(StoreAddressRequest $request , CreateAddressUseCase $useCase)
    {
        $dto = new AddressDTOs(
            regionID: $request->regionID,
            districtID: $request->districtID
        );

        $addressData = $useCase->execute($dto);

        return ApiResponse::created(
            data: $addressData,
            message: 'تم إنشاء عنوان جديد بنجاح.'
        );
    }


    public function show(ShowAddressUseCase $useCase, int $id)
    {
        $address = $useCase->execute($id);

        return ApiResponse::ok(
            data: $address,
            message: "تم جلب بيانات العنوان مع ال ID [{$id}] بنجاح."
        );
    }


    public function update(int $id , UpdateAddressRequest $request, UpdateAddressUseCase $useCase)
    {
        $dto = new AddressDTOs(
            regionID: $request->regionID,
            districtID: $request->districtID
        );

        $address = $useCase->execute($id, $dto);

        return ApiResponse::ok(
            data: $address,
            message: "تم تحديث بيانات العنوان مع ال ID [{$id}] بنجاح."
        );
    }


    public function destroy(DeleteAddressUseCase $useCase, int $id)
    {
        $useCase->execute($id);

        return ApiResponse::ok(
            message: "تم حذف العنوان مع ال ID [{$id}] بنجاح."
        );
    }
}
