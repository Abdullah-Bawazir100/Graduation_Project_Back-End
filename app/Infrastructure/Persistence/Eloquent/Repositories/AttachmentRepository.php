<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Attachment\Entities\Attachment;
use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;
use App\Domain\Department\Entities\Department;
use App\Domain\District\Entities\District;
use App\Domain\File\Entities\File;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\Region\Entities\Region;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\AttachmentFileModel;
use App\Infrastructure\Persistence\Eloquent\Models\FileModel;
use Override;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function create(Attachment $attachment): Attachment
    {
        $file = FileModel::query()->findOrFail($attachment->file->id);

        $attachmentModel = AttachmentFileModel::create([
            'title' => $attachment->title,
            'attachment_file' => $attachment->attachmentFile,
            'file_id' => $file->id,
        ]);

        $attachmentModel->load([
            'file.taxPayer',
            'file.department',
            'file.fileStatus',
            'file.activityType',
            'file.paymentType',
            'file.region',
            'file.district.region',
            'file.creator.department',
        ]);

        return $this->mapToDomain($attachmentModel);
    }

    public function update(int $id, Attachment $attachment): ?Attachment
    {
        $attachmentModel = AttachmentFileModel::with('file')->find($id);
        if(!$attachmentModel)
            return null;
        
        $attachmentModel->update([
            'title' => $attachment->title,
            'attachment_file' => $attachment->attachmentFile,
            'file_id' => $attachment->file->id,
        ]);

        return $this->mapToDomain($attachmentModel);
    }

    public function getAll()
    {
        $attachments = AttachmentFileModel::with('file')->get();
        return $attachments->map(function (AttachmentFileModel $model) {
            return $this->mapToDomain($model);
        });
    }

    #[Override]
    public function findById(int $id): ?Attachment
    {
        $attachment = AttachmentFileModel::with('file')->find($id);
        if(!$attachment)
            return null;

        return $this->mapToDomain($attachment);
    }

    public function delete(int $id)
    {
        AttachmentFileModel::findOrFail($id)->delete();
    }

    private function mapToDomain(AttachmentFileModel $model): Attachment
    {
        $fileModel = $model->file;

        $region = new Region(
            id: $fileModel->region->id,
            name: $fileModel->region->name,
        );

        $file = new File(
            id: $fileModel->id,
            taxNumber: $fileModel->tax_number,
            inventoryNumber: $fileModel->inventory_number,
            activityStartDate: $fileModel->activity_start_date,
            docsCount: $fileModel->docs_count,
            note: $fileModel->note,

            taxPayer: new TaxPayer(
                id: $fileModel->taxPayer->id,
                userId: $fileModel->taxPayer->user_id,
                tradeName: $fileModel->taxPayer->trade_name,
                commercialRecord: $fileModel->taxPayer->commercial_record,
                activityLicense: $fileModel->taxPayer->activity_license,
                tradePict: $fileModel->taxPayer->trade_pict,
                insuranceCard: $fileModel->taxPayer->insurance_card,
                propertyDocPict: $fileModel->taxPayer->property_doc_pict,
                fileType: $fileModel->taxPayer->file_type,
                source: $fileModel->taxPayer->source,
            ),

            department: new Department(
                id: $fileModel->department->id,
                name: $fileModel->department->name,
            ),

            fileStatus: new FileStatus(
                id: $fileModel->fileStatus->id,
                statusName: $fileModel->fileStatus->status_name,
                statusDescription: $fileModel->fileStatus->status_description,
            ),

            activityType: new Activity_Type(
                id: $fileModel->activityType->id,
                name: $fileModel->activityType->name,
            ),

            paymentType: new PaymentType(
                id: $fileModel->paymentType->id,
                name: $fileModel->paymentType->name,
                note: $fileModel->paymentType->note,
            ),

            region: $region,

            district: new District(
                id: $fileModel->district->id,
                name: $fileModel->district->name,
                region: $region,
            ),

            creator: $fileModel->creator
                ? new User(
                    id: $fileModel->creator->id,
                    firstName: $fileModel->creator->first_name,
                    lastName: $fileModel->creator->last_name,
                    idCard: $fileModel->creator->id_card,
                    userName: $fileModel->creator->user_name,
                    phone: $fileModel->creator->phone,
                    image: $fileModel->creator->image,
                    password: $fileModel->creator->password,
                    createdBy: $fileModel->creator->created_by,
                    department: new Department(
                        id: $fileModel->creator->department->id,
                        name: $fileModel->creator->department->name,
                    ),
                    role: $fileModel->creator->role,
                    mustChangePassword: $fileModel->creator->must_change_password,
                )
                : null,
        );

        return new Attachment(
            id: $model->id,
            title: $model->title,
            attachmentFile: $model->attachment_file,
            file: $file,
        );
    }
}
