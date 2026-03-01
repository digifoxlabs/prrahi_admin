<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailOrder;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\SalesPerson;
use App\Models\Retailer;
use Illuminate\Support\Facades\DB;
use App\Services\RetailOrderActivityLogger;

class AdminRetailOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_orders')->only(['index', 'show']);
        $this->middleware('permission:create_orders')->only(['create']);
        $this->middleware('permission:edit_orders')->only(['edit', 'confirm', 'cancel', 'assignDistributor']);
        $this->middleware('permission:delete_orders')->only(['destroy']);
    }

    public function index(Request $request)
    {


        $title = 'Orders';

        $distributor_id = auth('distributor')->id();

        //fetch all retail orders
        $query = RetailOrder::with(['distributor', 'retailer'])->latest();

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
            ->appends($request->query()); // keep filters during pagination

        return view('admin.retail-orders.index', compact('orders', 'title'));
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

        $order->load('items.product', 'distributor');
        return view('admin.retail-orders.show', compact('order', 'title', 'eligibleDistributors'));
    }



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
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'products'    => $products,
            'distributors' => Distributor::orderBy('firm_name')->get(),
            'retailers' => Retailer::orderBy('retailer_name')->get(),
            'title' => $title,
        ]);
    }



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
            'distributor',
            'retailer',
        ]);

        // $sales_id = auth('sales')->id();

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
                'discount'  => (float) ($item->discount_percent ?? 0),
                'base_unit' => $item->base_unit,
                'amount'    => (float) $item->total,
            ];
        });



        return view('retailers.orders.edit', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'products'     => $products,
            'order'        => $order,
            'distributors' => Distributor::orderBy('firm_name')->get(),
            'retailers' => Retailer::withTrashed()->orderBy('retailer_name')->get(),
            'cartItems'    => $cartItems,
            'title' => $title,
        ]);
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



    public function assignDistributor(Request $request, RetailOrder $order){
        
        abort_if($order->status !== 'pending', 403);

        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
        ]);

        // Ensure distributor belongs to retailer
        if (
            !$order->retailer ||
            $order->retailer->distributor_id != $request->distributor_id
        ) {
            abort(403, 'Invalid distributor selection');
        }

        $order->update([
            'distributor_id' => $request->distributor_id,
        ]);

        return back()->with('success', 'Distributor assigned successfully.');

           

    }






}
