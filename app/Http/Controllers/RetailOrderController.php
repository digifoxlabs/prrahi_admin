<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\RetailOrder;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Support\OrderActor;
use Illuminate\Support\Facades\DB;
use App\Services\RetailOrders\{
    CreateRetailOrderService,
    AddOrderItemsService
};
use App\Services\OrderActivityLogger;

class RetailOrderController extends Controller
{
    

    public function store(Request $request)
    {

         $validated = $this->validatedData($request);

        /** Detect actor */
       // [$actorType, $actorId] = $this->resolveActor();
        $actor = OrderActor::resolve();

        //Generate Order Number if not provided
        $orderNumber = $request->order_number ?: 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

        $order = CreateRetailOrderService::create([
            'order_number'     => $orderNumber,
            'order_date'      => $request->order_date,
            'distributor_id'  => $request->distributor_id,
            'retailer_id'      => $request->retailer_id,
            'billing_address' => $request->billing_address,

            'subtotal'        => $request->subtotal,
            'discount'        => $request->discount_amount ?? 0,
            'cgst'            => $request->cgst,
            'sgst'            => $request->sgst,
            'igst'            => $request->igst ?? 0,
            'round_off'       => $request->round_off ?? 0,
            'total_amount'    => $request->total_amount,

            'status'          => 'pending',
            'created_by_type'  => $actor['type'],
            'created_by_id'    => $actor['id'],

        ]);

        // $items = collect($validated['items'])->map(fn ($row) => [
        $items = collect($request->items)->map(fn ($row) => [
            
                'retail_order_id' => $order->id,
                'product_id' => $row['product_id'],
                'price'   => $row['rate'],
                'base_unit' => $row['base_unit'],
                'quantity'  => $row['quantity'],
                'discount_percent'   => $row['discount_percent'],
                'total'   => $row['amount'],

        ])->toArray();

     
        AddOrderItemsService::handle($order, $items);

       // OrderActivityLogger::log($order, 'created', 'Order created');

         return $this->redirectAfterSave($order, $actor['role'])
         ->with('success', 'Order created successfully.');


    }




    
    // Validate Request Data for both create and edit
    private function validatedData(Request $request, ?RetailOrder $order = null): array
    {
        return $request->validate([

            'retailer_id' => ['required', 'exists:retailers,id'],
            'order_date'     => ['required', 'date'],
            'order_number'   => ['nullable', 'max:50', Rule::unique('orders', 'order_number')->ignore($order?->id)],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],

        ],
    
        [
                'retailer_id.required' => 'Please select a retailer.',
                'order_number.unique' => 'Duplicate Order Number',
                'items.required'          => 'Please add at least one product to the order.',
                'items.min' => 'Please add at least one product before saving the order.',
        ] );
    }


    protected function redirectAfterSave(RetailOrder $order, string $actor)
    {
        return match ($actor) {
           // 'admin'       => redirect()->route('admin.orders.index'),
          //  'distributor' => redirect()->route('distributor.orders.index'),
            'sales'       => redirect()->route('sales.retail.orders.index'),
            default       => abort(403),
        };
    }


}
