<?php

namespace App\Http\Controllers\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function index(Request $request)
{
    $title = 'Invoices';

    $query = Order::with('distributor')
        ->where('invoice_status', 'generated');

    if ($request->filled('q')) {
        $q = $request->q;

        $query->where(function ($sub) use ($q) {
            $sub->where('order_number', 'like', "%{$q}%")
                ->orWhere('invoice_no', 'like', "%{$q}%")
                ->orWhereHas('distributor', function ($d) use ($q) {
                    $d->where('firm_name', 'like', "%{$q}%");
                });
        });
    }

    $invoices = $query
        ->orderByDesc('invoice_date')
        ->paginate(20)
        ->withQueryString();

    return view('admin.invoices.index', compact('invoices', 'title'));
}

}
