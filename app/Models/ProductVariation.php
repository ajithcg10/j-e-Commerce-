<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariation extends Model implements HasMedia
{
      use InteractsWithMedia;
    protected $casts = [
        'variation_type_option_ids' => 'json',
    ];}
