<?php

namespace App\Http\Controllers\Api\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Actions\Orders\SaveOrderAction;
use App\Models\Distributor;
use App\Services\Orders\OrderCalculationService;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\OrderActivityLogger;
use App\Services\OrderDeliveryService;

class DistOrderController extends Controller
{
    /**
     * List orders of logged-in distributor
     */
    public function index(Request $request)
    {
        $distributor = auth('distributor_api')->user();

        $query = Order::with('distributor')
            ->where('distributor_id', $distributor->id);

        // 🔹 Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔹 Search by order number
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            });
        }

        $orders = $query
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'has_more'     => $orders->hasMorePages(),
            ],
        ]);
    }

    /**
     * Show order details
     */
    public function show(Request $request, $id)
    {
        $distributor = auth('distributor_api')->user();

        $order = Order::with([
            'items.product.parent',
            'distributor'
        ])
            ->where('id', $id)
            ->where('distributor_id', $distributor->id)
            ->firstOrFail();

        return response()->json([
            'order' => [
                'id'            => $order->id,
                'order_number'  => $order->order_number,
                'order_date'    => $order->order_date,
                'status'        => $order->status,
                'subtotal'      => $order->subtotal,
                'discount'      => $order->discount,
                'cgst'          => $order->cgst,
                'sgst'          => $order->sgst,
                'igst'          => $order->igst,
                'round_off'     => $order->round_off,
                'total_amount'  => $order->total_amount,
                'invoice_status'  => $order->invoice_status,
                'dispatch_status'  => $order->dispatch_status,
                'distributor'   => [
                    'id'        => $order->distributor->id,
                    'firm_name' => $order->distributor->firm_name,
                    'state'     => $order->distributor->state,
                ],
                'items' => $order->items->map(fn($item) => [
                    'id'         => $item->id,
                    'product_id' => $item->product_id,
                    'product'    => [
                        'id'         => $item->product->id,
                        'type'       => $item->product->type,
                        'name'       => $item->product->name,
                        'attributes' => $item->product->attributes,
                        'parent'     => $item->product->parent
                            ? [
                                'id'   => $item->product->parent->id,
                                'name' => $item->product->parent->name,
                            ]
                            : null,
                    ],
                    'quantity'         => $item->quantity,
                    'rate'             => $item->rate,
                    'base_unit'        => $item->base_unit,
                    'discount_percent' => $item->discount_percent,
                    'total'            => $item->total,
                ]),
            ],
        ]);
    }


    /**
     * Data needed for create order screen
     */
    public function create(Request $request)
    {
        // $sales = $request->user();

        $distributor = auth('distributor_api')->user();

        $distributors = Distributor::orderBy('firm_name')
            ->where('id', $distributor->id)
            ->get();

        $products = Product::whereNull('parent_id')
            ->with(['variants'])
            ->get();

        return response()->json([
            'distributors' => $distributors,
            'products' => $products,
        ]);
    }



    /**
     * Store new distributor order (Sales)
     */
    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        $orderNumber = $request->order_number
            ?: 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

        //Get Billing Address
        $distributorId = $validated['distributor_id'];

        $distributor = Distributor::find($distributorId);

        $billingAddress = $distributor?->formatted_billing_address;

        $order = SaveOrderAction::create([
            'order_number'    => $orderNumber,
            'order_date'      => $validated['order_date'],
            'distributor_id'  => $validated['distributor_id'],
            'billing_address' => $billingAddress,
            'discount'        => $request->discount ?? 0,
            'items'           => $validated['items'],
            'latitude'  => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'status'   => $order->status,
        ], 201);
    }



    //Mark Order Delivery
    public function deliver(Request $request, $id)
    {

        $distributor = auth('distributor_api')->user();

        $order = Order::where('id', $id)
            ->where('distributor_id', $distributor->id)
            ->firstOrFail();

        abort_if($order->status === 'pending' || $order->dispatch_status === 'pending' || $order->dispatch_status === 'delivered', 403, 'Order cannot be edited.');


        // // Guard: must be dispatched
        // if ($order->dispatch_status !== 'dispatched') {
        //     return back()->with('error', 'Order must be dispatched before delivery.');
        // }

        // // Guard: already delivered
        // if ($order->dispatch_status === 'delivered') {
        //     return back()->with('error', 'Order is already delivered.');
        // }

        DB::transaction(function () use ($order) {

            // Update order
            $order->update([
                'dispatch_status' => 'delivered',
            ]);

            // Log activity
            OrderActivityLogger::log(
                $order,
                'delivered',
                'Order delivered'
            );

            //Order Delivery Service to Update in Distributor Inventory
            OrderDeliveryService::handle($order);
    

        });

        return back()->with('success', 'Order marked as delivered.');
    }






    /**
     * Update order (only if pending)
     */
    public function update(Request $request, $id)
    {
        $distributor = auth('distributor_api')->user();

        $order = Order::where('id', $id)
            ->where('distributor_id', $distributor->id)
            ->firstOrFail();

        abort_if($order->status !== 'pending', 403, 'Order cannot be edited.');

        $validated = $this->validatedData($request);

        $distributorModel = Distributor::find($validated['distributor_id']);

        $billingAddress = $distributorModel?->formatted_billing_address;

        $order = SaveOrderAction::update($order, [
            'order_number'    => $request->order_number ?? $order->order_number,
            'order_date'      => $validated['order_date'],
            'distributor_id'  => $validated['distributor_id'],
            'billing_address' => $billingAddress,
            'discount'        => $request->discount ?? 0,
            'items'           => $validated['items'],
        ]);

        return response()->json([
            'message'  => 'Order updated successfully',
            'order_id' => $order->id,
        ]);
    }

    /**
     * Validation
     */
    private function validatedData(Request $request): array
    {
        return $request->validate(
            [
                'distributor_id' => ['required', 'exists:distributors,id'],
                'order_date'     => ['required', 'date'],
                'order_number'   => ['nullable', 'max:50'],
                'items'          => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'exists:products,id'],
                'items.*.quantity'   => ['required', 'integer', 'min:1'],
            ],
            [
                'distributor_id.required' => 'Distributor is required.',
                'items.required'          => 'At least one product is required.',
                'items.min'               => 'At least one product is required.',
            ]
        );
    }

        public function preview(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],


        ]);

        $calculation = OrderCalculationService::calculateForDistributor(
            $validated['items'],
            $validated['distributor_id'],
            $validated['discount'] ?? 0
        );

        return response()->json([
            'preview' => $calculation,
        ]);
    }











}
