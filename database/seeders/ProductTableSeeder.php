<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `products` (`id`, `parent_id`, `name`, `code`, `type`, `category_id`, `sub_category_id`, `unit`, `dozen_per_case`, `mrp_per_unit`, `ptr_per_dozen`, `ptd_per_dozen`, `weight_gm`, `size`, `has_free_qty`, `free_dozen_per_case`, `attributes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Popular Pouch', NULL, 'variable', 8, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 08:45:22', '2025-07-20 08:45:22'),
(2, 1, NULL, '35POPULAR3IN1', 'variant', NULL, NULL, NULL, 30, 35.00, 205.00, 177.49, 80.00, '9', 1, NULL, '{\"size\": \"9\", \"fragrance\": \"3 in 1\"}', '2025-07-20 08:45:22', '2025-09-01 05:49:10'),
(3, NULL, 'Premium Zipper Pack', NULL, 'variable', 7, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 08:50:13', '2025-09-01 05:03:20'),
(4, 3, NULL, '150POPULAR4IN1', 'variant', NULL, NULL, NULL, 15, 150.00, 1155.00, 1000.00, 400.00, '9', 1, NULL, '{\"size\": \"9\", \"fragrance\": \"4 in 1\"}', '2025-07-20 08:50:13', '2025-09-01 05:43:41'),
(5, NULL, 'Premium Zipper 120gm', NULL, 'variable', 6, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 08:53:05', '2025-09-01 05:38:05'),
(6, 5, NULL, '70PREMIUMSAND', 'variant', NULL, NULL, NULL, 15, 70.00, 426.00, 368.83, 120.00, '9', 1, NULL, '{\"size\": \"9\", \"fragrance\": \"Sandal\"}', '2025-07-20 08:53:05', '2025-09-01 05:42:52'),
(7, 5, NULL, '70PREMIUAGNI', 'variant', NULL, NULL, NULL, 15, 70.00, 426.00, 368.83, 120.00, '9', 1, NULL, '{\"size\": \"9\", \"fragrance\": \"Agni\"}', '2025-07-20 08:53:05', '2025-09-01 05:42:52'),
(8, 5, NULL, '70PREMIUMKASTURI', 'variant', NULL, NULL, NULL, 15, 70.00, 426.00, 368.83, 120.00, '9', 1, NULL, '{\"size\": \"9\", \"fragrance\": \"Kasturi\"}', '2025-07-20 08:53:05', '2025-09-01 05:42:52'),
(9, NULL, 'Premium', NULL, 'variable', 4, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 09:01:25', '2025-07-20 09:01:25'),
(10, 9, NULL, '70PREMIUMSAND', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Sandal\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(11, 9, NULL, '70PREMIUMPATC', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Patcholi\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(12, 9, NULL, '70PREMIUMKAST', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Kasturi\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(13, 9, NULL, '70PREMIUM3 IN 1', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"3 in 1\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(14, 9, NULL, '70PREMIUM4 IN 1', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"4 in 1\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(15, 9, NULL, '70PREMIUMAGNI', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '0', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Agni\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(16, 9, NULL, '70PREMIUMJAL', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Jal\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(17, 9, NULL, '70PREMIUMAKASH', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Akash\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(18, 9, NULL, '70PREMIUMVAYU', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Vayu\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(19, 9, NULL, '70PREMIUMPRITHIVI', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Prithvi\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(20, 9, NULL, '70PREMIUMSHAKTIPEETH', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Shakti Peeth\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(21, 9, NULL, '70PREMIUMSIDDHIVINAYAK', 'variant', NULL, NULL, NULL, 12, 70.00, 480.00, 415.58, 90.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Siddhi Vinayak\"}', '2025-07-20 09:01:25', '2025-09-01 05:36:07'),
(22, NULL, 'Prarthana 100 GM', NULL, 'variable', 2, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 14:46:15', '2025-09-01 05:22:44'),
(23, 22, NULL, '60 PRARTHANAPIN', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Pineapple\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(24, 22, NULL, '60 PRARTHANARAJ', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Rajanigandha\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(25, 22, NULL, '60 PRARTHANAMOG', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Mogra\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(26, 22, NULL, '60PRARTHANAMANTHAN', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Manthan\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(27, 22, NULL, '60PRARTHANASPARSH', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Sparsh\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(28, 22, NULL, '60PRARTHANASMARAN', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Smaran\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(29, 22, NULL, '60 PRARTHANALEV', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 359.31, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Lavender\"}', '2025-07-20 14:46:15', '2025-09-01 05:34:52'),
(30, NULL, 'Aaradhya', NULL, 'variable', 5, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 15:20:10', '2025-07-20 15:20:10'),
(31, 30, NULL, '25 AARADHYACHAN', 'variant', NULL, NULL, NULL, 30, 25.00, 190.00, 164.50, 50.00, '8', 1, 1, '{\"size\": \"8\", \"fragrance\": \"Chandan\"}', '2025-07-20 15:20:10', '2025-09-01 05:18:31'),
(32, 30, NULL, '25 AARADHYAMOGR', 'variant', NULL, NULL, NULL, 30, 25.00, 190.00, 164.50, 50.00, '8', 1, 1, '{\"size\": \"8\", \"fragrance\": \"Mogra\"}', '2025-07-20 15:20:10', '2025-09-01 05:18:31'),
(33, 30, NULL, '25 AARADHYABELA', 'variant', NULL, NULL, NULL, 30, 25.00, 190.00, 164.50, 50.00, '8', 1, 1, '{\"size\": \"8\", \"fragrance\": \"Bela\"}', '2025-07-20 15:20:10', '2025-09-01 05:18:31'),
(34, 30, NULL, '25 AARADHYAKAST', 'variant', NULL, NULL, NULL, 30, 25.00, 190.00, 164.50, 50.00, '8', 1, 1, '{\"size\": \"8\", \"fragrance\": \"Kasturi\"}', '2025-07-20 15:20:10', '2025-09-01 05:18:31'),
(35, 30, NULL, '25 AARADHYAROSE', 'variant', NULL, NULL, NULL, 30, 25.00, 190.00, 164.50, 50.00, '8', 1, 1, '{\"size\": \"8\", \"fragrance\": \"Rose\"}', '2025-07-20 15:20:10', '2025-09-01 05:18:31'),
(36, NULL, 'Prarthana', NULL, 'variable', 1, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 16:13:54', '2025-07-20 16:14:39'),
(37, 36, NULL, '15 PRARTHANALEV', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Lavender\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(38, 36, NULL, '15 PRARTHANARAJ', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Rajanigandha\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(39, 36, NULL, '15 PRARTHANAPINE', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Pineapple\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(40, 36, NULL, '15 PRARTHANAMOG', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Mogra\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(41, 36, NULL, '15PRARTHANAMANTHAN', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Manthan\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(42, 36, NULL, '15PRARTHANASPARSH', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Sparsh\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(43, 36, NULL, '15PRARTHANAPRAKRITI', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Prakriti\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(44, 36, NULL, '15PRARTHANASMARAN', 'variant', NULL, NULL, NULL, 60, 15.00, 92.00, 79.65, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Smaran\"}', '2025-07-20 16:13:54', '2025-09-01 05:17:56'),
(45, NULL, 'PREMIUM', NULL, 'variable', 3, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-20 16:28:22', '2025-08-22 04:52:25'),
(46, 45, NULL, '15 PREMIUM SAND', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Sandal\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(47, 45, NULL, '15 PREMIUM PATC', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Patcholi\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(48, 45, NULL, '15 PREMIUMKAST', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Kasturi\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(49, 45, NULL, '15PREMIUM3 IN 1', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"3 in 1\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(50, 45, NULL, '15 PREMIUM4IN1', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"4 in 1\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(51, 45, NULL, '15 PREMIUMAGNI', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Agni\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(52, 45, NULL, '15 PREMIUMJAL', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Jal\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(53, 45, NULL, '15PREMIUMAKASH', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Akash\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(54, 45, NULL, '15 PREMIUMVAYU', 'variant', NULL, NULL, NULL, 160, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Vayu\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(55, 45, NULL, '15PREMIUMPRITHIVI', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Prithivi\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(56, 45, NULL, '15PREMIUMSHAKTIPEETH', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Shakti Peeth\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(57, 45, NULL, '15PREMIUMSIDDHIVINAYAK', 'variant', NULL, NULL, NULL, 60, 15.00, 96.00, 83.12, 15.00, '9', 1, 2, '{\"size\": \"9\", \"fragrance\": \"Siddhi Vinayak\"}', '2025-07-20 16:28:22', '2025-09-01 05:16:56'),
(58, NULL, 'Dhup Stick 4\"', '15DHUPSTICK', 'simple', 10, NULL, 'case', 32, 15.00, 205.00, 177.49, NULL, '8', 1, NULL, '{\"size\": \"4\"}', '2025-07-27 07:38:50', '2025-09-01 04:13:28'),
(59, NULL, 'Premium', NULL, 'variable', 14, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-07-27 10:59:55', '2025-07-27 10:59:55'),
(61, NULL, 'Cup Sambrani', '75CUP3in1', 'simple', 9, NULL, 'case', 12, 75.00, 650.00, 562.77, NULL, '4', 1, NULL, NULL, '2025-09-01 05:53:35', '2025-09-01 05:53:35'),
(62, NULL, 'Brahmos', NULL, 'simple', 15, NULL, 'case', 100, 15.00, 100.00, 86.58, NULL, '9', 1, NULL, NULL, '2025-09-01 05:58:10', '2025-09-01 05:58:10'),
(63, NULL, 'Prarthana 100 Gm', NULL, 'variable', 2, NULL, 'case', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '[]', '2025-09-01 06:06:22', '2025-09-01 06:06:22'),
(64, 63, NULL, '60PRARTHANAPRKRITI', 'variant', NULL, NULL, NULL, 12, 60.00, 415.00, 370.91, 100.00, '9', 1, 1, '{\"size\": \"9\", \"fragrance\": \"Prakriti\"}', '2025-09-01 06:06:22', '2025-09-01 06:23:56');");
    }
}
