<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Distributor;
use App\Services\Orders\OrderCalculationService;
use App\Actions\Orders\{
    SaveOrderAction
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
       STORE DISTRIBUTOR ORDER
    ======================*/
    public function store(Request $request)
    {
           

        $validated = $this->validatedData($request);    
        $distributorLocation = $this->resolveDistributorLocation((int) $validated['distributor_id']);

        /** Detect actor */
       // [$actorType, $actorId] = $this->resolveActor();
       $actor = OrderActor::resolve();

        //Generate Order Number if not provided
        $orderNumber = $request->order_number ?: 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));


        $order = SaveOrderAction::create([
                ...$validated,
                'discount' => $request->discount_amount ?? 0,
                'order_number' => $orderNumber,
                'town' => $distributorLocation['town'],
                'district' => $distributorLocation['district'],
        ]);

         return $this->redirectAfterSave($order, $actor['role'])
         ->with('success', 'Order created successfully.');


    }

    /* =====================
       UPDATE
    ======================*/
    public function update(Request $request, Order $order)
    {
        abort_if($order->status !== 'pending', 403, 'Order cannot be edited.');

        $validated = $this->validatedData($request, $order);
        $distributorLocation = $this->resolveDistributorLocation((int) $validated['distributor_id']);

        $actor = OrderActor::resolve();

        $order = SaveOrderAction::update($order, [
            'order_number'    => $request->order_number ?? $order->order_number,
            'order_date'      => $validated['order_date'],
            'distributor_id'  => $validated['distributor_id'],
            'billing_address' => $request->billing_address,
            'discount'        => $request->discount_amount ?? 0,
            'town'            => $distributorLocation['town'],
            'district'        => $distributorLocation['district'],
            'items'           => $validated['items'],
        ]);

        return $this->redirectAfterUpdate($order, $actor['role'])->with('success', 'Order updated successfully.');
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'distributor_id'       => ['required', 'exists:distributors,id'],
            'discount'             => ['nullable', 'numeric', 'min:0'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
        ]);

        $calculation = OrderCalculationService::calculateForDistributor(
            $validated['items'],
            (int) $validated['distributor_id'],
            (float) ($validated['discount'] ?? 0)
        );

        return response()->json([
            'preview' => $calculation,
        ]);
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

    private function resolveDistributorLocation(int $distributorId): array
    {
        $distributor = Distributor::query()
            ->select(['id', 'town', 'district'])
            ->findOrFail($distributorId);

        return [
            'town' => $distributor->town,
            'district' => $distributor->district,
        ];
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
