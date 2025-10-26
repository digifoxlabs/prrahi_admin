# Terminal 1
php artisan serve

# Terminal 2
npm run dev


# For deployment
npm run build

# Custom script to run both commands Concurrently
First Install
npm install concurrently --save-dev

"scripts": {
  "dev": "vite",
  "serve": "php artisan serve",
  "hot": "concurrently \"php artisan serve\" \"npm run dev\""
}

Run CMD: npm run hot 

# Migrations and Models
php artisan make:model Role -m
php artisan make:model Permission -m
php artisan make:migration create_permission_role_table
php artisan make:migration create_role_user_table --create=role_user

php artisan make:seeder RolesAndPermissionsSeeder
php artisan make:seeder DefaultUserSeeder

## Controllers
php artisan make:controller Admin/AdminAuthController
php artisan make:controller Admin/DashboardController

php artisan make:controller Admin/PermissionController --resource 
php artisan make:controller Admin/RoleController --resource 
php artisan make:controller Admin/UserController --resource 


## Auth Scafolding for Admin and Clients

--> Create Laravel Guards in config/auth.php
  Create Guards and Providers

## Middleware to Check Permission
php artisan make:middleware CheckPermission

in app/Http/Middleware/CheckPermission.php

    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::guard('admin')->user();

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }

  -->register in Kernnel
    'permission' => \App\Http\Middleware\CheckPermission::class,


# In Routes
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->permission('view_rooms')->name('dashboard');


## Show with Toastr

# From Blade FIle
<button onclick="toastr.success('Operation completed successfully!')" 
        class="px-4 py-2 bg-green-600 text-white rounded">
    Show Success Toast
</button>

# From Controller
public function store(Request $request)
{
    // Your logic here
    
    return redirect()->back()->with('toast', [
        'type' => 'success',
        'message' => 'Record created successfully!'
    ]);
}
# In blade
@if(session('toast'))
    <script>
        toastr.{{ session('toast.type') }}('{{ session('toast.message') }}');
    </script>
@endif


# Export Excel
php artisan make:export PermissionsExport --model=Permission
php artisan make:export DistributorsExport --model=Distributor
php artisan make:export ProductsExport --model=Product
php artisan make:export SalesPersonExport --model=SalesPerson


php artisan make:model Category -mcr
php artisan make:model Fragrance -mcr
php artisan make:model Product -mcr
php artisan make:migration create_fragrance_product_table

