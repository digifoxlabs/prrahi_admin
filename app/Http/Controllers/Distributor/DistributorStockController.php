<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributorStock;

class DistributorStockController extends Controller
{

    public function index()
    {
        $title = 'My Inventory';

        $stocks = DistributorStock::with('product')
            ->where('distributor_id', auth('distributor')->id())
            ->orderByDesc('available_qty')
            ->paginate(15)        // ✅ pagination
            ->withQueryString();

        return view('distributor.stock.index', compact('stocks', 'title'));
    }





}
