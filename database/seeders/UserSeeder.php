<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\RolesEnum;
use App\VedorStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
        ])->assignRole(RolesEnum::User->value);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
        ])->assignRole(RolesEnum::Admin->value);

        $vendorUser = User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor@gmail.com',
        ]);
        $vendorUser->assignRole(RolesEnum::Vendor->value);

        Vendor::factory()->create([
            'user_id' => $vendorUser->id,
            'status' => VedorStatusEnum::Approved,
            'store_name' => 'Vendor Store',
            'store_adress' => fake()->address(),
        ]);
    }
}
