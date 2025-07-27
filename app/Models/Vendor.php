<?php

namespace App\Models;

use App\VedorStatusEnum;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

   public function scopeEligibleForPayout(EloquentBuilder $query) : EloquentBuilder
{
    return $query->where('status', VedorStatusEnum::Approved)
                 ->join('users', 'users.id', '=', 'vendors.user_id')
                 ->where('users.stripe_account_active', true);
}

    public  function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
