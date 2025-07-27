<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'starting_from',
        'until',
    ];

    // public function vendor()
    // {
    //     return $this->belongsTo(User::class, 'vendor_id');
    // }
}
