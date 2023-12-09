<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class factureItem extends Model
{
    use HasFactory,SoftDeletes;

    public function facture(): BelongsTo
    {
        return $this->BelongsTo(facture::class);
    }


    public function productOption(): BelongsTo
    {
        return $this->BelongsTo(productOption::class);
    }
}
