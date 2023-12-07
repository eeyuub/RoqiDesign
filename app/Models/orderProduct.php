<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class orderProduct extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function Order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productOption(): belongsTo
    {
        return $this->belongsTo(productOption::class);
    }

    /* public function Products(): HasMany
    {
        return $this->hasMany(Product::class);
    } */

}
