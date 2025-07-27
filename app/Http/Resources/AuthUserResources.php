<?php

namespace App\Http\Resources;

use App\Models\Vendor;
use App\VedorStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResources extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'permissions' => $this->getAllPermissions()->map(function ($permission) {
                return $permission->name;
            })->toArray(),
            'roles' => $this->getRoleNames(),
            'stripe_account_active' =>(bool)$this->stripe_account_active,
            'vendor' =>!$this->vendor ? null : [
                'status' => $this->vendor->status,
                'status_label' => VedorStatusEnum::from($this->vendor->status)->label(),
                'store_name' => $this->vendor->store_name,
                'store_address' => $this->vendor->store_address,
                'cover_image' => $this->vendor->cover_image,
            ],
            
        ];
    }   
}
