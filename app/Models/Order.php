<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    use SoftCascadeTrait;
    protected $softCascade = ['orderProducts'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(orderProduct::class);
    }

    public function Products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orderExtends(): HasMany
    {
        return $this->hasMany(orderExtend::class);
    }
}
