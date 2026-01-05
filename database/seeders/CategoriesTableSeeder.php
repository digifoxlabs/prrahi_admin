<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ This is important

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('categories')->delete();
        
        DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'PRATHANA 15 GRM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:46:56',
                'updated_at' => '2025-12-24 19:46:56',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'PRATHANA 100 GRM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:02',
                'updated_at' => '2025-12-24 19:47:02',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'PREMIUM 15 GM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:08',
                'updated_at' => '2025-12-24 19:47:08',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'PREMIUM 90 GM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:15',
                'updated_at' => '2025-12-24 19:47:15',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'AARADHYA 50 GM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:21',
                'updated_at' => '2025-12-24 19:47:21',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'PREMIUM ZIPPER',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:28',
                'updated_at' => '2025-12-24 19:47:28',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'PREMIUM ZIPPER PACK 400 GM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:35',
                'updated_at' => '2025-12-24 19:47:35',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'POPULAR POUCH 80 GM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:40',
                'updated_at' => '2025-12-24 19:47:40',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'CUP SAMBRANI',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:46',
                'updated_at' => '2025-12-24 19:47:46',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'DHUP STICK 4\'\'',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:52',
                'updated_at' => '2025-12-24 19:47:52',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'PREMIUM ZIPPER 120 GM',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:47:58',
                'updated_at' => '2025-12-24 19:47:58',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'BRAHMOS',
                'parent_id' => NULL,
                'created_at' => '2025-12-24 19:48:04',
                'updated_at' => '2025-12-24 19:48:04',
            ),
        ));
        
        
    }
}