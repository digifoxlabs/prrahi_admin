<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OrderActivityLogger;
use App\Services\OrderDeliveryService;


class AdminOrderController extends Controller
{


    public function index(Request $request)
    {
        $title = 'Orders';

        $query = Order::with([
                'distributor',
                'activities:id,order_id,event,created_at'
            ])->latest();

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

        return view('admin.orders.index', compact('orders', 'title'));


        
    }



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
        
        // return view('admin.orders.create', [
        //     'distributors' => Distributor::orderBy('firm_name')->get(),
        //     'products' => $products,
        //     'title' => $title,
        // ]);



        return view('orders.create', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'products'    => $products,
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'title' => $title,
        ]);




    }

//  public function store(Request $request)
// {
//     $request->validate([
//         'distributor_id' => ['required', 'exists:distributors,id'],
//         'order_date'     => ['required', 'date'],
//         'items'          => ['required', 'array', 'min:1'],
//         'items.*.product_id' => ['required', 'exists:products,id'],
//         'items.*.quantity'   => ['required', 'integer', 'min:1'],
//         'items.*.rate'       => ['required', 'numeric', 'min:0'],
//         'items.*.amount'     => ['required', 'numeric'],
//     ]);

//     DB::transaction(function () use ($request) {

//         // 1️⃣ Generate Order Number if not provided
//         $orderNumber = $request->order_number ?: 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

//         // 2️⃣ Create Order
//         $order = Order::create([
//             'order_number'    => $orderNumber,
//             'order_date'      => $request->order_date,
//             'distributor_id'  => $request->distributor_id,
//             'billing_address' => $request->billing_address,

//             'subtotal'        => $request->subtotal,
//             'discount'        => $request->discount_amount ?? 0,
//             'cgst'            => $request->cgst,
//             'sgst'            => $request->sgst,
//             'round_off'       => $request->round_off ?? 0,
//             'total_amount'    => $request->total_amount,

//             'status'          => 'pending',
//         ]);

//         // 3️⃣ Save creator (Admin / Distributor later)
//         $order->created_by()->associate(auth()->user());
//         $order->save();

//         // 4️⃣ Create Order Items
//         foreach ($request->items as $item) {

//             OrderItem::create([
//                 'order_id'   => $order->id,
//                 'product_id' => $item['product_id'],
//                 'rate'       => $item['rate'],
//                 'base_unit'  => $item['base_unit'],
//                 'quantity'   => $item['quantity'],
//                 'discount_percent'   => $item['discount_percent'],
//                 'total'      => $item['amount'],
//             ]);

//         }


//         OrderActivityLogger::log(
//             $order,
//             'created',
//             'Order created'
//         );

//     });

//     return redirect()
//         ->route('admin.orders.index')
//         ->with('success', 'Order created successfully.');
// }





    public function show(Order $order)
    {
        $title = 'Orders';
        $order->load('items.product','distributor');
        return view('admin.orders.show', compact('order','title'));
    }

    public function edit(Order $order)
    {

         if ($order->status !== 'pending') {
                    return redirect()
                    ->route('admin.orders.show', $order)
                    ->with('error', 'Confirmed or cancelled orders cannot be edited.');
            }

        $title = 'Orders';
        $order->load([
            'items.product.parent',
            'distributor'
        ]);


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

        // return view('admin.orders.edit', [
        //     'order'        => $order,
        //     'products'     => Product::whereIn('type', ['simple', 'variable'])
        //                             ->with('variants.parent')
        //                             ->orderBy('name')
        //                             ->get(),
        //     'distributors' => Distributor::orderBy('firm_name')->get(),
        //     'cartItems'    => $cartItems,
        //     'title'     =>$title,
        // ]);



        return view('orders.edit', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'products'     => $products,
            'order'        => $order,
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'cartItems'    => $cartItems,
            'title' => $title,
        ]);


    }





// public function update(Request $request, Order $order)
// {


