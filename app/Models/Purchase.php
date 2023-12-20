<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;

    use SoftCascadeTrait;
    protected $softCascade = ['purchaseProducts'];

    public function Supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseProducts(): HasMany
    {
        return $this->hasMany(purchaseProduct::class);
    }

    public function Products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
