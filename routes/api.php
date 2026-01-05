<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TallyController;

use App\Http\Controllers\Api\TallyOrderController; //New

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
