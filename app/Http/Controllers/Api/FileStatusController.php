<?php

namespace App\Http\Controllers\Api;

use App\Application\FileStatus\DTOs\FileStatusDTOs;
use App\Application\FileStatus\UseCases\CreateFileStatusUseCase;
use App\Application\FileStatus\UseCases\DeleteFileStatusUseCase;
use App\Application\FileStatus\UseCases\ListFileStatusUseCase;
use App\Application\FileStatus\UseCases\UpdateFileStatusUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileStatus\StoreFileStatusRequest;
use App\Http\Requests\FileStatus\UpdateFileStatusRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class FileStatusController extends Controller
{

    public function index(ListFileStatusUseCase $useCase)
    {
        $fileStatus = $useCase->execute();
        return ApiResponse::ok(
            data: $fileStatus,
            message: 'تم جلب حالات الملفات بنجاح.'
        );
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


    public function update(int $id , UpdateFileStatusRequest $request ,
        UpdateFileStatusUseCase $useCase)
    {
        $dto = new FileStatusDTOs(
            statusName: $request->statusName,
            statusDescription: $request->statusDescription
        );

        $updatedFileStatus = $useCase->execute($dto , $id);
        return ApiResponse::ok(
            data: $updatedFileStatus,
            message: 'تم تحديث بيانات حالة الملف بنجاح.'
        );
    }


    public function destroy(int $id , DeleteFileStatusUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            [],
            message: "تم حذف حالة الملف مع ال ID [$id] بنجاح."
        );
    }
}
