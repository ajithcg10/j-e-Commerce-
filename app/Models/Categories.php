<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categories extends Model
{
    public function parent() :BelongsTo{
        return $this->belongsTo(Categories::class ,'parent_id' );
    }
}
