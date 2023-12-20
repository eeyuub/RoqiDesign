<?php

namespace App\Models;

use App\Enums\customerGender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'gender' => customerGender::class
    ];

    public function Orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function factures(): HasMany
    {
        return $this->hasMany(facture::class);
    }

    public function getAttributeNameGender()
    {
        return $this->name . 'ahahaah';
    }
}