//     if ($order->status !== 'pending') {
//         return redirect()
//             ->route('admin.orders.show', $order)
//             ->with('error', 'Confirmed or cancelled orders cannot be updated.');
//     }




//     $request->validate([
//         'distributor_id' => ['required', 'exists:distributors,id'],
//         'order_date'     => ['required', 'date'],
//         'items'          => ['required', 'array', 'min:1'],
//         'items.*.product_id' => ['required', 'exists:products,id'],
//         'items.*.quantity'   => ['required', 'integer', 'min:1'],
//         'items.*.rate'       => ['required', 'numeric', 'min:0'],
//         'items.*.amount'     => ['required', 'numeric'],
//     ]);

//     DB::transaction(function () use ($request, $order) {

//         // 1️⃣ Update order header
//         $order->update([
//             'distributor_id'  => $request->distributor_id,
//             'order_number'    => $request->order_number,
//             'order_date'      => $request->order_date,
//             'billing_address' => $request->billing_address,
//             'subtotal'        => $request->subtotal,
//             'discount'        => $request->discount_amount ?? 0,
//             'cgst'            => $request->cgst,
//             'sgst'            => $request->sgst,
//             'round_off'       => $request->round_off ?? 0,
//             'total_amount'    => $request->total_amount,
//         ]);

//         // 2️⃣ Remove old items
//         $order->items()->delete();

//         // 3️⃣ Insert updated items
//         foreach ($request->items as $item) {
//             OrderItem::create([
//                 'order_id'   => $order->id,
//                 'product_id' => $item['product_id'],
//                 'rate'       => $item['rate'],
//                 'base_unit'  => $item['base_unit'],
//                 'quantity'   => $item['quantity'],
//                 'discount_percent'   => $item['discount_percent'],
//                 'total'      => $item['amount'],
//             ]);
//         }
//     });

//     // return redirect()
//     //     ->back()
//     //     ->with('success', 'Order updated successfully.');

//     return redirect()
//     ->route('admin.orders.show', $order)
//     ->with('success', 'Order updated successfully.');


// }


public function confirm(Request $request, Order $order)
{

    if ($order->status !== 'pending') {
        return back()->with('error', 'Only pending orders can be confirmed.');
    }

    $request->validate([
        'admin_comments' => ['nullable', 'string', 'max:2000'],
    ]);


    DB::transaction(function () use ($order, $request) {


        $order->load(['items.product.inventoryTransactions']);

        $errors = [];

        foreach ($order->items as $item) {
            $product = $item->product;

            $availableStock = $product->getAvailableStock();

            if ($availableStock < $item->quantity) {
                $errors[$item->id] = "Insufficient stock. Available: {$availableStock}";
            }
        }

        // ❌ If any stock issue → stop
        if (!empty($errors)) {
            return back()->with('stock_errors', $errors);
        }

        // Deduct stock
        foreach ($order->items as $item) {
            InventoryTransaction::create([
                'product_id' => $item->product_id,
                'order_id'   => $order->id,
                'type'       => 'out',
                'quantity'   => $item->quantity,
                'remarks'    => 'Order Confirmed - ' . $order->order_number,
                'date'       => now(),
            ]);
        }

        $order->update([
            'status'          => 'confirmed',
            'admin_comments'  => $request->admin_comments,
        ]);


        OrderActivityLogger::log(
            $order,
            'confirmed',
            $request->admin_comments // mandatory comment
        );

    });

    return back()->with('success', 'Order confirmed successfully.');
}


