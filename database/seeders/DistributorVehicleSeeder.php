<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class DistributorVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `distributor_vehicles` (`id`, `distributor_id`, `two_wheeler`, `three_wheeler`, `four_wheeler`, `created_at`, `updated_at`) VALUES
(2, 8, NULL, NULL, 5, '2025-08-22 05:49:44', '2025-08-22 05:49:44');");
    }
}
