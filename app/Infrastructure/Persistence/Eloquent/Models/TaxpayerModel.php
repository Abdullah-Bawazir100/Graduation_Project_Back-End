<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Models\User;

class TaxPayerModel extends Model
{
    use HasFactory;

    protected $table = 'tax_payers';

    protected $fillable = [
        'user_id',
        'commercial_record',
        'activity_license',
        'trade_pict',
        'insurance_card',
        'property_doc_pict',
        'file_type'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'file_type' => enFileType::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
