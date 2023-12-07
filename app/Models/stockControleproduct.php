<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class stockControleproduct extends Model
{
    use HasFactory,SoftDeletes;

    public function stockControl(): BelongsTo
    {
        return $this->belongsTo(stockControl::class);
    }

    public function productOption(): belongsTo
    {
        return $this->belongsTo(productOption::class);
    }

}
