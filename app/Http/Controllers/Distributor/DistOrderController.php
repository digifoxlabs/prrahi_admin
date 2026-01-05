<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OrderActivityLogger;
use App\Services\OrderDeliveryService;


class DistOrderController extends Controller
{



    //List All Orders
    public function index(Request $request){

       
        $title = 'Orders';

        $distributor_id = auth('distributor')->id();

       $query = Order::with(['distributor',
        'activities:id,order_id,event,created_at',
        ])
        ->where('distributor_id', $distributor_id)
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

        return view('distributor.orders.index', compact('orders', 'title'));

    }


    //Show Orders
    public function show(Order $order)
    {
        $title = 'Orders';
        $order->load('items.product','distributor');
        return view('distributor.orders.show', compact('order','title'));
    }


    //Mark Order Delivery
    public function deliver(Request $request, Order $order)
    {
        // Guard: must be dispatched
        if ($order->dispatch_status !== 'dispatched') {
            return back()->with('error', 'Order must be dispatched before delivery.');
        }

        // Guard: already delivered
        if ($order->dispatch_status === 'delivered') {
            return back()->with('error', 'Order is already delivered.');
        }

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
            //✅ EXACTLY LIKE LOGGER
            OrderDeliveryService::handle($order);

    

        });

        return back()->with('success', 'Order marked as delivered.');
}


    //Create Order 
    public function create()
    {
        $title = 'Orders';
        
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
            'layout'      => 'distributor.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'distributor',               // or distributor / sales
            'products'    => $products,
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'title' => $title,
        ]);


    }




}
