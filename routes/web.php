<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DistributorController as AdminDistributorController;
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
use App\Http\Controllers\Admin\AdminRetailOrderController;
use App\Http\Controllers\Admin\SalesTypeController;
use App\Http\Controllers\Admin\VisitController;
use App\Http\Controllers\Distributor\DashboardController as DistributorDashboardController;
use App\Http\Controllers\Distributor\DistRetailerController;
use App\Http\Controllers\Distributor\DistributorStockController;
use App\Http\Controllers\Distributor\RetailerSaleController;
use App\Http\Controllers\Distributor\DistOrderController;
use App\Http\Controllers\Distributor\DistributorInventoryLedgerController;
use App\Http\Controllers\Distributor\DistRetailOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\RetailOrderController;
use App\Http\Controllers\Sales\SalesRetailerController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Sales\SalesDistributorController;
use App\Http\Controllers\Sales\SalesRetailOrderController;



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
    return view('admin.pages.dashboard');
});

Route::get('/login', function () {
    return view('admin.login');
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
})->name('api.doc');
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
    // Route::post('/distributors/profile/upload/{id}', [AdminDistributorController::class, 'uploadImage'])->name('distributors.updateProfileImage');
    // Route::post('/distributors/update-password', [AdminDistributorController::class, 'updatePassword'])->name('distributors.updatePassword');


    // Password update
    Route::get('/distributors/export', [AdminDistributorController::class, 'export'])->name('distributors.export');  
    Route::resource('distributors', AdminDistributorController::class)->only(['index','create','edit','show','destroy']);


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


    Route::resource('orders', AdminOrderController::class)->only(['index','create','edit','show','destroy']);

    Route::post('orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');

   

    Route::post('/orders/{order}/dispatch', [AdminOrderController::class,'dispatch'])->name('orders.dispatch');

    Route::post('/orders/{order}/deliver', [AdminOrderController::class,'deliver'])->name('orders.deliver');


    Route::post('/orders/{order}/invoice-generate', [AdminOrderController::class,'markInvoiceGenerated'])->name('orders.invoice.generate');

    Route::post('/orders/{order}/invoice/remove',[AdminOrderController::class, 'removeInvoice'])->name('orders.invoice.remove');

    Route::get('/orders/{order}/invoice/print', [AdminOrderController::class, 'printInvoice'])->name('orders.invoice.print');


    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');


    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');

    //Retailers
    Route::resource('retailers', AdminRetailerController::class)->names('retailers')->only(['index','create','edit','show','destroy']);

    Route::get('retailers/export', [AdminRetailerController::class, 'export'])->name('retailers.export');

    // retail Order
     Route::resource('retail/orders', AdminRetailOrderController::class)->only(['index','create','edit','show','destroy'])->names('retail.orders');
    Route::post('retail/orders/{order}/confirm', [AdminRetailOrderController::class, 'confirm'])->name('retail.orders.confirm');
    Route::post('retail/orders/{order}/cancel', [AdminRetailOrderController::class, 'cancel'])->name('retail.orders.cancel');

    Route::post('/retail-orders/{order}/assign-distributor', [AdminRetailOrderController::class, 'assignDistributor'])->name('retail.orders.assign-distributor');

    //Sales Type Controller
    Route::resource('sales-types', SalesTypeController::class)->names('sales-type');
    Route::put('orders/{order}/sales-type', [AdminOrderController::class, 'updateSalesType'])->name('orders.updateSalesType');

    //Sales Visit Controller

    Route::delete('/visits/{visitNote}', [VisitController::class, 'destroy'])
        ->name('visits.destroy');

    Route::get('/visits', [VisitController::class, 'index'])
        ->name('visits.index');

    Route::get('/visits/load-more', [VisitController::class, 'loadMore'])
        ->name('visits.loadMore');

   
});


//Authenticated Distributor Login

