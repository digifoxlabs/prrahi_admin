<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributorInventoryTransaction;

class DistributorInventoryLedgerController extends Controller
{
    public function index(Request $request)
    {
        $distributorId = auth('distributor')->id();

        $productId = $request->query('product_id');
        $type      = $request->query('type'); // in | out

        $transactions = DistributorInventoryTransaction::with('distributorProduct')
            ->where('distributor_id', $distributorId)
            ->when($productId, fn ($q) =>
                $q->where('distributor_product_id', $productId)
            )
            ->when($type, fn ($q) =>
                $q->where('type', $type)
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('distributor.stock.ledger', [
            'transactions' => $transactions,
            'title'        => 'Inventory Ledger',
        ]);
    }
}