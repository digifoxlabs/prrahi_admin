<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:view_products')->only(['index', 'show']);
        $this->middleware('permission:create_products')->only(['create', 'store']);
        $this->middleware('permission:edit_products')->only(['edit', 'update']);
        $this->middleware('permission:delete_products')->only(['destroy']);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    
    $title = 'Products';
    $search = $request->query('search');

     $products = Product::with(['category', 'subCategory', 'parent.category', 'parent.subCategory'])
        ->whereIn('type', ['simple', 'variant'])  // ✅ Only simple and variant products
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('parent', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subCategory', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        })
        ->orderByDesc('id')
        ->paginate(20)
        ->withQueryString();

    return view('admin.products.index', compact('title', 'products', 'search'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Products';   
        $segments = Category::whereNull('parent_id')->with('children')->get();
        return view('admin.products.create', compact('segments', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

         $request->validate([
            'name' => 'required',
            'type' => 'required|in:simple,variable',
            'category_id' => 'required',
            'unit' => 'required',
            'variants' => 'required_if:type,variable|array|min:1',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'code'=> $request->code,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'unit' => $request->unit,
            'dozen_per_case' => $request->type == 'simple' ? $request->dozen_per_case : null,
            'free_dozen_per_case' => $request->type == 'simple' ? $request->free_dozen_per_case : null,
            'mrp_per_unit' => $request->type == 'simple' ? $request->mrp_per_unit : null,
            'ptr_per_dozen' => $request->type == 'simple' ? $request->ptr_per_dozen : null,
            'ptd_per_dozen' => $request->type == 'simple' ? $request->ptd_per_dozen : null,
            'weight_gm' => $request->type == 'simple' ? $request->weight_gm : null,
            'size' => $request->type == 'simple' ? $request->size : null,
            'attributes' => $request->type == 'simple' ? null : [],
        ]);

        if ($request->type == 'variable' && $request->variants) {
            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'name' => null,
                    'code' => $variant['code'],
                    'type' => 'variant',
                    'parent_id' => $product->id,
                    'category_id' => null,
                    'sub_category_id' => null,
                    'unit' => null,
                    'dozen_per_case' => $variant['dozen_per_case'],
                    'free_dozen_per_case' => $variant['free_dozen_per_case'],
                    'mrp_per_unit' => $variant['mrp_per_unit'],
                    'ptr_per_dozen' => $variant['ptr_per_dozen'],
                    'ptd_per_dozen' => $variant['ptd_per_dozen'],
                    'weight_gm' => $variant['weight_gm'],
                    'size' => $variant['size'],
                    'attributes' => [
                        'fragrance' => $variant['fragrance'],
                        'size' => $variant['size'],
                    ],
                ]);
            }
        }

    return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $title ='Products';  

        $segments = Category::whereNull('parent_id')->get();
        $product->load('category', 'subCategory');

        $attributes = $product->attributes ?? [];
        $fragrance = $attributes['fragrance'] ?? '';
        $sizeAttr = $attributes['size'] ?? '';

        return view('admin.products.edit', compact('product', 'segments', 'fragrance', 'sizeAttr','title'));

        
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, Product $product)
    {
 

    $validated = $request->validate([

        'name' => 'required|string|max:255',
        'code'=> 'nullable',
        'category_id' => 'nullable|exists:categories,id',
        'sub_category_id' => 'nullable|exists:categories,id',
        'unit' => 'nullable|string|max:20',
        'type' => 'required|in:simple,variable,variant',

        // For simple product
        'dozen_per_case' => 'nullable|numeric',
        'free_dozen_per_case' => 'nullable|numeric',
        'mrp_per_unit' => 'nullable|numeric',
        'ptr_per_dozen' => 'nullable|numeric',
        'ptd_per_dozen' => 'nullable|numeric',
        'weight_gm' => 'nullable|numeric',
        'size' => 'nullable|string|max:255',

        // For variant product
        'fragrance' => 'nullable|string|max:255',

        // For multiple variants (variable product)
        'variants.*.code' => 'nullable',
        'variants' => 'nullable|array',
        'variants.*.id' => 'nullable|exists:products,id',
        'variants.*.fragrance' => 'nullable|string|max:255',
        'variants.*.size' => 'nullable|string|max:255',
        'variants.*.dozen_per_case' => 'nullable|numeric',
        'variants.*.free_dozen_per_case' => 'nullable|numeric',
        'variants.*.mrp_per_unit' => 'nullable|numeric',
        'variants.*.ptr_per_dozen' => 'nullable|numeric',
        'variants.*.ptd_per_dozen' => 'nullable|numeric',
        'variants.*.weight_gm' => 'nullable|numeric',
    ]);

    DB::transaction(function () use ($validated, $request, $product) {
        if ($product->type === 'simple') {
            $product->update([
                'name' => $validated['name'],
                'code'=> $validated['code'],
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'],
                'unit' => $validated['unit'],
                'dozen_per_case' => $validated['dozen_per_case'],
                'free_dozen_per_case' => $validated['free_dozen_per_case'],
                'mrp_per_unit' => $validated['mrp_per_unit'],
                'ptr_per_dozen' => $validated['ptr_per_dozen'],
                'ptd_per_dozen' => $validated['ptd_per_dozen'],
                'weight_gm' => $validated['weight_gm'],
                'attributes' => ['size' => $validated['size']],
            ]);
        }

        if ($product->type === 'variable') {
            $product->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'],
                'unit' => $validated['unit'],
            ]);

            // Update existing variants
            if (isset($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    if (isset($variantData['id'])) {
                        $variant = Product::where('parent_id', $product->id)
                            ->where('id', $variantData['id'])
                            ->first();

                        if ($variant) {
                            $variant->update([      
                                'code' =>  $variantData['code'],                        
                                'dozen_per_case' => $variantData['dozen_per_case'],
                                'free_dozen_per_case' => $variantData['free_dozen_per_case'],
                                'mrp_per_unit' => $variantData['mrp_per_unit'],
                                'ptr_per_dozen' => $variantData['ptr_per_dozen'],
                                'ptd_per_dozen' => $variantData['ptd_per_dozen'],
                                'weight_gm' => $variantData['weight_gm'],
                                'attributes' => [
                                    'fragrance' => $variantData['fragrance'],
                                    'size' => $variantData['size'],
                                ],
                            ]);
                        }
                    }
                }
            }
        }

        if ($product->type === 'variant') {
            $product->update([
                'code' => $validated['code'],
                'dozen_per_case' => $validated['dozen_per_case'],
                'free_dozen_per_case' => $validated['free_dozen_per_case'],
                'mrp_per_unit' => $validated['mrp_per_unit'],
                'ptr_per_dozen' => $validated['ptr_per_dozen'],
                'ptd_per_dozen' => $validated['ptd_per_dozen'],
                'weight_gm' => $validated['weight_gm'],
                'attributes' => [
                    'fragrance' => $validated['fragrance'],
                    'size' => $validated['size'],
                ],
            ]);
        }
    });

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->variants()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    //Export Permissions
    public function export(Request $request)
    {       
        $search = $request->query('search');
        return Excel::download(new ProductsExport($search), 'products.xlsx');
    }



    //Create Variant
    public function createVariant(Product $product)
    {
         $title = 'Products';
        if ($product->type !== 'variable') {
            abort(403, 'Only variable products can have variants.');
        }

        $segments = Category::whereNull('parent_id')->get();
        return view('admin.products.add-variant', compact('product', 'segments','title'));
    }



    //Store variant
    public function storeVariant(Request $request, Product $product)
{
    if ($product->type !== 'variable') {
        abort(403, 'Only variable products can have variants.');
    }

    $validated = $request->validate([
        'variants' => 'required|array|min:1',
        'variants.*.code' => 'required|string|unique:products,code',
        'variants.*.fragrance' => 'nullable|string',
        'variants.*.size' => 'nullable|string',
        'variants.*.dozen_per_case' => 'nullable|numeric',
        'variants.*.mrp_per_unit' => 'nullable|numeric',
        'variants.*.ptr_per_dozen' => 'nullable|numeric',
        'variants.*.ptd_per_dozen' => 'nullable|numeric',
        'variants.*.weight_gm' => 'nullable|numeric',
    ]);

    foreach ($validated['variants'] as $variant) {
        Product::create([
            'parent_id' => $product->id,
            'type' => 'variant',
            'name' => $product->name,
            'code' => $variant['code'],
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'unit' => $product->unit,
            'dozen_per_case' => $variant['dozen_per_case'],
            'mrp_per_unit' => $variant['mrp_per_unit'],
            'ptr_per_dozen' => $variant['ptr_per_dozen'],
            'ptd_per_dozen' => $variant['ptd_per_dozen'],
            'weight_gm' => $variant['weight_gm'],
            'attributes' => [
                'fragrance' => $variant['fragrance'],
                'size' => $variant['size'],
            ],
        ]);
    }

    return redirect()->route('admin.products.index')->with('success', 'Variants added successfully D');
}


}
