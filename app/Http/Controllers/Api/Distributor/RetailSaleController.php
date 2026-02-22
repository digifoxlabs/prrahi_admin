<?php

namespace App\Http\Controllers\Api\Distributor;

use App\Http\Controllers\Controller;
use App\Models\DistributorInventoryTransaction;
use App\Models\DistributorStock;
use App\Models\Retailer;
use App\Models\RetailerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RetailSaleController extends Controller
{
    public function index(Request $request)
    {
        $distributorId = auth('distributor_api')->id();

        $query = RetailerSale::with('retailer')
            ->where('distributor_id', $distributorId);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('retailer', function ($rq) use ($search) {
                    $rq->where('retailer_name', 'like', "%{$search}%");
                })
                ->orWhere('sale_date', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        $sales = $query
            ->orderByDesc('sale_date')
            ->paginate(15)
            ->withQueryString();

        return response()->json([
            'data' => $sales->items(),
            'meta' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'has_more' => $sales->hasMorePages(),
            ],
        ]);
    }

    public function create()
    {
        $distributorId = auth('distributor_api')->id();

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

        return response()->json([
            'retailers' => $retailers,
            'stocks' => $stocks,
            'as_on_date' => now()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'retailer_id' => 'required|exists:retailers,id',
            'sale_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:distributor_products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $distributorId = auth('distributor_api')->id();
        $saleDate = $validated['sale_date'] ?? now()->toDateString();

        /** @var \App\Models\RetailerSale $sale */
        $sale = DB::transaction(function () use ($validated, $distributorId, $saleDate) {
            $sale = RetailerSale::create([
                'distributor_id' => $distributorId,
                'retailer_id' => $validated['retailer_id'],
                'sale_date' => $saleDate,
                'total_qty' => 0,
            ]);

            $totalQty = 0;

            foreach ($validated['items'] as $index => $item) {
                $stock = DistributorStock::where('distributor_id', $distributorId)
                    ->where('distributor_product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item['qty'] > $stock->available_qty) {
                    throw ValidationException::withMessages([
                        "items.$index.qty" => "Only {$stock->available_qty} units available for this product.",
                    ]);
                }

                $stock->decrement('available_qty', $item['qty']);

                $sale->items()->create([
                    'distributor_product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                ]);

                DistributorInventoryTransaction::create([
                    'distributor_id' => $distributorId,
                    'distributor_product_id' => $item['product_id'],
                    'type' => 'out',
                    'quantity' => $item['qty'],
                    'source_type' => RetailerSale::class,
                    'source_id' => $sale->id,
                    'remarks' => 'Retailer sale',
                ]);

                $totalQty += $item['qty'];
            }

            $sale->update(['total_qty' => $totalQty]);

            return $sale;
        });

        return response()->json([
            'message' => 'Retailer sale recorded successfully.',
            'sale_id' => $sale->id,
        ], 201);
    }

    public function show($id)
    {
        $distributorId = auth('distributor_api')->id();

        $retailerSale = RetailerSale::with([
            'retailer',
            'items.product',
        ])
            ->where('id', $id)
            ->where('distributor_id', $distributorId)
            ->firstOrFail();

        return response()->json([
            'sale' => $retailerSale,
        ]);
    }

    public function destroy($id)
    {
        $distributorId = auth('distributor_api')->id();

        $retailerSale = RetailerSale::with('items')
            ->where('id', $id)
            ->where('distributor_id', $distributorId)
            ->firstOrFail();

        DB::transaction(function () use ($retailerSale, $distributorId) {
            foreach ($retailerSale->items as $item) {
                DistributorStock::where('distributor_id', $distributorId)
                    ->where('distributor_product_id', $item->distributor_product_id)
                    ->increment('available_qty', $item->quantity);

                DistributorInventoryTransaction::create([
                    'distributor_id' => $distributorId,
                    'distributor_product_id' => $item->distributor_product_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'source_type' => RetailerSale::class,
                    'source_id' => $retailerSale->id,
                    'remarks' => 'Retailer sale reversed',
                ]);
            }

            $retailerSale->delete();
        });

        return response()->json([
            'message' => 'Retailer sale deleted and stock restored.',
        ]);
    }
}
