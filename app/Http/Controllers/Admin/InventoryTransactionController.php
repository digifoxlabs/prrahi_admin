<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        //  $title ='Inventory';   
        // $transactions = InventoryTransaction::with(['product', 'variant'])
        // ->latest()
        // ->paginate(15);

        //  return view('admin.inventory.index', compact('transactions','title'));


    // $title ='Inventory';   
    // $products = Product::with('variants')->get();
    // $productId = $request->query('product_id');

    // $transactions = InventoryTransaction::with(['product', 'variant'])
    //     ->when($productId, function ($query) use ($productId) {
    //         $query->where('product_id', $productId);
    //     })
    //     ->orderByDesc('date')
    //     ->paginate(10)
    //     ->withQueryString();

    // return view('admin.inventory.index', compact('transactions', 'products', 'productId','title'));



    $title = 'Inventory';

    // Only simple products and variants (with parent loaded for variants)
    $products = Product::whereIn('type', ['simple', 'variant'])
        ->with('parent')
        ->get();

    $productId = $request->query('product_id');
    $productName = null;

    if ($productId) {
        $product = Product::with('parent')->find($productId);
        if ($product) {
            if ($product->type === 'variant') {
                $productName = ($product->parent->name ?? '') . ' - ' . ($product->attributes['fragrance'] ?? '');
            } else {
                $productName = $product->name;
            }
        }
    }

    $transactions = InventoryTransaction::with(['product.parent'])
        ->when($productId, function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })
        ->orderByDesc('date')
        ->paginate(10)
        ->withQueryString();

    return view('admin.inventory.index', compact('transactions', 'products', 'productId','productName', 'title'));



    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
           $title = 'Add Inventory Transaction';

            // Get only simple products and variants (with parent relationship for variants)
            $products = Product::with('parent')
                ->whereIn('type', ['simple', 'variant'])
                ->get();



                $productId = $request->query('product_id');

                $transaction = new InventoryTransaction([
                'product_id' => $productId,
                'date' => now()->format('Y-m-d'),
                ]);


            return view('admin.inventory.create', compact('title', 'products','transaction'));

    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {

    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'type' => 'required|in:in,out,adjustment',
        'quantity' => 'required|integer|min:1',
        'remarks' => 'nullable|string',
        'date' => 'required|date',
    ]);



    InventoryTransaction::create([
        'product_id' => $validated['product_id'],
        'type' => $validated['type'],
        'quantity' => $validated['quantity'],
        'remarks' => $validated['remarks'],
        'date' => $validated['date'],
    ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory transaction added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryTransaction $inventory)
    {
        $title ='Inventory'; 
        // $products = Product::with('variants')->get();
        // return view('admin.inventory.edit', compact('inventory', 'products','title'));




    // Get only simple products and variants (with parent relationship for variants)
    $products = Product::with('parent')
        ->whereIn('type', ['simple', 'variant'])
        ->get();

    return view('admin.inventory.edit', compact('title', 'inventory', 'products'));

    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, InventoryTransaction $inventory)
{

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'type' => 'required|in:in,out,adjustment',
        'quantity' => 'required|integer|min:1',
        'remarks' => 'nullable|string',
        'date' => 'required|date',
    ]);

    $inventory->update([
        'product_id' => $request->product_id,
        'type' => $request->type,
        'quantity' => $request->quantity,
        'remarks' => $request->remarks,
        'date' => $request->date,
    ]);



    return redirect()->route('admin.inventory.index')->with('success', 'Inventory transaction updated.');
}
    /**
     * Remove the specified resource from storage.
     */
     public function destroy(InventoryTransaction $inventory)
    {
        $inventory->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory entry deleted.');
    }


    private function validateInventory(Request $request, $id = null)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'type' => 'required|in:opening,purchase,sale,return,adjustment',
            'quantity' => 'required|numeric|min:0',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);


        if ($validated['type'] === 'opening') {
            $exists = InventoryTransaction::where('type', 'opening')
                ->where('product_id', $validated['product_id'])
                ->when($validated['variant_id'], fn($q) => $q->where('variant_id', $validated['variant_id']))
                ->when(!$validated['variant_id'], fn($q) => $q->whereNull('variant_id'))
                ->when($id, fn($q) => $q->where('id', '!=', $id))
                ->exists();

            if ($exists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'type' => 'Opening stock for this product already exists.',
                ]);
            }
        }

        return $validated;
    }



    public function export(Request $request)
    {
        $productId = $request->query('product_id');

        return Excel::download(new InventoryExport($productId), 'inventory-export.xlsx');
    }





}
