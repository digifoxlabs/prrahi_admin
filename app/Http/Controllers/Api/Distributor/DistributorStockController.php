<?php

namespace App\Http\Controllers\Api\Distributor;

use App\Http\Controllers\Controller;
use App\Models\DistributorStock;
use Illuminate\Http\Request;

class DistributorStockController extends Controller
{
    public function index(Request $request)
    {
        $distributor = auth('distributor_api')->user();

        $query = DistributorStock::with('product')
            ->where('distributor_id', $distributor->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%");
            });
        }

        $stocks = $query
            ->orderByDesc('available_qty')
            ->paginate(15)
            ->withQueryString();

        return response()->json([
            'data' => $stocks->items(),
            'meta' => [
                'current_page' => $stocks->currentPage(),
                'last_page' => $stocks->lastPage(),
                'has_more' => $stocks->hasMorePages(),
            ],
        ]);
    }
}
