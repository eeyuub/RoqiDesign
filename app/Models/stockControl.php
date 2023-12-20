<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class stockControl extends Model
{
    use HasFactory;
    use SoftDeletes;

    use SoftCascadeTrait;
    protected $softCascade = ['stockControleproducts'];


    public function stockControleproducts(): HasMany
    {
        return $this->hasMany(stockControleproduct::class);
    }


}
