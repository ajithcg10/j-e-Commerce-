<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'variation_type_option_ids',
    ];

    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
 public function product()
{
    return $this->belongsTo(Product::class);
}

}
