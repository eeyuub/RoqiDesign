<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class factureItem extends Model
{
    use HasFactory;

    public function facture(): BelongsTo
    {
        return $this->BelongsTo(facture::class);
    }


    public function productOption(): BelongsTo
    {
        return $this->BelongsTo(productOption::class);
    }
}
