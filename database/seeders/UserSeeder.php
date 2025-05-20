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
            'name' => 'admin',
            'email' => 'admin@gmail.com',


        ])->assignRole(RolesEnum::Admin->value);
        
       $user = User::factory()->create([
            'name' => 'Vendor',
            'email' => 'Vendor@gmail.com',


       ]);
        $user->assignRole(RolesEnum::Vendor->value);

          Vendor::factory()->create([
            'user_id' => $user->id,
            'status' => VedorStatusEnum::Approved,
            'store_name' => 'Vendor Store',
            'store_adress' => fake()->address(),

       ]);
    }
}
