<?php

namespace App\Models;

use App\ProductStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $casts = [
        'variations' => 'array',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100);

        $this->addMediaConversion('small')
            ->width(480);

        $this->addMediaConversion('large')
            ->width(1200);
    }

    public function scopeForVendor(Builder $query): Builder
    {
        return $query->where('created_by', Auth::user()->id);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ProductStatusEnum::Published);
    }

    public function scopeForwebsite(Builder $query): Builder
    {
        return $query->published();
    }

    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categories()
    {
        return $this->belongsTo(Categories::class, 'categorey_id');
    }

    public function variationTypes(): HasMany
    {
        return $this->hasMany(VariationType::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function getPriceForOptions(array $optionIds = [])
    {
        $optionIds = array_values($optionIds);
        sort($optionIds);

        $variations = $this->variations()->get(); // <- force load variations

        foreach ($variations as $variation) {
            $val = $variation->toArray();
            $ids = [];
            foreach ($variation->variation_type_option_ids as $item) {
                $ids[] = $item['id'];
            }

            sort($val);

            if ($optionIds == $ids) {
                return $variation->price !== null ? $variation->price : $this->price;
            }
        }

        return $this->price;
    }

    public function getImageForOptions(array $optionIds = []): ?string 
    {
        $optionIds = array_values($optionIds);
        sort($optionIds);

        $variations = $this->variations()->get(); // <- force load variations

        foreach ($variations as $variation) {
            $ids = [];
            foreach ($variation->variation_type_option_ids as $item) {
                $ids[] = $item['id'];
            }
            sort($ids);

            if ($optionIds == $ids) {
                return $variation->getFirstMediaUrl('images', 'samll') ?: null;
            }
        }

        $imag = $this->getFirstMediaUrl('images', 'small');

        if (!$optionIds && $imag) {
            return $imag;
        }

        if (!$optionIds && !$imag) {
            return null;
        }

        if ($optionIds) {
            $optionIds = array_values($optionIds);
            sort($optionIds);
            $options = VariationTypeOption::whereIn('id', $optionIds)->get();

            foreach ($options as $option) {
       
                $image = $option->getFirstMediaUrl('images', 'small');
                if (  $image ) {
                    return  $image ;
                }
            }

            return $this->getFirstMediaUrl('images', 'small');
        }

        return null;
    }
}
 