Route::prefix('distributor')->name('distributor.')->middleware('auth:distributor')->group(function () {

    //Dashboard
    Route::get('/dashboard', [DistributorDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('retailers', DistRetailerController::class)->names('retailers');
    Route::get('get-districts', [DistRetailerController::class, 'getDistricts'])->name('get-districts');


    Route::get('/stock', [DistributorStockController::class, 'index'])->name('stock.index');

    Route::resource('retailer/sales', RetailerSaleController::class)->names('retailer-sales');

    Route::resource('/orders', DistOrderController::class);
    Route::post('/orders/{order}/deliver', [DistOrderController::class,'deliver'])->name('orders.deliver');

    Route::get('/inventory/ledger',[DistributorInventoryLedgerController::class, 'index'])->name('inventory.ledger');

    //Retail order Controller
    Route::resource('retail/orders', DistRetailOrderController::class)->only(['index','create','edit','show','destroy'])->names('retail.orders');
    Route::post('retail/orders/{order}/confirm', [DistRetailOrderController::class, 'confirm'])->name('retail.orders.confirm');
    Route::post('retail/orders/{order}/cancel', [DistRetailOrderController::class, 'cancel'])->name('retail.orders.cancel');


});

//Authenticated Sales Person Login

Route::prefix('sales')->name('sales.')->middleware('auth:sales')->group(function () {

    //Dashboard
    Route::get('/dashboard', [SalesDashboardController::class, 'dashboard'])->name('dashboard');


    /* Retailers CRUD by Sales Pesons
    *  Store and Update to be done by Shared OrderController and Services
    */
    Route::resource('/retailers', SalesRetailerController::class)->only(['index','create','edit','show','destroy']);

    //Order By Sales Persons
    Route::resource('/orders', SalesOrderController::class);

    //Distributors Route
    Route::resource('distributors', SalesDistributorController::class)->only(['index','create','edit','show','destroy']);

    //Retail order Controller
    Route::resource('retail/orders', SalesRetailOrderController::class)->only(['index','create','edit','show','destroy'])->names('retail.orders');

    Route::post('retail/orders/{order}/confirm', [SalesRetailOrderController::class, 'confirm'])->name('retail.orders.confirm');
    Route::post('retail/orders/{order}/cancel', [SalesRetailOrderController::class, 'cancel'])->name('retail.orders.cancel');

    Route::post('/retail-orders/{order}/assign-distributor', [SalesRetailOrderController::class, 'assignDistributor'])->name('retail.orders.assign-distributor');


});


//Shared Order Controller Routes
// Route::middleware(['auth:admin,distributor,sales'])->group(function () {
//   //  Route::get('orders/create', [OrderController::class,'create'])->name('orders.create');
//     Route::post('orders', [OrderController::class,'store'])->name('orders.store');
//    // Route::get('orders/{order}/edit', [OrderController::class,'edit'])->name('orders.edit');
//     Route::put('orders/{order}', [OrderController::class,'update'])->name('orders.update');
// });


//Shared District Web Route
    Route::middleware(['auth:admin,distributor,sales'])->group(function () {

    // Route::get('orders/create', [OrderController::class,'create'])->name('orders.create');
    Route::get('get-districts', [RetailerController::class, 'getDistricts'])->name('all.get-districts');

});

/*********************
 * Shared Distributor Order Controller
*********************/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {

        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/orders/{order}/invoice/print', [OrderController::class, 'printInvoice'])->name('orders.invoice.print');
    });

    Route::prefix('distributor')
        ->name('distributor.')
        ->middleware('auth:distributor')
        ->group(function () {   

            Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

            // Route::get('orders/{order}/edit', [OrderController::class, 'edit'])
            //     ->name('orders.edit');

            Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

            Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

            Route::get('/orders/{order}/invoice/print', [OrderController::class, 'printInvoice'])->name('orders.invoice.print');

        });

    Route::prefix('sales')
        ->name('sales.')
        ->middleware('auth:sales')
        ->group(function () {   

            Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

            Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

             Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

             Route::get('/orders/{order}/invoice/print', [OrderController::class, 'printInvoice'])->name('orders.invoice.print');

        });


/******************
 * Shared Retailer Controller
 * ******************/

    Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function (){

        Route::post('/retailers/store', [RetailerController::class, 'store'])->name('retailers.store');
        Route::put('retailers/{retailer}', [RetailerController::class, 'update'])->name('retailers.update');

    });

    Route::prefix('distributor')->name('distributor.')->middleware('auth:distributor')->group(function (){

        Route::post('/retailers/store', [RetailerController::class, 'store'])->name('retailers.store');
        Route::put('retailers/{retailer}', [RetailerController::class, 'update'])->name('retailers.update');

    });

    Route::prefix('sales')->name('sales.')->middleware('auth:sales')->group(function (){

        Route::post('/retailers/store', [RetailerController::class, 'store'])->name('retailers.store');
        Route::put('retailers/{retailer}', [RetailerController::class, 'update'])->name('retailers.update');

    });

    /************************ 
     * Shared Distributor Controller
    */

        Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function (){

            Route::post('/distributors/store', [DistributorController::class, 'store'])->name('distributor.store');
            Route::put('distributors/{distributor}', [DistributorController::class, 'update'])->name('distributor.update');


            Route::post('/distributors/profile/upload/{id}', [DistributorController::class, 'uploadImage'])->name('distributors.updateProfileImage');
            Route::post('/distributors/update-password', [DistributorController::class, 'updatePassword'])->name('distributors.updatePassword');

        });

        Route::prefix('sales')->name('sales.')->middleware('auth:sales')->group(function (){

            Route::post('/distributors/store', [DistributorController::class, 'store'])->name('distributor.store');
            Route::put('distributors/{distributor}', [DistributorController::class, 'update'])->name('distributor.update');

            
            Route::post('/distributors/profile/upload/{id}', [DistributorController::class, 'uploadImage'])->name('distributors.updateProfileImage');
            Route::post('/distributors/update-password', [DistributorController::class, 'updatePassword'])->name('distributors.updatePassword');


        });
    
        /*********************
         * Shared Retailer Orders
         *************************/
            Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function (){

                Route::post('sales/orders', [RetailOrderController::class, 'store'])->name('retail.orders.store');

                Route::put('sales/orders/{order}', [RetailOrderController::class, 'update'])->name('retail.orders.update');


            });
            Route::prefix('sales')->name('sales.')->middleware('auth:sales')->group(function (){

                Route::post('sales/orders', [RetailOrderController::class, 'store'])->name('retail.orders.store');

                Route::put('sales/orders/{order}', [RetailOrderController::class, 'update'])->name('retail.orders.update');


            });

            Route::prefix('distributor')->name('distributor.')->middleware('auth:distributor')->group(function (){

                Route::post('sales/orders', [RetailOrderController::class, 'store'])->name('retail.orders.store');

                Route::put('sales/orders/{order}', [RetailOrderController::class, 'update'])->name('retail.orders.update');


            });