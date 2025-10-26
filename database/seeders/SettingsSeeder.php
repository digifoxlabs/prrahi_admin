<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `settings` (`id`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'products', 'low_stock_warning', '10', '2025-08-18 13:32:41', '2025-08-18 13:32:41'),
(2, 'distributors', 'max_outstanding', '0', '2025-08-18 13:32:41', '2025-08-18 13:32:41'),
(3, 'orders', 'free-dozen', '1', '2025-08-18 13:32:41', '2025-08-18 13:32:41');");
    }
}
