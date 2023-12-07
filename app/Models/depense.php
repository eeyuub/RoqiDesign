<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class depense extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function depenseItem(): BelongsTo
    {
        return $this->belongsTo(depenseItem::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(provider::class);
    }

}
