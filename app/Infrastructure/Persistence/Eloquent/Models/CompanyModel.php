<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyModel extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'tax_payer_id',
        'articles_of_incorporation',
        'govemor_license',
        'partners_id_cards',
    ];

    protected $casts = [
        'tax_payer_id' => 'integer',
    ];

    public function taxPayer(): BelongsTo
    {
        return $this->belongsTo(TaxPayerModel::class , 'tax_payer_id');
    }
}
