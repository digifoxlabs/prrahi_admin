<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            DB::statement("INSERT INTO `categories` (`id`, `name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'PRATHANA 15 GRM', NULL, '2025-07-20 17:52:37', '2025-08-17 19:34:04'),
(2, 'PRATHANA 100 GRM', NULL, '2025-07-20 17:53:32', '2025-08-17 19:33:52'),
(3, 'PREMIUM 15 GM', NULL, '2025-07-20 17:53:54', '2025-08-17 19:28:20'),
(4, 'PREMIUM 90 GM', NULL, '2025-07-20 17:54:09', '2025-08-17 19:28:45'),
(5, 'AARADHYA 50 GM', NULL, '2025-07-20 17:54:35', '2025-08-17 19:29:06'),
(6, 'PREMIUM ZIPPER', NULL, '2025-07-20 17:54:54', '2025-08-17 19:29:27'),
(7, 'PREMIUM ZIPPER PACK 400 GM', NULL, '2025-07-20 17:55:35', '2025-09-01 05:04:42'),
(8, 'POPULAR POUCH 80 GM', NULL, '2025-07-20 17:55:58', '2025-08-17 19:30:20'),
(9, 'CUP SAMBRANI', NULL, '2025-07-20 17:56:24', '2025-08-17 19:30:41'),
(10, 'DHUP STICK 4\'\'', NULL, '2025-07-20 17:56:50', '2025-08-17 19:31:04'),
(14, 'PREMIUM ZIPPER 120 GM', NULL, '2025-07-27 16:27:59', '2025-08-17 19:35:57'),
(15, 'BRAHMOS', NULL, '2025-09-01 05:56:31', '2025-09-01 05:56:31');");
    }
}
