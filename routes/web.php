<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DistributorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FragranceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\InventoryTransactionController;
use App\Http\Controllers\Admin\SalesPersonController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Distributor\DistAuthController;
use App\Http\Controllers\Sales\SalesAuthController;
use App\Http\Controllers\Admin\AdminRetailerController;
use App\Http\Controllers\Distributor\DashboardController as DistributorDashboardController;
use App\Http\Controllers\Distributor\DistRetailerController;
use App\Http\Controllers\Distributor\DistributorStockController;
use App\Http\Controllers\Distributor\RetailerSaleController;
use App\Http\Controllers\Distributor\DistOrderController;
use App\Http\Controllers\Distributor\DistributorInventoryLedgerController;
use App\Http\Controllers\OrderController;



use App\Http\Controllers\Sales\DashboardController as SalesDashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/alert', function () {
    return view('alert-test');
});

Route::get('/alert-lite', function () {
    return view('alert-test-lite');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin/pages/dashboard');
});

Route::get('/login', function () {
    return view('admin/login');
});

// Route::get('/sales/login', function () {
//     return view('sales/login');
// });

// Route::get('/distributor/login', function () {
//     return view('distributor/login');
// });

//Admin Login Routes
Route::prefix('admin')->name('admin.')->group(function(){

    Route::get('/login',[AdminAuthController::class,'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
      Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

});


//API Instruction Route
Route::get('/api/invoice', function () {
    return view('api.invoice_doc');
});
// Route::get('/api/invoice', function () {
//     return view('api/invoice_doc')->with(['title' => 'Invoice API Documentation']);
// });

//Distributor Login Routes
Route::prefix('distributor')->name('distributor.')->group(function(){

    Route::get('login',[DistAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DistAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [DistAuthController::class, 'logout'])->name('logout');
});

//Sales Login Routes
Route::prefix('sales')->name('sales.')->group(function(){

    Route::get('login',[SalesAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SalesAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [SalesAuthController::class, 'logout'])->name('logout');
});



//Authenticated Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    //Profile
     Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
     //Upload Profile
     Route::post('profile/upload', [DashboardController::class, 'uploadImage'])->name('profile.upload');
     Route::post('profile/remove-image', [DashboardController::class, 'removeImage'])->name('profile.removeImage');
     //Update profile
     Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
     Route::post('/profile/update-password', [DashboardController::class, 'updatePassword'])->name('profile.password.update');


     //Permissions
    Route::get('/permissions/export', [PermissionController::class, 'export'])->name('permissions.export');   
    Route::resource('permissions', PermissionController::class)->middleware('permission:view_permissions');

    //Roles
    Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
    Route::resource('roles', RoleController::class);

    //Users
    Route::post('/users/profile/upload', [UserController::class, 'uploadImage'])->name('users.profile.upload');
    Route::post('/users/profile/update-password', [UserController::class, 'updatePassword'])->name('users.profile.password.update');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');   
    Route::resource('users', UserController::class);
    Route::get('users/{userdata}/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign.roles');
    Route::post('users/{user}/assign-roles', [UserController::class, 'storeRoles'])->name('users.assign.roles.store');

    //Distributor
   Route::post('/distributors/profile/upload/{id}', [DistributorController::class, 'uploadImage'])->name('distributors.updateProfileImage');
   Route::post('/distributors/update-password', [DistributorController::class, 'updatePassword'])->name('distributors.updatePassword');


    // Password update
    Route::get('/distributors/export', [DistributorController::class, 'export'])->name('distributors.export');  
    Route::resource('distributors', DistributorController::class);


    Route::get('/categories/{id}/children', function ($id) {
    return \App\Models\Category::where('parent_id', $id)->get(['id', 'name']);
    })->name('categories.children');

    

    Route::resource('categories', CategoryController::class);

    Route::resource('fragrances', FragranceController::class);


    Route::get('products/{product}/add-variant', [ProductController::class, 'createVariant'])->name('products.add-variant');
    Route::post('products/{product}/store-variant', [ProductController::class, 'storeVariant'])->name('products.store-variant');

    Route::get('products/{product}/variants', [ProductController::class, 'variants'])->name('products.variants');

    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');  
    Route::resource('products', ProductController::class);

    Route::get('inventory/export', [InventoryTransactionController::class, 'export'])->name('inventory.export');
    Route::resource('inventory', InventoryTransactionController::class);


    Route::get('get-districts', [SalesPersonController::class, 'getDistricts'])->name('get-districts');


    Route::post('sales-persons/{salesPerson}/map-distributors', [SalesPersonController::class, 'mapDistributors'])
    ->name('sales-persons.mapDistributors');
    Route::post('sales-persons/{salesPerson}/unmap-distributor', [SalesPersonController::class, 'unmapDistributor'])
    ->name('sales-persons.unmapDistributor');

    Route::get('/sales-persons/export', [SalesPersonController::class, 'export'])->name('sales-persons.export');  
    // Profile photo upload
    Route::post('/sales-persons/{salesPerson}/profile/upload', [SalesPersonController::class, 'uploadProfile'])->name('sales-persons.profile.upload');
    // Password update
    Route::post('/sales-persons/update-password', [SalesPersonController::class, 'updatePassword'])->name('sales-persons.updatePassword');
    Route::resource('sales-persons', SalesPersonController::class);


    Route::resource('orders', AdminOrderController::class)->only(['index','create','edit','show','destroy']);;

    Route::post('orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
   // Route::delete('admin/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
           // Orders
        Route::post('/orders/{order}/dispatch', [
            AdminOrderController::class,
            'dispatch'
        ])->name('orders.dispatch');

        Route::post('/orders/{order}/deliver', [
            AdminOrderController::class,
            'deliver'
        ])->name('orders.deliver');


        Route::post('/orders/{order}/invoice-generate', [
        AdminOrderController::class,
        'markInvoiceGenerated'
        ])->name('orders.invoice.generate');


        Route::post('/orders/{order}/invoice/remove', 
        [AdminOrderController::class, 'removeInvoice'])
        ->name('orders.invoice.remove');

         Route::get('/orders/{order}/invoice/print', 
        [AdminOrderController::class, 'printInvoice'])
        ->name('orders.invoice.print');


    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');


    // Route::get('/invoices', [TallyInvoiceController::class, 'index'])->name('tally.invoices.index');
    // Route::get('/invoices/{id}', [TallyInvoiceController::class, 'show'])->name('tally.invoices.show');
    // Route::delete('/invoices/{id}', [TallyInvoiceController::class, 'destroy'])->name('tally.invoices.destroy');
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{order}/print', [AdminInvoiceController::class, 'print'])->name('print');

    //Retailers
    Route::resource('retailers', AdminRetailerController::class)->names('retailers');
    Route::get('retailers/export', [AdminRetailerController::class, 'export'])->name('retailers.export');


});


//Authenticated Distributor Login

Route::prefix('distributor')->name('distributor.')->middleware('auth:distributor')->group(function () {

    //Dashboard
    Route::get('/dashboard', [DistributorDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('retailers', DistRetailerController::class)->names('retailers');
    Route::get('get-districts', [DistRetailerController::class, 'getDistricts'])->name('get-districts');





    Route::get('/stock', [DistributorStockController::class, 'index'])
        ->name('stock.index');

    Route::resource('retailer/sales', RetailerSaleController::class)->names('retailer-sales');

    // Route::get('/sales/create', [RetailerSaleController::class, 'create'])
    //     ->name('sales.create');

    // Route::post('/sales', [RetailerSaleController::class, 'store'])
    //     ->name('sales.store');

    Route::resource('/orders', DistOrderController::class);
    Route::post('/orders/{order}/deliver', [DistOrderController::class,'deliver'])->name('orders.deliver');

      Route::get('/inventory/ledger',
            [DistributorInventoryLedgerController::class, 'index']
        )->name('inventory.ledger');






});

//Authenticated Sales Person Login

Route::prefix('sales')->name('sales.')->middleware('auth:sales')->group(function () {

    //Dashboard
    Route::get('/dashboard', [SalesDashboardController::class, 'dashboard'])->name('dashboard');


});


//Shared Order Controller Routes
// Route::middleware(['auth:admin,distributor,sales'])->group(function () {
//   //  Route::get('orders/create', [OrderController::class,'create'])->name('orders.create');
//     Route::post('orders', [OrderController::class,'store'])->name('orders.store');
//    // Route::get('orders/{order}/edit', [OrderController::class,'edit'])->name('orders.edit');
//     Route::put('orders/{order}', [OrderController::class,'update'])->name('orders.update');
// });

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {   

        Route::post('orders', [OrderController::class, 'store'])
            ->name('orders.store');

        // Route::get('orders/{order}/edit', [OrderController::class, 'edit'])
        //     ->name('orders.edit');

        Route::put('orders/{order}', [OrderController::class, 'update'])
            ->name('orders.update');
    });

Route::prefix('distributor')
    ->name('distributor.')
    ->middleware('auth:distributor')
    ->group(function () {   

        Route::post('orders', [OrderController::class, 'store'])
            ->name('orders.store');

        // Route::get('orders/{order}/edit', [OrderController::class, 'edit'])
        //     ->name('orders.edit');

        Route::put('orders/{order}', [OrderController::class, 'update'])
            ->name('orders.update');
    });
