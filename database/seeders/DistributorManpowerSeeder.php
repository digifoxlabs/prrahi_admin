<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class DistributorManpowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `distributor_manpowers` (`id`, `distributor_id`, `sales`, `accounts`, `godown`, `created_at`, `updated_at`) VALUES
(3, 8, '5', '1', '2', '2025-08-22 05:49:44', '2025-08-22 05:49:44'),
(4, 9, '2', NULL, '1', '2025-09-01 03:33:26', '2025-09-01 03:33:26'),
(5, 10, '2', NULL, NULL, '2025-09-02 01:01:36', '2025-09-02 01:01:36');");
    }
}
