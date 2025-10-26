<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Setting;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;


class AdminOrderController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:view_orders')->only(['index', 'show']);
        $this->middleware('permission:create_orders')->only(['create', 'store']);
        $this->middleware('permission:edit_orders')->only(['edit', 'update']);
        $this->middleware('permission:delete_orders')->only(['destroy']);
    }



public function index(Request $request)
{
    $title = 'Order';
    $search = $request->input('search');
    $status = $request->input('status'); // <-- new filter

    $orders = Order::with('distributor')
        ->when($search, function ($query, $search) {
            $query->where('order_number', 'like', "%{$search}%")
                ->orWhereHas('distributor', function ($q) use ($search) {
                    $q->where('firm_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
        })
        ->when($status, function ($query, $status) {
            $query->where('status', $status);
        })
        ->latest()
        ->paginate(15);

    return view('admin.orders.index', compact('orders', 'search', 'status', 'title'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Order';
        $distributors = Distributor::select('*')->get();
        $freeDozenCase = Setting::get('orders', 'free-dozen', false);
        $products = Product::whereIn('type', ['simple', 'variant'])
        ->with('parent')
        ->get();

        return view('admin.orders.create', compact('distributors', 'products','freeDozenCase','title'));


    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{

    $validated = $request->validate([
        'order_number' => 'nullable|string|max:255',
        'distributor_id' => 'required|exists:distributors,id',
        'order_date' => 'required|date',
        'product_ids' => 'required|array|min:1',
        'product_ids.*' => 'required|exists:products,id',
        'prices' => 'required|array|min:1',
        'prices.*' => 'required|numeric|min:0',
        'dozen_cases' => 'required|array|min:1',
        'dozen_cases.*' => 'required|integer|min:0',
        'quantities' => 'required|array|min:1',
        'quantities.*' => 'required|integer|min:1',
        'totals' => 'required|array|min:1',
        'totals.*' => 'required|numeric|min:0',
        'discount'=> 'nullable|numeric|min:0', // ← add this
    ]);



    $checkStock = Setting::get('products', 'check_stock_before_order', true);
    $insufficientStock = [];

    if ($checkStock) {
        
        foreach ($validated['product_ids'] as $index => $productId) {

            $product = Product::find($productId);
            $productName = ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ');

            $availableStock = $product->getAvailableStock();
            $requestedQty = $validated['quantities'][$index];

            if ($requestedQty > $availableStock) {
                $insufficientStock[] = [
                    'product_name' => $productName,
                    'available' => $availableStock,
                    'requested' => $requestedQty,
                ];
            }
        }

        if (count($insufficientStock) > 0) {
            $messages = collect($insufficientStock)->map(function ($item) {
                return "{$item['product_name']} has only {$item['available']} in stock. You requested {$item['requested']}.";
            });

            return redirect()->back()
                ->withInput()
                ->withErrors(['stock_error' => $messages->implode('<br>')]);
        }


    }



    DB::beginTransaction();

    try {


        // Calculate subtotal
        $subtotal = collect($validated['totals'])->sum();

        //Discount
        $discount = $validated['discount'] ?? 0;

        // Calculate taxes (assume SGST and CGST 9% each)
        $sgst = round($subtotal * 0.09, 2);
        $cgst = round($subtotal * 0.09, 2);
        $totalAmount = $subtotal + $sgst + $cgst;
        $totalAmount = $totalAmount - $discount;

        // Create the order
        $order = \App\Models\Order::create([    
            'order_number'   => $validated['order_number'] ?? null,      
            'distributor_id' => $validated['distributor_id'],
            'subtotal' => $subtotal,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'status' => 'pending',             
            'created_by_id'    => auth('admin')->user()->id,   // 👇 Created by admin guard user  
            'created_by_type'  => \App\Models\User::class,
            'created_at' => $validated['order_date'],
            'updated_at' => now(),
        ]);



        // Save order items
        foreach ($validated['product_ids'] as $index => $productId) {


            $tmpFreeDozen =0;
            $totalDozenCase =0;
             $tmpTotal  =0;

             //Fetch Free Dozen Per Case from Settings
            $freeDozenCase = Setting::get('orders', 'free-dozen', false);

            // Fetch product model
            $product = Product::findOrFail($productId);

            //Check Free Dozen for each product IDs
            if(!$product->has_free_qty){

                $freeDozenCase = 0;
            }
            
            //Free Dozen per quantity
            $tmpFreeDozen = $freeDozenCase * $validated['quantities'][$index];

            //dozen per case deducting free dozen
            $totalDozenCase = ( $validated['dozen_cases'][$index] * $validated['quantities'][$index] ) - $tmpFreeDozen;      

            //Server callculated total
            $tmpTotal =  round($validated['prices'][$index] * $totalDozenCase, 2);


            // Check DB calculated total and posted total
                if ($tmpTotal != $validated['totals'][$index]) {
                    // Mismatch → rollback immediately
                    DB::rollBack();
                    return back()->withErrors(['totals' => "Total mismatch for product ID: {$productId}"]);
                }


                    $order->items()->create([
                        'product_id' => $productId,
                        'rate' => $validated['prices'][$index],
                        'dozen_case' =>$validated['dozen_cases'][$index],
                        'free_dozen_case' => $freeDozenCase,
                        'quantity' => $validated['quantities'][$index],
                        'total' => $validated['totals'][$index],
                    ]);


                    // Create HOLD stock transaction
                    InventoryTransaction::create([
                        'product_id' => $productId,
                        'type' => 'hold',
                        'quantity' => $validated['quantities'][$index],
                        'order_id' => $order->id,
                        'remarks' => 'New Order',
                    ]);


        
      


        }

    

        DB::commit();

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully!');
    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Something went wrong. ' . $e->getMessage()]);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $title = 'Order';
         $order->load(['distributor', 'items.product']); // Load distributor and each product in items

        return view('admin.orders.show', compact('order','title'));
    }


    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    
        $title = 'Order';

            $order = \App\Models\Order::with('items.product.parent.category')->findOrFail($id);

            $distributors = \App\Models\Distributor::all();

             $freeDozenCase = Setting::get('orders', 'free-dozen', false);

            // Only fetch products of type 'simple' and 'variant'
            $products = \App\Models\Product::whereIn('type', ['simple', 'variant'])
                ->with('parent.category')
                ->get();

            return view('admin.orders.edit', compact('order', 'distributors', 'products', 'title','freeDozenCase'));

    }

    /**
     * Update the specified resource in storage.
     */
 public function update(Request $request, $id)
{


    $validated = $request->validate([
        'order_number' => 'nullable|string|max:255',
        'distributor_id' => 'required|exists:distributors,id',
        'order_date' => 'required|date',
        'product_ids' => 'required|array|min:1',
        'product_ids.*' => 'required|exists:products,id',
        'prices' => 'required|array|min:1',
        'prices.*' => 'required|numeric|min:0',
        'dozen_cases' => 'required|array|min:1',
        'dozen_cases.*' => 'required|integer|min:0',
        'quantities' => 'required|array|min:1',
        'quantities.*' => 'required|integer|min:1',
        'totals' => 'required|array|min:1',
        'totals.*' => 'required|numeric|min:0',
        'discount'=> 'nullable|numeric|min:0',
    ]);


    $checkStock = Setting::get('products', 'check_stock_before_order', true);
    $insufficientStock = [];

    if ($checkStock) {
        
        foreach ($validated['product_ids'] as $index => $productId) {
            $product = Product::find($productId);
            $productName = ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ');

            $availableStock = $product->getAvailableStock();
            $requestedQty = $validated['quantities'][$index];

            if ($requestedQty > $availableStock) {
                $insufficientStock[] = [
                    'product_name' => $productName,
                    'available' => $availableStock,
                    'requested' => $requestedQty,
                ];
            }
        }

        if (count($insufficientStock) > 0) {
            $messages = collect($insufficientStock)->map(function ($item) {
                return "{$item['product_name']} has only {$item['available']} in stock. You requested {$item['requested']}.";
            });

            return redirect()->back()
                ->withInput()
                ->withErrors(['stock_error' => $messages->implode('<br>')]);
        }


    }



    DB::beginTransaction();

    try {
        $order = \App\Models\Order::findOrFail($id);

        // Calculate subtotal
        $subtotal = collect($validated['totals'])->sum();

        // Discount
        $discount = $validated['discount'] ?? 0;

        // Taxes
        $sgst = round($subtotal * 0.09, 2);
        $cgst = round($subtotal * 0.09, 2);
        $totalAmount = $subtotal + $sgst + $cgst - $discount;

        // Update the order
        $order->update([
            'order_number'   => $validated['order_number'] ?? null,
            'distributor_id' => $validated['distributor_id'],
            'subtotal'       => $subtotal,
            'sgst'           => $sgst,
            'cgst'           => $cgst,
            'discount'       => $discount,
            'total_amount'   => $totalAmount,
            'created_at'     => $validated['order_date'], // for backdated updates
            'updated_at'     => now(),
        ]);

        // Remove existing items and add updated ones
        $order->items()->delete();
        //Remove existing transactions
        $this->cancelInventory($order);

        foreach ($validated['product_ids'] as $index => $productId) {


             $tmpFreeDozen =0;
             $tmpDozen = 0;
             $tmpTotal  = 0;

             //Fetch Free Dozen Per Case from Settings
            $freeDozenCase = Setting::get('orders', 'free-dozen', false);

            // Fetch product model
            $product = Product::findOrFail($productId);

            //Check Free Dozen for each product IDs
            if(!$product->has_free_qty){

                $freeDozenCase = 0;
            }


            //Free Dozen per quantity
            $tmpFreeDozen = $freeDozenCase * $validated['quantities'][$index];

            //dozen per case deducting free dozen
            $totalDozenCase = ( $validated['dozen_cases'][$index] * $validated['quantities'][$index] ) - $tmpFreeDozen;      

            //Server callculated total
            $tmpTotal =  round($validated['prices'][$index] * $totalDozenCase, 2);


             // Check DB calculated total and posted total
                if ($tmpTotal != $validated['totals'][$index]) {
                    // Mismatch → rollback immediately
                    DB::rollBack();
                    return back()->withErrors(['totals' => "Total mismatch for product ID: {$productId} tmp {$tmpTotal} form {$validated['totals'][$index]}"]);
                }



            $order->items()->create([
                'product_id'  => $productId,
                'rate'        => $validated['prices'][$index],
                'dozen_case'  => $validated['dozen_cases'][$index],
                'free_dozen_case'  => $freeDozenCase,
                'quantity'    => $validated['quantities'][$index],
                'total'       => $validated['totals'][$index],
            ]);


            // Create HOLD stock transaction
            InventoryTransaction::create([
                'product_id' => $productId,
                'type' => 'hold',
                'quantity' => $validated['quantities'][$index],
                'order_id' => $order->id,
                'remarks' => 'New Order Updated',
            ]);



        }

        DB::commit();

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Something went wrong. ' . $e->getMessage()]);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Delete related order items first
            $order->items()->delete();

            // Delete the order itself
            $order->delete();

            //Remove hold transactions
            $this->cancel($order);
            

            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Failed to delete order. ' . $e->getMessage());
        }
    }


    //Cancel Pending Order
    public function cancel(Order $order)
    {

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                // Remove hold
                InventoryTransaction::where('order_id', $order->id)
                    ->where('product_id', $item->product_id)
                    ->where('type', 'hold')->delete();              
            }

            $order->update(['status' => 'cancelled']);
        });
        
    return redirect()->back()->with('success', 'Order cancelled.');
    }


    //Confirm Pending Order
    public function confirm(Order $order)
        {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    // Remove hold
                    InventoryTransaction::where('order_id', $order->id)
                        ->where('product_id', $item->product_id)
                        ->where('type', 'hold')->delete();

                    // Create OUT transaction
                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'order_id' => $order->id,
                        'remarks' => 'Order Confirmed'
                    ]);
                }

                $order->update(['status' => 'confirmed']);
            });

            return redirect()->back()->with('success', 'Order Confirmed.');
        }


        //Cancel Holding Inventory
        public function cancelInventory(Order $order)
        {
            DB::transaction(function () use ($order) {
                // Remove hold transactions
                InventoryTransaction::where('order_id', $order->id)
                    ->where('type', 'hold')->delete();

               // $order->update(['status' => 'cancelled']);
            });
        }




}
