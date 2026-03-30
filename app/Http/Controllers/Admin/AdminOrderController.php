<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\SalesType;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OrderActivityLogger;
use App\Services\OrderDeliveryService;


class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_orders')->only(['index', 'show', 'printInvoice']);
        $this->middleware('permission:create_orders')->only(['create']);
        $this->middleware('permission:edit_orders')->only([
            'edit',
            'confirm',
            'cancel',
            'dispatch',
            'deliver',
            'markInvoiceGenerated',
            'removeInvoice',
            'updateSalesType',
        ]);
        $this->middleware('permission:delete_orders')->only(['destroy']);
    }

    /*****************************
     * Lists all Distributor Orders
     *****************************/
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

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('town')) {
            $query->where('town', $request->town);
        }

        $orders = $query
            ->paginate(20)
            ->appends($request->query()); // keep filters during pagination

        $districts = Order::query()
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        $towns = Order::query()
            ->whereNotNull('town')
            ->where('town', '!=', '')
            ->distinct()
            ->orderBy('town')
            ->pluck('town');

        return view('admin.orders.index', compact('orders', 'title', 'districts', 'towns'));
    }


    /*************************
     * Create Order Page
     ************************/
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

        //Loads shared view for admin/distributor/sales
        return view('orders.create', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'products'    => $products,
            'distributors' => Distributor::orderBy('firm_name')->get(),
            'title' => $title,
        ]);
    }

    /*******************
     * View Order Page
     *******************/

    public function show(Order $order)
    {
        $title = 'Orders';
        $salesType = SalesType::all();
        $order->load([
            'items.product.parent',
            'items.product.inventoryTransactions',
            'distributor',
        ]);
        return view('admin.orders.show', compact('order', 'title', 'salesType'));
    }

    /**************************
     * Edit order Page
     ************************/

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

        //Shared Edit View for Admin/Distributors/Sales

        return view('orders.edit', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'products'     => $products,
            'order'        => $order,
            'distributors' => Distributor::withTrashed()->orderBy('firm_name')->get(),
            'cartItems'    => $cartItems,
            'title' => $title,
        ]);
    }

    /*****************************************
     * Store and Update from Shared Controller
     ***************************************/
 

    /*********************
     * Confirm Order by Admin
     ***********************/
    public function confirm(Request $request, Order $order)
    {

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be confirmed.');
        }

        if (!$order->sales_type_id) {
            return back()->withErrors([
                'sales_type_id' => 'Please select Sales Type before confirming the order.'
            ]);
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


    /********************
     * Cancel Order
     ****************/
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

    /*****************
     * Change Order Dispatch Status
     ***********************/
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


    /*******************
     * Order Delivery Status
     ********************/

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


    /***************************
     * Manual Invoice From Admin
     ************************/
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
                'Invoice generated manually by admin (Invoice No: ' . $request->invoice_no . ')'
            );
        });

        return back()->with('success', 'Invoice marked as generated.');
    }


    /********************
     * Remove Invoice
     ******************/
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

    /*********************
     * Print & Download Invoice
     *************************/
    public function printInvoice(Order $order)
    {
        abort_if($order->invoice_status !== 'generated', 403);

        $order->load([
            'items.product.parent',
            'distributor'
        ]);

        return view('admin.orders.invoice-print', compact('order'));
    }


    /***************
     * Set Order Sales Type
     *****************/

    public function updateSalesType(Request $request, Order $order)
    {
        if ($order->invoice_status == 'generated') {
            abort(403, 'Sales Type cannot be changed after invoice generation.');
        }

        $validated = $request->validate([
            'sales_type_id' => ['required', 'exists:sales_types,id'],
        ]);

        $order->update([
            'sales_type_id' => $validated['sales_type_id'],
        ]);

        return back()->with('success', 'Sales type updated.');
    }
}
