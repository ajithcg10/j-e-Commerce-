<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\VedorStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;


class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // Set manually in seeder
            'status' => VedorStatusEnum::Approved,
            'store_name' => $this->faker->company,
            'store_adress' => $this->faker->address,
        ];
    }
}
