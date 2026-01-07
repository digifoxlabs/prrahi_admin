<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\SalesPerson;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){

       
        $title = 'Orders';

        $sales_id = auth('sales')->id();

       $query = Order::with(['distributor','activities:id,order_id,event,created_at',])
        ->where(function ($q) use ($sales_id) {

            $q->where('created_by_type', SalesPerson::class)
                ->where('created_by_id', $sales_id);

        })
        ->latest();

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

        return view('sales.orders.index', compact('orders', 'title'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Orders';

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
        

        return view('orders.create', [
            'layout'      => 'sales.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'sales',               // or distributor / sales
            'products'    => $products,
            'distributors'=> Distributor::orderBy('firm_name')->where('sales_persons_id', $sales_id)->get(),
            'title' => $title,
        ]);


    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Common Shared OrderController
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $title = 'Orders';
        $order->load('items.product','distributor');
        return view('sales.orders.show', compact('order','title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
}
