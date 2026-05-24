<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Request\Entities\TaxPayerRequest;
use App\Domain\Request\Enums\enRequestStatus;
use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Infrastructure\Persistence\Eloquent\Models\RequestModel;
use Override;

class TaxPayerRequestRepository implements TaxPayerRequestRepositoryInterface
{
    public function create(TaxPayerRequest $request): TaxPayerRequest
    {
        $model = RequestModel::create([
            'user_id' => $request->userId,
            'trade_name' => $request->tradeName,
            'commercial_record' => $request->commercialRecord,
            'activity_license' => $request->activityLicense,
            'trade_pict' => $request->tradePict,
            'insurance_card' => $request->insuranceCard,
            'property_doc_pict' => $request->propertyDocPict,
            'file_type' => $request->fileType->value,
            'articles_of_incorporation' => $request->articlesOfIncorporation,
            'govemor_license' => $request->govemorLicense,
            'partners_id_cards' => $request->partnersIDCards,
            'by_laws_copy' => $request->byLawsCopy,
            'status' => $request->requestStatus->value,
            'note' => $request->note,
            'source' => $request->source
        ]);

        $model->load('user');
        return $this->mapToDomain($model);
    }

    public function getPendingRequests(): array
    {
        $models = RequestModel::where('status', EnRequestStatus::Pending->value)
            ->with('user')
            ->get();

        return $models->map(fn (RequestModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function getConfirmedRequests(): array
    {
        $models = RequestModel::where('status', EnRequestStatus::Confirmed->value)
            ->with('user')
            ->get();

        return $models->map(fn (RequestModel $model) => $this->mapToDomain($model))->toArray();
    }
    public function getArchivedRequests(): array
    {
        $models = RequestModel::where('status', EnRequestStatus::Archived->value)
            ->with('user')
            ->get();

        return $models->map(fn (RequestModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function getAllRequests(): array
    {
        $models = RequestModel::with('user')->get();
        return $models->map(fn (RequestModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findRequestById(int $id)
    {
        $request = RequestModel::find($id);
        if(!$request)
        {
            return null;
        }
        return $this->mapToDomain($request);
    }

    public function acceptRequest(int $requestId): ?TaxPayerRequest
    {
        $request = RequestModel::with('user')->find($requestId);
        if(!$request)
        {
            return null;
        }

        $request->update([
            'status' => EnRequestStatus::Confirmed
        ]);
        $request->refresh();
        return $this->mapToDomain($request);

    }

    public function archiveRequest(int $requestId)
    {
        $request = RequestModel::with('user')->find($requestId);
        if(!$request)
        {
            return null;
        }

        $request->update([
            'status' => EnRequestStatus::Archived
        ]);
        $request->refresh();
        return $this->mapToDomain($request);
    }


    private function mapToDomain(RequestModel $model): TaxPayerRequest
    {
        return new TaxPayerRequest(
            id: $model->id,
            userId: $model->user_id,
            tradeName: $model->trade_name,
            commercialRecord: $model->commercial_record,
            activityLicense: $model->activity_license,
            tradePict: $model->trade_pict,
            insuranceCard: $model->insurance_card,
            propertyDocPict: $model->property_doc_pict,
            fileType: is_string($model->file_type) ? enFileType::from($model->file_type) : $model->file_type,
            articlesOfIncorporation: $model->articles_of_incorporation,
            govemorLicense: $model->govemor_license,
            partnersIDCards: $model->partners_id_cards,
            byLawsCopy: $model->by_laws_copy,
            requestStatus: is_string($model->status) ? EnRequestStatus::from($model->status) : $model->status,
            note: $model->note,
            source: $model->source
        );
    }
}
