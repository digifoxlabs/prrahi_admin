<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderActivityLogger;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class TallyOrderController extends Controller
{
    // public function pendingInvoiceOrders()
    // {
    //     $orders = Order::with([
    //             'distributor:id,firm_name,nature_of_firm,gst,contact_person,contact_number',
    //             'items.product.parent'
    //         ])
    //         ->where('status', 'confirmed')
    //         ->where('invoice_status', 'pending')
    //         ->get();

    //     $response = $orders->map(function ($order) {

    //         return [
    //             // ================= DISTRIBUTOR =================
    //             'distributor' => [
    //                 'id'              => $order->distributor->id,
    //                 'firm_name'       => $order->distributor->firm_name,
    //                 'nature_of_firm'  => $order->distributor->nature_of_firm,
    //                 'gst'             => $order->distributor->gst,
    //                 'contact_person'  => $order->distributor->contact_person,
    //                 'contact_number'  => $order->distributor->contact_number,
    //             ],

    //             // ================= ORDER =================
    //             'order' => [
    //                 'order_number'    => $order->order_number,
    //                 'order_date'      => $order->order_date,
    //                 'billing_address' => $order->billing_address,
    //                 'subtotal'        => (float) $order->subtotal,
    //                 'discount'        => (float) $order->discount,
    //                 'cgst'            => (float) $order->cgst,
    //                 'sgst'            => (float) $order->sgst,
    //                 'igst'            => (float) $order->igst,
    //                 'round_off'       => (float) $order->round_off,
    //                 'total_amount'    => (float) $order->total_amount,
    //             ],

    //             // ================= ORDER ITEMS =================
    //             'items' => $order->items->map(function ($item) {

    //                 $product = $item->product;

    //                 // Product name logic
    //                 if ($product->type === 'variant' && $product->parent) {
    //                     $productName = $product->parent->name;
    //                     $productCode = $product->code;

    //                     if ($product->attributes) {
    //                         $attrs = [];
    //                         if (!empty($product->attributes['fragrance'])) {
    //                             $attrs[] = $product->attributes['fragrance'];
    //                         }
    //                         if (!empty($product->attributes['size'])) {
    //                             $attrs[] = $product->attributes['size'];
    //                         }

    //                         if (count($attrs)) {
    //                             $productName .= ' - ' . implode(' / ', $attrs);
    //                         }
    //                     }
    //                 } else {
    //                     $productName = $product->name;
    //                     $productCode = $product->code;
    //                 }

    //                 return [
    //                     'product_name' => trim($productName . ($productCode ? " ($productCode)" : '')),
    //                     'rate'         => (float) $item->rate,
    //                     'base_unit'    => $item->base_unit,
    //                     'quantity'     => (int) $item->quantity,
    //                     'discount_percent'     => (int) $item->discount_percent,
    //                     'total'        => (float) $item->total,
    //                 ];
    //             })->values(),
    //         ];
    //     });

    //     return response()->json([
    //         'status' => 'success',
    //         'count'  => $response->count(),
    //         'data'   => $response,
    //     ]);
    // }

public function pendingInvoiceOrders()
{
    $orders = Order::with([
            'distributor:id,firm_name,gst,state,pincode,contact_person,contact_number',
            'items.product.parent'
        ])
        ->where('status', 'confirmed')
        ->where('invoice_status', 'pending')
        ->get();

    $orderArray = $orders->map(function ($order) {

        return [
            'order_number'    => $order->order_number,
            'order_date'      => $order->order_date,

            // Distributor data flattened
            'firm_name'       => $order->distributor->firm_name,
            'state'           => $order->distributor->state,
            'pincode'           => $order->distributor->pincode,
            'gst_number'             => $order->distributor->gst,
            'contact_person'  => $order->distributor->contact_person,
            'contact_number'  => $order->distributor->contact_number,

            'billing_address' => $order->billing_address,

            // Amounts
            'subtotal'        => (float) $order->subtotal,
            'discount'        => (float) $order->discount,
            'cgst'            => (float) $order->cgst,
            'sgst'            => (float) $order->sgst,
            'igst'            => (float) $order->igst,
            'round_off'       => (float) $order->round_off,
            'total_amount'    => (float) $order->total_amount,

            // ================= ITEMS =================
            'items' => $order->items->map(function ($item) {

                $product = $item->product;

                // Product name logic
                if ($product->type === 'variant' && $product->parent) {
                    $productName = $product->parent->name;
                    $productCode = $product->code;

                    if ($product->attributes) {
                        $attrs = array_filter([
                            $product->attributes['fragrance'] ?? null,
                            $product->attributes['size'] ?? null,
                        ]);

                        if ($attrs) {
                            $productName .= ' - ' . implode(' / ', $attrs);
                        }
                    }
                } else {
                    $productName = $product->name;
                    $productCode = $product->code;
                }

                return [
                    'product_name'     => trim($productName . ($productCode ? " ({$productCode})" : '')),
                    'hsn'             => (float) $item->hsn,
                    'rate'             => (float) $item->rate,
                    'base_unit'        => $item->base_unit,
                    'quantity'         => (int) $item->quantity,
                    'discount_percent' => (float) ($item->discount_percent ?? 0),
                    'total'            => (float) $item->total,
                ];
            })->values(),
        ];
    });

    return response()->json([
        'status' => 'success',
        'count'  => $orderArray->count(),
        'data'   => [
            'order' => $orderArray
        ],
    ]);
}






public function invoiceGenerated(Request $request)
{
    $request->headers->set('Accept', 'application/json');
    /* ================= SECURITY ================= */
    $apiKey = $request->header('X-API-KEY');

    if ($apiKey !== config('services.tally.api_key')) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized request'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /* ================= VALIDATION ================= */
    $validated = $request->validate([
        'order_number' => ['required', 'string', 'exists:orders,order_number'],
        'invoice_no'   => ['required', 'string', 'max:100'],
        'invoice_date' => ['required', 'date'],
    ]);

    /* ================= FETCH ORDER ================= */
    $order = Order::where('order_number', $validated['order_number'])->first();

    // Guard: must be confirmed
    if ($order->status !== 'confirmed') {
        return response()->json([
            'status'  => 'error',
            'message' => 'Order is not in confirmed state'
        ], 422);
    }

    // Guard: prevent double invoicing
    if ($order->invoice_status === 'generated') {
        return response()->json([
            'status'  => 'success',
            'message' => 'Invoice already generated',
        ]);
    }

    /* ================= TRANSACTION ================= */
    DB::transaction(function () use ($order, $validated) {

        // Update order
        $order->update([
            'invoice_status' => 'generated',
            'bill_generated' => true,
            'invoice_no'     => $validated['invoice_no'],
            'invoice_date'   => $validated['invoice_date'],
        ]);

        // Log timeline activity
        OrderActivityLogger::log(
            $order,
            'invoice_generated',
            'Invoice generated via Tally'
        );
    });

    return response()->json([
        'status'  => 'success',
        'message' => 'Invoice details saved successfully',
    ]);
}




public function invoiceStatus(Request $request)
{
    // Force JSON response
    $request->headers->set('Accept', 'application/json');

    /* ================= SECURITY ================= */
    $apiKey = $request->header('X-API-KEY');

    if ($apiKey !== config('services.tally.api_key')) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized request',
        ], Response::HTTP_UNAUTHORIZED);
    }

    /* ================= VALIDATION ================= */
    $validator = Validator::make($request->all(), [
        'order_number' => ['required', 'string', 'exists:orders,order_number'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /* ================= FETCH ORDER ================= */
    $order = Order::where('order_number', $request->order_number)->first();

    /* ================= RESPONSE ================= */
    return response()->json([
        'status' => 'success',

        'order' => [
            'order_number'   => $order->order_number,
            'order_status'   => $order->status,              // pending / confirmed / cancelled
            'invoice_status' => $order->invoice_status,      // pending / generated
            'bill_generated' => (bool) $order->bill_generated,

            'invoice' => [
                'invoice_no'   => $order->invoice_no,
                'invoice_date' => $order->invoice_date,
            ],
        ],
    ]);
}









}