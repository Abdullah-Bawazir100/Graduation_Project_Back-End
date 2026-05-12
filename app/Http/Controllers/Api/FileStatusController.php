<?php

namespace App\Http\Controllers\Api;

use App\Application\FileStatus\DTOs\FileStatusDTOs;
use App\Application\FileStatus\UseCases\CreateFileStatusUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileStatus\StoreFileStatusRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class FileStatusController extends Controller
{

    public function index()
    {
        //
    }


    public function store(StoreFileStatusRequest $request , CreateFileStatusUseCase $useCase)
    {
        $dto = new FileStatusDTOs(
            statusName: $request->statusName,
            statusDescription: $request->statusDescription
        );

        $result = $useCase->execute($dto);

        return ApiResponse::created(
            data: $result,
            message: 'تم إنشاء حالة ملف جديدة بنجاح.'
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
