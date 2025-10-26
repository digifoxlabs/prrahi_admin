<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class DistributorGodownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `distributor_godowns` (`id`, `distributor_id`, `no_godown`, `godown_size`, `created_at`, `updated_at`) VALUES
(3, 8, 5, '20000', '2025-08-22 05:49:44', '2025-08-22 05:49:44'),
(4, 9, 2, '4000', '2025-09-01 03:33:26', '2025-09-01 03:33:26'),
(5, 10, 2, '6000', '2025-09-02 01:01:36', '2025-09-02 01:01:36');");
    }
}
