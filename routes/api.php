<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TallyController;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\TallyOrderController; //New
use App\Http\Controllers\Api\Sales\OrderController;
use App\Http\Controllers\Api\Sales\RetailOrderController;
use App\Http\Controllers\Api\Sales\DistributorController;
use App\Http\Controllers\Api\Sales\DistributorCompanyController;
use App\Http\Controllers\Api\Sales\DistributorBankController;
use App\Http\Controllers\Api\Sales\DistributorGodownController;
use App\Http\Controllers\Api\Sales\DistributorManpowerController;
use App\Http\Controllers\Api\Sales\DistributorVehicleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/health', function () {
    return 'OK';
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tally/invoice/{orderNumber}', [TallyController::class, 'store']);


//New Api
Route::get('/tally/orders/pending-invoice', [
    TallyOrderController::class,
    'pendingInvoiceOrders'
]);

Route::post('/tally/orders/invoice-generated', [
    TallyOrderController::class,
    'invoiceGenerated'
]);


Route::get('/tally/orders/invoice-status', [
    TallyOrderController::class,
    'invoiceStatus'
]);  // GET https://your-domain.com/api/tally/orders/invoice-status?order_number=ORD-2025-00012


//Api Auth Controller
Route::post('/login/sales', [AuthController::class, 'salesLogin']);
Route::post('/login/distributor', [AuthController::class, 'distributorLogin']);

Route::middleware('auth:sales_api')->get('/sales/dashboard', function () {
    return response()->json([
        'total_orders' => 120,
        'total_distributors' => 42,
    ]);
});

Route::middleware('auth:distributor_api')->get('/distributor/dashboard', function () {
    return response()->json([
        'pending_orders' => 15,
        'outstanding_amount' => 82000,
    ]);
});


Route::middleware('auth:sales_api')->post(
    '/logout/sales',
    [AuthController::class, 'salesLogout']
);

Route::middleware('auth:distributor_api')->post(
    '/logout/distributor',
    [AuthController::class, 'distributorLogout']
);


Route::middleware('auth:sales_api')->prefix('sales')->group(function () {

    //Distributor Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/create', [OrderController::class, 'create']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::post('/orders/preview', [OrderController::class, 'preview']);

    //Retailer Orders
    Route::get('/retail-orders', [RetailOrderController::class, 'index']);
    Route::get('/retail-orders/create', [RetailOrderController::class, 'create']);
    Route::post('/retail-orders', [RetailOrderController::class, 'store']);
    Route::get('/retail-orders/{id}', [RetailOrderController::class, 'show']);
    Route::put('/retail-orders/{id}', [RetailOrderController::class, 'update']);
    Route::post('/retail-orders/preview', [RetailOrderController::class, 'preview']);


    // Distributors
    // Route::get('/distributors', [DistributorController::class, 'index']);
    Route::post('/distributors', [DistributorController::class, 'store']); // 👈 NEW
    Route::put('/distributors/{distributor}', [DistributorController::class, 'update']);

});


//temp api
Route::get('/sales/distributors', function () {
    return response()->json([
        'user' => auth('sales_api')->user(),
    ]);
})->middleware('auth:sales_api');


//Companies Under Distributors 
Route::middleware('auth:sales_api')
    ->prefix('sales/distributors/{distributor}')
    ->group(function () {
        Route::get('companies', [DistributorCompanyController::class, 'index']);
        Route::post('companies', [DistributorCompanyController::class, 'store']);
        Route::put('companies/{company}', [DistributorCompanyController::class, 'update']);
        Route::delete('companies/{company}', [DistributorCompanyController::class, 'destroy']);

        //Distributor Banks
        Route::get('banks', [DistributorBankController::class, 'index']);
        Route::post('banks', [DistributorBankController::class, 'store']);
        Route::put('banks/{bank}', [DistributorBankController::class, 'update']);
        Route::delete('banks/{bank}', [DistributorBankController::class, 'destroy']);


        //Godown Controller
        Route::get('godowns', [DistributorGodownController::class, 'index']);
        Route::post('godowns', [DistributorGodownController::class, 'store']);
        Route::put('godowns/{godown}', [DistributorGodownController::class, 'update']);
        Route::delete('godowns/{godown}', [DistributorGodownController::class, 'destroy']);

        //  Manpower Controller
        Route::get('manpowers', [DistributorManpowerController::class, 'index']);
        Route::post('manpowers', [DistributorManpowerController::class, 'store']);
        Route::put('manpowers/{manpower}', [DistributorManpowerController::class, 'update']);
        Route::delete('manpowers/{manpower}', [DistributorManpowerController::class, 'destroy']);

        // Vehcile Controller
        Route::get('vehicles', [DistributorVehicleController::class, 'index']);
        Route::post('vehicles', [DistributorVehicleController::class, 'store']);
        Route::put('vehicles/{vehicle}', [DistributorVehicleController::class, 'update']);
        Route::delete('vehicles/{vehicle}', [DistributorVehicleController::class, 'destroy']);

        Route::post('profile-photo',  [DistributorController::class, 'uploadProfilePhoto']);
        Route::post('change-password',[DistributorController::class, 'changePassword']);


    });