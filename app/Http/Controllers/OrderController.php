<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Services\Orders\{
    CreateOrderService,
    AddOrderItemsService
};
use App\Services\OrderActivityLogger;
use Illuminate\Support\Str;
use App\Support\OrderActor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\InventoryTransaction;


class OrderController extends Controller
{
    /* =====================
       CREATE
    ======================*/
    public function create(Request $request)
    {
        $products = Product::with('parent')->orderBy('name')->get();

        return view('orders.create', compact('products'));
    }

    /* =====================
       STORE
    ======================*/
    public function store(Request $request)
    {
     
        //  $validated = $request->validate([
        //     'distributor_id' => ['required', 'exists:distributors,id'],
        //     'order_date'     => ['required', 'date'],
        //     'items'          => ['required', 'array', 'min:1'],
        //     'items.*.product_id' => ['required', 'exists:products,id'],
        //     'items.*.quantity'   => ['required', 'integer', 'min:1'],
        //     'items.*.rate'       => ['required', 'numeric', 'min:0'],
        //     'items.*.amount'     => ['required', 'numeric'],
        //  ],
        //     [
        //         // Distributor & Order
        //         'distributor_id.required' => 'Please select a distributor.',
        //         'distributor_id.exists'   => 'Selected distributor is invalid.',
        //         'order_date.required'     => 'Order date is required.',
        //         'order_date.date'         => 'Order date must be a valid date.',

        //         // Items
        //         'items.required'          => 'Please add at least one product to the order.',
        //         'items.array'             => 'Invalid product data submitted.',
        //         'items.min'               => 'At least one product must be added.',

        //         // Item fields
        //         'items.*.product_id.required' => 'Product selection is required.',
        //         'items.*.product_id.exists'   => 'Selected product does not exist.',
        //         'items.*.quantity.required'   => 'Quantity is required.',
        //         'items.*.quantity.integer'    => 'Quantity must be a whole number.',
        //         'items.*.quantity.min'        => 'Quantity must be at least 1.',
        //         'items.*.rate.required'       => 'Rate is required.',
        //         'items.*.rate.numeric'        => 'Rate must be a number.',
        //         'items.*.rate.min'            => 'Rate cannot be negative.',
        //         'items.*.amount.required'     => 'Amount is required.',
        //         'items.*.amount.numeric'      => 'Amount must be numeric.',
        //     ]      
        
        // );


        //use Validator
        // $validator = Validator::make(
        //     $request->all(),
        //     [
        //         'distributor_id' => ['required', 'exists:distributors,id'],
        //         'order_date'     => ['required', 'date'],
        //         'order_number'   => ['nullable', 'max:50', 'unique:orders,order_number'],
        //         'items'          => ['required', 'array', 'min:1'],
        //         'items.*.product_id' => ['required', 'exists:products,id'],
        //         'items.*.quantity'   => ['required', 'integer', 'min:1'],
        //     ],
        //     [
        //         'distributor_id.required' => 'Please select a distributor.',
        //         'order_number.unique' => 'Duplicate Order Number',
        //         'items.required'          => 'Please add at least one product to the order.',
        //         'items.min' => 'Please add at least one product before saving the order.',
        //     ]
        // );

        // if ($validator->fails()) {
        //     return back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // $validated = $validator->validated();



         $validated = $this->validatedData($request);







        /** Detect actor */
       // [$actorType, $actorId] = $this->resolveActor();
        $actor = OrderActor::resolve();

        //Generate Order Number if not provided
        $orderNumber = $request->order_number ?: 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

        // echo $orderNumber;
        // exit;

        $order = CreateOrderService::create([
            'order_number'     => $orderNumber,
            'order_date'      => $request->order_date,
            'distributor_id'  => $request->distributor_id,
            'billing_address' => $request->billing_address,

            'subtotal'        => $request->subtotal,
            'discount'        => $request->discount_amount ?? 0,
            'cgst'            => $request->cgst,
            'sgst'            => $request->sgst,
            'igst'            => $request->igst ?? 0,
            'round_off'       => $request->round_off ?? 0,
            'total_amount'    => $request->total_amount,

            'status'          => 'pending',
            'created_by_type'  => $actor['type'],
            'created_by_id'    => $actor['id'],

        ]);

        // $items = collect($validated['items'])->map(fn ($row) => [
        $items = collect($request->items)->map(fn ($row) => [
            
                'order_id' => $order->id,
                'product_id' => $row['product_id'],
                'price'   => $row['rate'],
                'base_unit' => $row['base_unit'],
                'quantity'  => $row['quantity'],
                'discount_percent'   => $row['discount_percent'],
                'total'   => $row['amount'],

        ])->toArray();

     
        AddOrderItemsService::handle($order, $items);

        OrderActivityLogger::log($order, 'created', 'Order created');


         return $this->redirectAfterSave($order, $actor['role'])
         ->with('success', 'Order created successfully.');


        // return redirect()->route($this->redirectRoute(), $order)
        //     ->with('success', 'Order created successfully.');
    }

    /* =====================
       EDIT
    ======================*/
    // public function edit(Order $order)
    // {
    //     $products = Product::with('parent')->orderBy('name')->get();
    //     $order->load('items.product');

    //     return view('orders.edit', compact('order', 'products'));
    // }

