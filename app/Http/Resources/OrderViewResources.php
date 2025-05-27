<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderViewResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          
 
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total' => $this->total_price,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'vendorUser' => new VendorUsersResources($this->vendorUser),
            'orderItems' => $this->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'variation_type_option_ids' => $item->variation_type_option_ids,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'description' => $item->product->description,
                        'image' => $item->product->getImageForOptions(
                            is_array($item->variation_type_option_ids)
                                ? $item->variation_type_option_ids
                                : json_decode($item->variation_type_option_ids, true) ?? []
                            ),

                        'price' => $item->product->price,
                    ]
                ];
              
            }),
        ];
    }
}
