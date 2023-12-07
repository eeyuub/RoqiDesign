<?php

namespace App\Models;

use App\Observers\SupplierObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    /* public function deleteObserver()
    {
        if ($this->Products()->withTrashed()->count() > 0) {
            return true;
         }
         return false;
    } */



    public function Products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseNotes(): HasMany
    {
        return $this->hasMany(purchaseNote::class);
    }
}
