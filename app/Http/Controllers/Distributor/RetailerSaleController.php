<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    RetailerSale,
    RetailerSaleItem,
    DistributorStock,
    Retailer,
    Distributor,
    DistributorInventoryTransaction
};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class RetailerSaleController extends Controller
{
    /* =========================
       INDEX
    ==========================*/
    public function index(Request $request)
    {

        $title= "Retail Sale";
        $distributorId = auth('distributor')->id();

        $sales = RetailerSale::with('retailer')
            ->where('distributor_id', $distributorId)
            ->orderByDesc('sale_date')
            ->paginate(15);

        return view('distributor.retailer-sales.index', compact('sales','title'));
    }

    /* =========================
       CREATE
    ==========================*/
    public function create()
    {

        $title= "Retail Sale";
        $distributorId = auth('distributor')->id();

        $retailers = Retailer::where(function ($q) use ($distributorId) {
                $q->where('distributor_id', $distributorId)
                  ->orWhere(function ($q2) use ($distributorId) {
                      $q2->where('appointed_by_type', \App\Models\Distributor::class)
                         ->where('appointed_by_id', $distributorId);
                  });
            })
            ->orderBy('retailer_name')
            ->get();

        $stocks = DistributorStock::with('product')
            ->where('distributor_id', $distributorId)
            ->where('available_qty', '>', 0)
            ->orderByDesc('available_qty')
            ->get();

        return view('distributor.retailer-sales.create', compact(
            'retailers',
            'stocks','title'
        ));
    }

    /* =========================
       STORE
    ==========================*/
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'retailer_id' => 'required|exists:retailers,id',
    //         'sale_date'   => 'required|date',
    //         'items'       => 'required|array|min:1',
    //         'items.*.product_id' => 'required|exists:distributor_products,id',
    //         'items.*.qty' => 'required|integer|min:1',
    //     ]);

    //     $distributorId = auth('distributor')->id();

    //     DB::transaction(function () use ($request, $distributorId) {

    //         $sale = RetailerSale::create([
    //             'distributor_id' => $distributorId,
    //             'retailer_id'    => $request->retailer_id,
    //             'sale_date'      => $request->sale_date,
    //             'total_qty'      => 0,
    //         ]);

    //         $totalQty = 0;

    //         foreach ($request->items as $item) {

    //             $stock = DistributorStock::where('distributor_id', $distributorId)
    //                 ->where('distributor_product_id', $item['product_id'])
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             if ($item['qty'] > $stock->available_qty) {
    //                 throw new \Exception('Insufficient stock.');
    //             }

    //             // Deduct stock
    //             $stock->decrement('available_qty', $item['qty']);

    //             // Save sale item
    //             $sale->items()->create([
    //                 'distributor_product_id' => $item['product_id'],
    //                 'quantity' => $item['qty'],
    //             ]);

    //             // Inventory OUT
    //             DistributorInventoryTransaction::create([
    //                 'distributor_id' => $distributorId,
    //                 'distributor_product_id' => $item['product_id'],
    //                 'type' => 'out',
    //                 'quantity' => $item['qty'],
    //                 'source_type' => RetailerSale::class,
    //                 'source_id'   => $sale->id,
    //                 'remarks'     => 'Retailer sale',
    //             ]);

    //             $totalQty += $item['qty'];
    //         }

    //         $sale->update(['total_qty' => $totalQty]);
    //     });

    //     return redirect()
    //         ->route('distributor.retailer-sales.index')
    //         ->with('success', 'Retailer sale recorded successfully.');
    // }


public function store(Request $request)
{
    $request->validate([
        'retailer_id' => 'required|exists:retailers,id',
        'sale_date'   => 'required|date',
        'items'       => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:distributor_products,id',
        'items.*.qty' => 'required|integer|min:1',
    ]);

    $distributorId = auth('distributor')->id();

    try {
        DB::transaction(function () use ($request, $distributorId) {

            $sale = RetailerSale::create([
                'distributor_id' => $distributorId,
                'retailer_id'    => $request->retailer_id,
                'sale_date'      => $request->sale_date,
                'total_qty'      => 0,
            ]);

            $totalQty = 0;

            foreach ($request->items as $index => $item) {

                $stock = DistributorStock::where('distributor_id', $distributorId)
                    ->where('distributor_product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item['qty'] > $stock->available_qty) {
                    throw ValidationException::withMessages([
                        "items.$index.qty" =>
                            "Only {$stock->available_qty} units available for this product.",
                    ]);
                }

                // Deduct stock
                $stock->decrement('available_qty', $item['qty']);

                // Save sale item
                $sale->items()->create([
                    'distributor_product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                ]);

                // Inventory OUT
                DistributorInventoryTransaction::create([
                    'distributor_id' => $distributorId,
                    'distributor_product_id' => $item['product_id'],
                    'type' => 'out',
                    'quantity' => $item['qty'],
                    'source_type' => RetailerSale::class,
                    'source_id'   => $sale->id,
                    'remarks'     => 'Retailer sale',
                ]);

                $totalQty += $item['qty'];
            }

            $sale->update(['total_qty' => $totalQty]);
        });

    } catch (ValidationException $e) {
        return back()
            ->withErrors($e->errors())
            ->withInput();
    }

    return redirect()
        ->route('distributor.retailer-sales.index')
        ->with('success', 'Retailer sale recorded successfully.');
}


    /* =========================
       SHOW
    ==========================*/
    // public function show(RetailerSale $retailerSale)
    // {
    //     $title = "Retail Sale";

    //     // $distributorId = auth('distributor')->id();

    //     // abort_if(
    //     //     !$distributorId || $retailerSale->distributor_id !== $distributorId,
    //     //     403,
    //     //     'Unauthorized access'
    //     // );

    //     $retailerSale->load(['retailer', 'items.product']);

    //     return view('distributor.retailer-sales.show', compact('retailerSale', 'title'));
    // }



public function show($id)
{
    $title = "Retail Sale";
    $distributorId = auth('distributor')->id();

    $retailerSale = RetailerSale::with([
            'retailer',
            'items.product'
        ])
        ->where('id', $id)
        ->where('distributor_id', $distributorId)
        ->firstOrFail();

    return view('distributor.retailer-sales.show', compact('retailerSale', 'title'));
}




    /* =========================
       DESTROY (REVERSE STOCK)
    ==========================*///
public function destroy($id)
{
    $distributorId = auth('distributor')->id();

    // Fetch sale scoped to distributor (same pattern as show)
    $retailerSale = RetailerSale::with('items')
        ->where('id', $id)
        ->where('distributor_id', $distributorId)
        ->firstOrFail();

    DB::transaction(function () use ($retailerSale, $distributorId) {

        foreach ($retailerSale->items as $item) {

            // Restore distributor stock
            DistributorStock::where('distributor_id', $distributorId)
                ->where('distributor_product_id', $item->distributor_product_id)
                ->increment('available_qty', $item->quantity);

            // Inventory IN transaction (reverse sale)
            DistributorInventoryTransaction::create([
                'distributor_id'         => $distributorId,
                'distributor_product_id' => $item->distributor_product_id,
                'type'                   => 'in',
                'quantity'               => $item->quantity,
                'source_type'            => RetailerSale::class,
                'source_id'              => $retailerSale->id,
                'remarks'                => 'Retailer sale reversed',
            ]);
        }

        // Delete sale (items will delete if FK cascade is set)
        $retailerSale->delete();
    });

    return redirect()
        ->route('distributor.retailer-sales.index')
        ->with('success', 'Retailer sale deleted and stock restored.');
}



}