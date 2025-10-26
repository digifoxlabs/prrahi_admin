<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

                // Create Admin User
                $admin = User::create([
                    'fname' => 'admin',
                    'lname' => 'admin',
                    'email' => 'admin@gmail.com',
                    'password' => bcrypt('password@123'), // Admin password
                    'mobile_number' => '9127022438',
                ]);
        
                // Create Subadmin User
                $subadmin = User::create([
                    'fname' => 'subadmin',
                    'lname' => 'subadmin',
                    'email' => 'subadmin@gmail.com',
                    'password' => bcrypt('password@123'), // Subadmin password
                    'mobile_number' => '9127022437',
                ]);
        
                // Assign Admin Role to the Admin User
                $adminRole = Role::where('name', 'admin')->first();
                $admin->roles()->attach($adminRole);
        
                // Assign Subadmin Role to the Subadmin User
                $subadminRole = Role::where('name', 'subadmin')->first();
                $subadmin->roles()->attach($subadminRole);



    }
}
