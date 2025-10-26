<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
                 // Permissions
                $permissions = ['view_users', 'create_users', 'edit_users', 'delete_users',
                'view_permissions','edit_permissions','create_permissions','delete_permissions',
                'view_roles','edit_roles','create_roles','delete_roles',
                'view_products','edit_products','create_products','delete_products',
                'view_categories','edit_categories','create_categories','delete_categories',
                'view_inventories','edit_inventories', 'create_inventories','delete_inventories',
                'view_orders','edit_orders','create_orders','delete_orders',
                'view_distributors','edit_distributors','create_distributors','delete_distributors',
                'view_sales','edit_sales','create_sales','delete_sales',
                'view_retailers','edit_retailers','create_retailers','delete_retailers',
                'view_settings','edit_settings','create_settings','delete_settings'];

                foreach ($permissions as $perm) {
                    Permission::firstOrCreate(['name' => $perm]);
                }
        
                // Admin Role
                $adminRole = Role::firstOrCreate(['name' => 'admin']);
                $adminRole->permissions()->sync(Permission::all()->pluck('id'));
        
                // Subadmin Role
                $subadminRole = Role::firstOrCreate(['name' => 'subadmin']);
                $subadminRole->permissions()->sync(
                    Permission::whereIn('name', ['view_users', 'view_categories','view_products','view_orders','view_distributors'])->pluck('id')
                );




    }
}