    /* =====================
       UPDATE
    ======================*/
    public function update(Request $request, Order $order)
    {
        abort_if($order->status !== 'pending', 403, 'Order cannot be edited.');


          // Same validation as store
        // $request->validate([
        //     'distributor_id' => ['required', 'exists:distributors,id'],
        //     'order_date'     => ['required', 'date'],
        //     'items'          => ['required', 'array', 'min:1'],
        //     'items.*.product_id' => ['required', 'exists:products,id'],
        //     'items.*.quantity'   => ['required', 'integer', 'min:1'],
        //     'items.*.rate'       => ['required', 'numeric', 'min:0'],
        //     'items.*.amount'     => ['required', 'numeric'],
        // ]);



        //use Validator
        // $validator = Validator::make(
        //     $request->all(),
        //     [
        //         'distributor_id' => ['required', 'exists:distributors,id'],
        //         'order_date'     => ['required', 'date'],
        //         'order_number'   => ['nullable', 'max:50', Rule::unique('orders', 'order_number')->ignore($order->id)],
        //         'items'          => ['required', 'array', 'min:1'],
        //         'items.*.product_id' => ['required', 'exists:products,id'],
        //         'items.*.quantity'   => ['required', 'integer', 'min:1'],
        //     ],
        //     [
        //         'distributor_id.required' => 'Please select a distributor.',
        //         'order_number.unique' => 'Duplicate Order Number',
        //         'items.required'          => 'Please add at least one product to the order.',
        //         'items.min' => 'Please add at least one product before saving the order.',
        //     ]
        // );

        // if ($validator->fails()) {
        //     return back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // $validated = $validator->validated();


        $validated = $this->validatedData($request, $order);

        $actor = OrderActor::resolve();

        DB::transaction(function () use ($request, $order) {

            // 1️⃣ Update order header
            $order->update([
                'distributor_id'  => $request->distributor_id,
                'order_number'    => $request->order_number,
                'order_date'      => $request->order_date,
                'billing_address' => $request->billing_address,
                'subtotal'        => $request->subtotal,
                'discount'        => $request->discount_amount ?? 0,
                'cgst'            => $request->cgst ?? 0,
                'sgst'            => $request->sgst ?? 0,
                'igst'            => $request->igst ?? 0,
                'round_off'       => $request->round_off ?? 0,
                'total_amount'    => $request->total_amount,
            ]);

            // 2️⃣ Remove old items
            $order->items()->delete();


        $items = collect($request->items)->map(fn ($row) => [
            
                'order_id' => $order->id,
                'product_id' => $row['product_id'],
                'price'   => $row['rate'],
                'base_unit' => $row['base_unit'],
                'quantity'  => $row['quantity'],
                'discount_percent'   => $row['discount_percent'],
                'total'   => $row['amount'],

        ])->toArray();

        
        AddOrderItemsService::handle($order, $items);

        OrderActivityLogger::log($order, 'updated', 'Order updated');

    });


         return $this->redirectAfterUpdate($order, $actor['role'])->with('success', 'Order updated successfully.');
        
        

      //  return back()->with('success', 'Order updated successfully.');
    }

    /* =====================
       HELPERS
    ======================*/
    protected function resolveActor(): array
    {
        if (auth('admin')->check()) {
            return [\App\Models\User::class, auth('admin')->id()];
        }

        if (auth('distributor')->check()) {
            return [\App\Models\Distributor::class, auth('distributor')->id()];
        }

        return [\App\Models\SalesPerson::class, auth('sales')->id()];
    }

    protected function redirectRoute(): string
    {
        if (auth('admin')->check()) return 'admin.orders.show';
        if (auth('distributor')->check()) return 'distributor.orders.show';
        return 'sales.orders.show';
    }



    protected function redirectAfterSave(Order $order, string $actor)
    {
        return match ($actor) {
            'admin'       => redirect()->route('admin.orders.index'),
            'distributor' => redirect()->route('distributor.orders.index'),
            'sales'       => redirect()->route('sales.orders.index'),
            default       => abort(403),
        };
    }


    protected function redirectAfterUpdate(Order $order, string $actor)
    {
        return match ($actor) {
            'admin'       => redirect()->route('admin.orders.show', $order),
            'distributor' => redirect()->route('distributor.orders.show', $order),
            'sales'       => redirect()->route('sales.orders.show', $order),
            default       => abort(403),
        };
    }

    protected function redirectIndex(Order $order, string $actor){

        return match ($actor) {
            'admin'       => redirect()->route('admin.orders.index', $order),
            // 'distributor' => redirect()->route('distributor.orders.index', $order),
            // 'sales'       => redirect()->route('sales.orders.index', $order),
            default       => abort(403),
        };

    }


    // Validate Request Data for both create and edit
    private function validatedData(Request $request, ?Order $order = null): array
    {
        return $request->validate([

            'distributor_id' => ['required', 'exists:distributors,id'],
            'order_date'     => ['required', 'date'],
            'order_number'   => ['nullable', 'max:50', Rule::unique('orders', 'order_number')->ignore($order?->id)],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],

        ],
    
        [
                'distributor_id.required' => 'Please select a distributor.',
                'order_number.unique' => 'Duplicate Order Number',
                'items.required'          => 'Please add at least one product to the order.',
                'items.min' => 'Please add at least one product before saving the order.',
        ] );
    }


    //Cancel Order
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

         $actor = OrderActor::resolve();
        
        return $this->redirectAfterUpdate($order, $actor['role'])->with('success', 'Order Cancelled successfully.');

        // return back()->with('success', 'Order cancelled successfully.');
    }



    // ================= PRINT / DOWNLOAD INVOICE =================
    public function printInvoice(Order $order)
    {
        abort_if($order->invoice_status !== 'generated', 403);

        $order->load([
            'items.product.parent',
            'distributor'
        ]);

        return view('orders.invoice-print', compact('order'));
    }




}