public function cancel(Request $request, Order $order)
{
    if ($order->status === 'cancelled') {
        return back()->with('error', 'Order is already cancelled.');
    }

    $request->validate([
        'admin_comments' => ['nullable', 'string', 'max:2000'],
    ]);

    DB::transaction(function () use ($order, $request) {

        if ($order->status === 'confirmed') {
            $order->load('items');

            foreach ($order->items as $item) {
                InventoryTransaction::create([
                    'product_id' => $item->product_id,
                    'order_id'   => $order->id,
                    'type'       => 'in',
                    'quantity'   => $item->quantity,
                    'remarks'    => 'Order Cancelled - ' . $order->order_number,
                    'date'       => now(),
                ]);
            }
        }

        $order->update([
            'status'         => 'cancelled',
            'admin_comments' => $request->admin_comments,
        ]);


            OrderActivityLogger::log(
            $order,
            'cancelled',
            $request->admin_comments
            );

    });

    return back()->with('success', 'Order cancelled successfully.');
}


public function dispatch(Request $request, Order $order)
{
    // Guard: status
    if ($order->status !== 'confirmed') {
        return back()->with('error', 'Only confirmed orders can be dispatched.');
    }

    // Guard: invoice
    if ($order->invoice_status !== 'generated') {
        return back()->with('error', 'Invoice must be generated before dispatch.');
    }

    // Guard: already dispatched
    if ($order->dispatch_status === 'dispatched') {
        return back()->with('error', 'Order is already dispatched.');
    }

    DB::transaction(function () use ($order) {

        // Update order
        $order->update([
            'dispatch_status' => 'dispatched',
        ]);

        // Log activity
        OrderActivityLogger::log(
            $order,
            'dispatched',
            'Order dispatched'
        );
    });

    return back()->with('success', 'Order dispatched successfully.');
}



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


public function markInvoiceGenerated(Request $request, Order $order)
{
    // Guard 1: must be confirmed
    if ($order->status !== 'confirmed') {
        return back()->with('error', 'Only confirmed orders can be invoiced.');
    }

    // Guard 2: already invoiced
    if ($order->invoice_status === 'generated') {
        return back()->with('error', 'Invoice is already generated.');
    }

    // Validation
    $request->validate([
        'invoice_no'   => ['required', 'string', 'max:100'],
        'invoice_date' => ['required', 'date'],
    ]);

    DB::transaction(function () use ($order, $request) {

        // Update invoice details
        $order->update([
            'invoice_no'     => $request->invoice_no,
            'invoice_date'   => $request->invoice_date,
            'invoice_status' => 'generated',
            'bill_generated' => true,
        ]);

        // Log activity
        OrderActivityLogger::log(
            $order,
            'invoice_generated',
            'Invoice generated manually by admin (Invoice No: '.$request->invoice_no.')'
        );
    });

    return back()->with('success', 'Invoice marked as generated.');
}


// ================= REMOVE INVOICE =================
public function removeInvoice(Order $order)
{
    if ($order->invoice_status !== 'generated') {
        return back()->with('error', 'Invoice not generated.');
    }

    DB::transaction(function () use ($order) {

        // 1️⃣ Roll back invoice fields
        $order->update([
            'invoice_no'     => null,
            'invoice_date'   => null,
            'invoice_status' => 'pending',
            'bill_generated' => false,
        ]);

        // 2️⃣ Remove invoice-generated activity
        $order->activities()
            ->where('event', 'invoice_generated')
            ->delete();

        // 3️⃣ Add rollback activity (audit trail)
        // $order->activities()->create([
        //     'event'        => 'invoice_removed',
        //     'remarks' => 'Invoice removed by admin',
        // ]);

        // 4️⃣ Ensure order stays CONFIRMED (not pending)
        // (no status change required, but this is explicit)
        if ($order->status !== 'confirmed') {
            $order->update(['status' => 'confirmed']);
        }
    });

    return back()->with('success', 'Invoice removed and order rolled back to confirmed stage.');
}

    // ================= PRINT / DOWNLOAD INVOICE =================
    public function printInvoice(Order $order)
    {
        abort_if($order->invoice_status !== 'generated', 403);

        $order->load([
            'items.product.parent',
            'distributor'
        ]);

        return view('admin.orders.invoice-print', compact('order'));
    }

}
