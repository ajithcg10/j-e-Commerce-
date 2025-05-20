<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                "name" => 'Electronics',
                "slug" => Str::slug('Electronics'),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'Fashion',
                "slug" => Str::slug('Fashion'),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'Home Appliances',
                "slug" => Str::slug('Home Appliances'),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'Sports & Fitness',
                "slug" => Str::slug('Sports & Fitness'),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'Books & Stationery',
                "slug" => Str::slug('Books & Stationery'),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'Health & Beauty',
                "slug" => Str::slug('Health & Beauty'),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ];

        DB::table('departments')->insert($departments);
    }
}
