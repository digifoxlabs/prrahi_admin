<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailOrder;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\SalesPerson;
use App\Models\Retailer;
use Illuminate\Support\Facades\DB;
use App\Services\RetailOrderActivityLogger;

class DistRetailOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){

       
        $title = 'Orders';

        $distributor_id = auth('distributor')->id();

       $query = RetailOrder::with(['distributor','retailer'])
        ->where(function ($q) use ($distributor_id) {

            $q->where('distributor_id', $distributor_id);

        })->latest();

        // 🔍 Search by Order Number
        if ($request->filled('q')) {
            $query->where('order_number', 'like', '%' . $request->q . '%');
        }

        // 🏷 Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query
            ->paginate(20)
            ->appends($request->query());// keep filters during pagination

        return view('distributor.retail-orders.index', compact('orders', 'title'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Retail Orders';

        $distributor_id = auth('distributor')->id();
        
        // Load products with category and variants (with their categories)
        $products = Product::whereIn('type', ['simple', 'variable'])
            ->with([
                'category', // Load category for parent products
                'variants.parent',
                'variants.category' // Load category for variants too if needed
            ])
            ->orderBy('name')
            ->get();
        
        // Transform the data if needed
        $products->transform(function ($product) {
            if ($product->type === 'variable' && $product->variants) {
                // Ensure each variant has access to parent's category if variant doesn't have its own
                $product->variants->each(function ($variant) use ($product) {
                    if (!$variant->category && $product->category) {
                        $variant->category = $product->category;
                    }
                });
            }
            return $product;
        });
        

        return view('retailers.orders.create', [
            'layout'      => 'distributor.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'distributor',               // or distributor / sales
            'products'    => $products,
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'retailers'=> Retailer::orderBy('retailer_name')->where('distributor_id',$distributor_id)->get(),
            'title' => $title,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RetailOrder $order)
    {
        $title = 'Orders';
        $eligibleDistributors = [];

        if ($order->retailer && $order->retailer->distributor_id) {
            $eligibleDistributors = \App\Models\Distributor::where(
                'id',
                $order->retailer->distributor_id
            )->get();
        }

        $order->load('items.product','distributor');
        return view('distributor.retail-orders.show', compact('order','title','eligibleDistributors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RetailOrder $order)
    {
        

         if ($order->status !== 'pending') {
                    return redirect()
                    ->route('sales.retail.orders.show', $order)
                    ->with('error', 'Confirmed or cancelled orders cannot be edited.');
            }

        $title = 'Orders';
      

        $order->load([
            'items.product.parent',
            'distributor','retailer',
        ]);

        $sales_id = auth('sales')->id();

        // Load products with category and variants (with their categories)
        $products = Product::whereIn('type', ['simple', 'variable'])
            ->with([
                'category', // Load category for parent products
                'variants.parent',
                'variants.category' // Load category for variants too if needed
            ])
            ->orderBy('name')
            ->get();
        
        // Transform the data if needed
        $products->transform(function ($product) {
            if ($product->type === 'variable' && $product->variants) {
                // Ensure each variant has access to parent's category if variant doesn't have its own
                $product->variants->each(function ($variant) use ($product) {
                    if (!$variant->category && $product->category) {
                        $variant->category = $product->category;
                    }
                });
            }
            return $product;
        });



        // Build cart items safely for Alpine
        $cartItems = $order->items->map(function ($item) {

            $product = $item->product;

            $name = $product->type === 'variant'
                ? $product->parent->name
                : $product->name;

            if ($product->attributes) {
                $name .= ' - ' . ($product->attributes['fragrance'] ?? '');
                if (!empty($product->attributes['size'])) {
                    $name .= ' (' . $product->attributes['size'] . ')';
                }
            }

            return [
                'id'        => $product->id,
                'name'      => $name,
                'code'      => $product->code,
                'qty'       => (int) $item->quantity,
                'rate'      => (float) $item->rate,
                // 'discount'  => (float) ($product->distributor_discount_percent ?? 0),
                'discount'  => (float) ($item->discount_percent ?? 0),
                'base_unit' => $item->base_unit,
                'amount'    => (float) $item->total,
            ];
        });


     

        return view('retailers.orders.edit', [
            'layout'      => 'distributor.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'distributor',               // or distributor / sales
            'products'     => $products,
            'order'        => $order,
            // 'distributors'=> Distributor::orderBy('firm_name')->get(),
            'distributors'=> Distributor::orderBy('firm_name')->where('sales_persons_id', $sales_id)->get(),
            'retailers'=> Retailer::orderBy('retailer_name')->get(),
            'cartItems'    => $cartItems,
            'title' => $title,
        ]);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


public function confirm(Request $request, RetailOrder $order)
{

    if ($order->status !== 'pending') {
        return back()->with('error', 'Only pending orders can be confirmed.');
    }

    $request->validate([
        'admin_comments' => ['nullable', 'string', 'max:2000'],
    ]);


    DB::transaction(function () use ($order, $request) {


        $order->update([
            'status'          => 'confirmed',
            'admin_comments'  => $request->admin_comments,
        ]);


        RetailOrderActivityLogger::log(
            $order,
            'confirmed',
            $request->admin_comments // mandatory comment
        );

    });

    return back()->with('success', 'Order confirmed successfully.');
}


public function cancel(Request $request, RetailOrder $order)
{
    if ($order->status === 'cancelled') {
        return back()->with('error', 'Order is already cancelled.');
    }

    $request->validate([
        'admin_comments' => ['nullable', 'string', 'max:2000'],
    ]);

    DB::transaction(function () use ($order, $request) {

        $order->update([
            'status'         => 'cancelled',
            'admin_comments' => $request->admin_comments,
        ]);


            RetailOrderActivityLogger::log(
            $order,
            'cancelled',
            $request->admin_comments
            );

    });

    return back()->with('success', 'Order cancelled successfully.');
}








}
