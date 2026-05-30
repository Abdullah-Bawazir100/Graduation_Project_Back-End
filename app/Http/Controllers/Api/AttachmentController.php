<?php

namespace App\Http\Controllers\Api;

use App\Application\Attachment\DTOs\AttachmentDTOs;
use App\Application\Attachment\UseCases\CreateAttachmentUseCase;
use App\Application\Attachment\UseCases\DeleteAttachmentUseCase;
use App\Application\Attachment\UseCases\FindAttachmentByIdUseCase;
use App\Application\Attachment\UseCases\ListAttachmentsUseCase;
use App\Application\Attachment\UseCases\UpdateAttachmentUseCase;
use App\Application\User\Services\UploadFileService;
use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachmentRequest;
use App\Http\Requests\Attachment\UpdateAttachmentRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService,
        private AttachmentRepositoryInterface  $attachment_repository,
    )
    {
    }

    public function index(ListAttachmentsUseCase $useCase)
    {
        $attachments = $useCase->execute();
        return ApiResponse::ok(
            data: $attachments,
            message: 'تم جلب مرفقات الملفات بنجاح.'
        );
    }


    public function store(StoreAttachmentRequest $request , CreateAttachmentUseCase $useCase)
    {
        $actor = Auth::user();
        $attachmentFileUrl = null;
        if($request->hasFile('attachmentFile')){
            $attachmentFileUrl = $this->uploadFileService->uploadFile($request->file('attachmentFile') , 'attachment-file-records');
        }

        $dto = new AttachmentDTOs(
            id: null,
            title: $request->title,
            attachmentFile: $attachmentFileUrl,
            fileId: $request->fileId,
        );

        $createdAttachment = $useCase->execute($dto , $actor->id);
        return ApiResponse::created(
            data: $createdAttachment,
            message: "تم إنشاء مرفق للملف بنجاح."
        );
    }


    public function show(int $id , FindAttachmentByIdUseCase $useCase)
    {
        $attachment = $useCase->execute($id);
        return ApiResponse::ok(
            data: $attachment,
            message: "تم جلب مرفق الملف مع ال ID [$id] بنجاح."
        );
    }


    public function update(int $id , UpdateAttachmentRequest $request ,
    UpdateAttachmentUseCase $useCase)
    {
        $attachment = $this->attachment_repository->findById($id);
        if(!$attachment)
        {
            return ApiResponse::notFound(
                message: "المرفق مع ال ID [$id] غير موجود."
            );
        }

        $attachmentFileUrl = null;
        if($request->hasFile('attachmentFile')){
            $attachmentFileUrl = $this->uploadFileService->uploadFile($request->file('attachmentFile') , 'attachment-file-records');
        }

        $dto = new AttachmentDTOs(
            id: $attachment->id,
            title: $request->title,
            attachmentFile: $attachmentFileUrl,
            fileId: $attachment->file->id
        );

        $updatedAttachment = $useCase->execute($id , $dto);
        return ApiResponse::ok(
            data: $updatedAttachment,
            message: "تم تحديث مرفق الملف مع ال ID [$id] بنجاح."
        );
    }


    public function destroy(int $id , DeleteAttachmentUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            message: "تم حذف مرفق الملف مع ال ID [$id] بنجاح."
        );
    }
}
