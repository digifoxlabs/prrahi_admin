<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\RetailOrder;
use App\Models\Retailer;
use Illuminate\Support\Str;
use App\Support\OrderActor;
use App\Actions\RetailOrders\SaveRetailOrderAction;
use App\Services\Orders\OrderCalculationService;

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

        $retailerId = $request->retailer_id;

        $distributorId = Retailer::where('id', $retailerId)
        ->value('distributor_id'); // returns null if not found



        $order = SaveRetailOrderAction::create([
            'order_number'     => $orderNumber,
            'order_date'       => $validated['order_date'],
            'distributor_id'   => $distributorId,
            'retailer_id'      => $validated['retailer_id'],
            'billing_address'  => $request->billing_address,
            'discount'         => $request->discount_amount ?? 0,
            'items'            => $validated['items'],
        ]);

        return $this->redirectAfterSave($order, $actor['role'])
        ->with('success', 'Order created successfully.');


    }


    public function update(Request $request, RetailOrder $order)
    {

        abort_if($order->status !== 'pending', 403, 'Order cannot be edited.');

        $validated = $this->validatedData($request, $order);

        $actor = OrderActor::resolve();

        $retailerId = $request->retailer_id;

        $distributorId = Retailer::where('id', $retailerId)->value('distributor_id'); // returns null if not found


        $order = SaveRetailOrderAction::update($order, [
            'order_number'    => $request->order_number ?? $order->order_number,
            'order_date'      => $validated['order_date'],
            'retailer_id'     => $retailerId,
            'distributor_id'  => $distributorId,
            'billing_address' => $request->billing_address,
            'discount'        => $request->discount_amount ?? 0,
            'items'           => $validated['items'],
        ]);

        return $this->redirectAfterUpdate($order, $actor['role'])
         ->with('success', 'Order Updated successfully.');


    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'retailer_id'         => ['required', 'exists:retailers,id'],
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
        ]);

        $calculation = OrderCalculationService::calculateForRetailer(
            $validated['items'],
            (int) $validated['retailer_id'],
            (float) ($validated['discount'] ?? 0)
        );

        return response()->json([
            'preview' => $calculation,
        ]);
    }

    
    // Validate Request Data for both create and edit
    private function validatedData(Request $request, ?RetailOrder $order = null): array
    {
        return $request->validate([

            'retailer_id' => ['required', 'exists:retailers,id'],
            'order_date'     => ['required', 'date'],
            'order_number'   => ['nullable', 'max:50', Rule::unique('retail_orders', 'order_number')->ignore($order?->id)],
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
           'admin'       => redirect()->route('admin.retail.orders.index'),
           'distributor' => redirect()->route('distributor.retail.orders.index'),
            'sales'       => redirect()->route('sales.retail.orders.index'),
            default       => abort(403),
        };
    }

    protected function redirectAfterUpdate(RetailOrder $order, string $actor)
    {
        return match ($actor) {
           'admin'       => redirect()->route('admin.retail.orders.show', $order),
           'distributor' => redirect()->route('distributor.retail.orders.show', $order),
            'sales'       => redirect()->route('sales.retail.orders.show', $order),
            default       => abort(403),
        };
    }


}
