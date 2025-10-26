<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(DefaultUserSeeder::class);
        $this->call(StateDistrictSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(SalesSeeder::class);
        $this->call(InventoryTransactions::class);

        $this->call(DistributorSeeder::class);
        $this->call(DistributorCompanies::class);
        $this->call(DistributorBank::class);
        $this->call(DistributorGodownSeeder::class);
        $this->call(DistributorManpowerSeeder::class);
        $this->call(DistributorVehicleSeeder::class);
        $this->call(SettingsSeeder::class);


    }
}
