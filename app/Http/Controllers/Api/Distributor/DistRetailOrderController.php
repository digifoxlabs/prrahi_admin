<?php

namespace App\Http\Controllers\Api\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Retailer;
use App\Models\Product;
use App\Models\RetailOrder;
use Illuminate\Support\Str;
use App\Actions\Orders\SaveOrderAction;
use App\Actions\RetailOrders\SaveRetailOrderAction;
use App\Services\Orders\OrderCalculationService;

class DistRetailOrderController extends Controller
{
    /**
     * List retail orders created by logged-in sales person
     */
    public function index(Request $request)
    {
        $distributor = $request->user();

        $query = RetailOrder::with(['distributor', 'retailer'])
            ->where('distributor_id', $distributor->id);

        // 🔹 Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔹 Search (order number or retailer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('retailer', function ($rq) use ($search) {
                        $rq->where('retailer_name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(10);

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
     * Data needed for create retail order screen
     */
    public function create(Request $request)
    {
         $distributor = $request->user();

        $retailers = Retailer::orderBy('retailer_name')->where('distributor_id', $distributor->id)->get();

        $products = Product::whereNull('parent_id')
            ->with('variants')
            ->get();

        return response()->json([
            'retailers' => $retailers,
            'products'  => $products,
        ]);
    }

    /**
     * Store new retail order (Sales)
     */
    public function store(Request $request)
    {
          $distributor = $request->user();
        $validated = $this->validatedData($request);

        $orderNumber = $request->order_number
            ?: 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

        // 🔹 Retailer billing address
      $retailer = Retailer::findOrFail($validated['retailer_id']);

        $billingAddress = $retailer?->formatted_billing_address;

        $order = SaveRetailOrderAction::create([
            'order_number'    => $orderNumber,
            'order_date'      => $validated['order_date'],
            'retailer_id'     => $validated['retailer_id'],
            'billing_address' => $billingAddress,
            'discount'        => $request->discount ?? 0,
            'items'           => $validated['items'],
            'latitude'        => $request->latitude ?? null,
            'longitude'       => $request->longitude ?? null,
            'distributor_id'  => $distributor->id ?? null,
        ]);

        return response()->json([
            'message'  => 'Retail order created successfully',
            'order_id' => $order->id,
            'status'   => $order->status,
        ], 201);
    }

    /**
     * Show retail order details
     */
    public function show(Request $request, $id)
    {
        $distributor = $request->user();

        $order = RetailOrder::with([
            'items.product.parent',
            'retailer',
        ])
            ->whereNotNull('retailer_id')
            ->where('id', $id)
            ->where('distributor_id', $distributor->id)
            ->firstOrFail();

        return response()->json([
            'order' => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'order_date'   => $order->order_date,
                'status'       => $order->status,
                'subtotal'     => $order->subtotal,
                'discount'     => $order->discount,
                'cgst'         => $order->cgst,
                'sgst'         => $order->sgst,
                'igst'         => $order->igst,
                'round_off'    => $order->round_off,
                'total_amount' => $order->total_amount,

                'retailer' => [
                    'id'    => $order->retailer->id,
                    'retailer_name'  => $order->retailer->retailer_name,
                    'state' => $order->retailer->state,
                ],

                'items' => $order->items->map(fn ($item) => [
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
                    'quantity'  => $item->quantity,
                    'rate'      => $item->rate,
                    'base_unit' => $item->base_unit,
                    'discount_percent' => $item->discount_percent,
                    'total'     => $item->total,
                ]),
            ],
        ]);
    }

    /**
     * Update retail order (only if pending)
     */
    public function update(Request $request, $id)
    {
        $sales = $request->user();

        $order = RetailOrder::where('id', $id)
            ->whereNotNull('retailer_id')
            ->where('created_by_type', get_class($sales))
            ->where('created_by_id', $sales->id)
            ->firstOrFail();

        abort_if($order->status !== 'pending', 403, 'Order cannot be edited.');

        $validated = $this->validatedData($request, $order);

       $retailer = Retailer::findOrFail($validated['retailer_id']);

        $billingAddress = $retailer?->formatted_billing_address;

        $order = SaveRetailOrderAction::update($order, [
            'order_number'    => $request->order_number ?? $order->order_number,
            'order_date'      => $validated['order_date'],
            'retailer_id'     => $validated['retailer_id'],
            'billing_address' => $billingAddress,
            'discount'        => $request->discount ?? 0,
            'items'           => $validated['items'],
            'latitude'        => $request->latitude ?? null,
            'longitude'       => $request->longitude ?? null,
        ]);

        return response()->json([
            'message'  => 'Retail order updated successfully',
            'order_id' => $order->id,
        ]);
    }

    /**
     * Preview retail order
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'retailer_id' => ['required', 'exists:retailers,id'],
            'discount'    => ['nullable', 'numeric', 'min:0'],
            'items'       => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $calculation = OrderCalculationService::calculateForRetailer(
            $validated['items'],
            $validated['retailer_id'],
            $validated['discount'] ?? 0
        );

        return response()->json([
            'preview' => $calculation,
        ]);
    }

    /**
     * Validate create & update
     */
    private function validatedData(Request $request, ?RetailOrder $order = null): array
    {
        return $request->validate(
            [
                'retailer_id' => ['required', 'exists:retailers,id'],
                'order_date'  => ['required', 'date'],
                'order_number' => ['nullable', 'max:50'],
                'discount'    => ['nullable', 'numeric'],
                'items'       => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'exists:products,id'],
                'items.*.quantity'   => ['required', 'integer', 'min:1'],
                'latitude'    => ['nullable', 'numeric'],
                'longitude'   => ['nullable', 'numeric'],
            ],
            [
                'retailer_id.required' => 'Please select a retailer.',
                'items.required'       => 'Please add at least one product.',
                'items.min'            => 'At least one product is required.',
            ]
        );
    }
}
