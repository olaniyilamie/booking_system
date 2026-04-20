<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Space::insert([
            ['name' => 'Conference Room', 'description' => 'Large meeting room', 'capacity' => 20, 'hourly_rate' => 25.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Private Meeting Room', 'description' => 'Quiet private office', 'capacity' => 5, 'hourly_rate' => 15.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hot Desk', 'description' => 'Shared desk space', 'capacity' => 50, 'hourly_rate' => 5.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
