<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class productOption extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function Warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productSize(): BelongsTo
    {
        return $this->belongsTo(productSize::class);
    }

    public function Motif(): BelongsTo
    {
        return $this->BelongsTo(Motif::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(orderProduct::class);
    }

    public function stockControls(): HasMany
    {
        return $this->hasMany(stockControl::class);
    }
}
