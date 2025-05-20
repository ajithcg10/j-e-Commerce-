<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap =false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'qunatity' =>$this->qunatity,
            'images' => $this->getFirstMediaUrl('images','small'),
            'images' => $this->getMedia('images')->map(function($image){
                return [
                    'id' => $image->id,
                    'thumb' => $image->getUrl('thumb'),
                    'small' => $image->getUrl('small'),
                    'large' => $image->getUrl('large'),
                ];
            }),
            'variationTypes' => $this->variationTypes->map(function($variationType){
                return [
                    'id' => $variationType->id,
                    'name' => $variationType->name,
                     'type' => $variationType->type,
                    
                    'options' => $variationType->options->map(function ($options){
                        return [
                            'id' => $options->id,
                            'name' => $options->name,
                            'image' => $options->getmedia('image')->map(function ($image){
                              return[  
                                'id' => $image->id ,
                                'thumb' => $image->getUrl('thumb'),
                                'large' => $image->getUrl('large'),
                                'small' => $image->getUrl('small'),
                            ];
                            })
                        ];
                    })
                ];
            }),
            'variations' => $this->Variations->map(function ($variation) {
    return [
        'id' => $variation->id,
        'variation_type_option_ids' => $variation->variation_type_option_ids, // decode it properly
        'quantity' => $variation->quantity,
        'price' => $variation->price,
    ];
}),


            'user' => [
                'id'=> $this->user->id,
                'name' => $this->user->name,

            ],
            'department' => [
                'id'=> $this->user->id,
                'name' => $this->user->name,
                
            ]

        ];
    }
}
