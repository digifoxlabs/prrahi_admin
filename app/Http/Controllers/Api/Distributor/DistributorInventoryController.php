<?php

namespace App\Http\Controllers\Api\Distributor;

use App\Http\Controllers\Controller;
use App\Models\DistributorInventoryTransaction;
use Illuminate\Http\Request;

class DistributorInventoryController extends Controller
{
    public function index(Request $request)
    {
        $distributorId = auth('distributor_api')->id();

        $productId = $request->query('product_id');
        $type = $request->query('type'); // in | out

        $transactions = DistributorInventoryTransaction::with('distributorProduct')
            ->where('distributor_id', $distributorId)
            ->when($productId, fn ($q) => $q->where('distributor_product_id', $productId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return response()->json([
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'has_more' => $transactions->hasMorePages(),
            ],
        ]);
    }
}
