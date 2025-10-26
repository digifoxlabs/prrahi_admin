<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class DistributorBank extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `distributor_banks` (`id`, `distributor_id`, `bank_name`, `branch_name`, `current_ac`, `ifsc`, `created_at`, `updated_at`) VALUES
(1, 1, 'Union Bank', 'SIXMILE', NULL, NULL, '2025-08-11 15:43:09', '2025-08-11 15:43:09'),
(2, 7, 'PNB', 'Tezpur Main Branch', '33060021000110', 'PUNB0330600', '2025-08-18 20:54:20', '2025-08-18 20:54:20'),
(5, 8, 'PNB', 'Dhallswar', NULL, NULL, '2025-08-22 05:49:44', '2025-08-22 05:49:44'),
(6, 9, 'Axis', 'Dharma Nagar', '920020041669434', 'UTIB0000708', '2025-09-01 03:33:26', '2025-09-01 03:33:26'),
(7, 10, 'PNB', 'Machmara', NULL, NULL, '2025-09-02 01:01:36', '2025-09-02 01:01:36');");
    }


}
