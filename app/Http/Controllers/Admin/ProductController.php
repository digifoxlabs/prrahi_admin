<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\DB;

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
    public function indexX(Request $request)
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







// public function index(Request $request)
// {
//     $title = 'Products';
//     $search = $request->query('search');

//     // Fetch only top-level products (parent_id IS NULL) so variables (with variants) and simple products appear.
//     // Eager load variants and categories
//     $products = Product::with(['category', 'subCategory', 'variants', 'variants.category', 'variants.subCategory'])
//         ->whereNull('parent_id')
//         ->when($search, function ($query, $search) {
//             $query->where(function ($q) use ($search) {
//                 // Match on parent (top-level) name/code OR any variant attributes (via variants relation)
//                 $q->where('name', 'like', "%{$search}%")
//                   ->orWhere('code', 'like', "%{$search}%")
//                   ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
//                   ->orWhereHas('subCategory', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
//                   ->orWhereHas('variants', function ($q3) use ($search) {
//                       $q3->where('name', 'like', "%{$search}%")
//                          ->orWhere('code', 'like', "%{$search}%")
//                          ->orWhereHas('category', fn($q4) => $q4->where('name', 'like', "%{$search}%"))
//                          ->orWhereHas('subCategory', fn($q4) => $q4->where('name', 'like', "%{$search}%"));
//                   });
//             });
//         })
//         ->orderByDesc('id')
//         ->paginate(20)
//         ->withQueryString();

//     return view('admin.products.index', compact('title', 'products', 'search'));
// }



    // Index: return only top-level products (parent_id IS NULL)
    public function index(Request $request)
    {
        $title = 'Products';
        $search = $request->query('search');

        $productsQuery = Product::with([
                'category',
                'subCategory',
                'variants' => function ($q) {
                    $q->orderBy('id', 'asc'); // order variants if you like
                },
                'variants.category',
                'variants.subCategory'
            ])
            ->whereNull('parent_id');

        if ($search) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subCategory', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('variants', function ($q3) use ($search) {
                      $q3->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhereHas('category', fn($q4) => $q4->where('name', 'like', "%{$search}%"))
                         ->orWhereHas('subCategory', fn($q4) => $q4->where('name', 'like', "%{$search}%"));
                  });
            });
        }

        $products = $productsQuery->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.products.index', compact('title', 'products', 'search'));
    }



    public function variants(Product $product)
{

     $title = 'Products';
    // ensure product is a variable type
    if ($product->type !== 'variable') {
        return redirect()->route('admin.products.index')->with('error', 'Product is not a variable product.');
    }

    // eager load variants (and category/subCategory if you want)
    $product->load(['variants', 'category', 'subCategory']);

    // If you used the aggregate-subquery approach in index(), ensure variants have total_stock:
    // Optionally append total_stock per variant if not provided by query:
    // $product->variants->each->append('total_stock');

    return view('admin.products.variants', compact('product','title'));
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
            'base_unit' => 'required',
            'variants' => 'required_if:type,variable|array|min:1',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'code'=> $request->code,
            'hsn'=> $request->hsn,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'base_unit' => $request->base_unit,
            'base_quantity' => $request->base_quantity,
            'mrp_per_unit' => $request->type == 'simple' ? $request->mrp_per_unit : null,
            'ptr_per_dozen' => $request->type == 'simple' ? $request->ptr_per_dozen : null,
            'retailer_discount_percent' => $request->type == 'simple' ? $request->retailer_discount_percent : null,
            'ptd_per_dozen' => $request->type == 'simple' ? $request->ptd_per_dozen : null,
            'distributor_discount_percent' => $request->type == 'simple' ? $request->distributor_discount_percent : null,
            'weight_gm' => $request->type == 'simple' ? $request->weight_gm : null,
            'size' => $request->type == 'simple' ? $request->size : null,
            'attributes' => $request->type == 'simple' ? null : [],
        ]);

        if ($request->type == 'variable' && $request->variants) {
            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'name' => null,
                    'code' => $variant['code'],
                    'hsn' => $variant['hsn'],
                    'type' => 'variant',
                    'parent_id' => $product->id,
                    'category_id' => null,
                    'sub_category_id' => null,
                    'base_unit' => null,
                    'base_quantity' => null,                    
                    // 'dozen_per_case' => $variant['dozen_per_case'],
                    // 'free_dozen_per_case' => $variant['free_dozen_per_case'],
                    'mrp_per_unit' => $variant['mrp_per_unit'],
                    'ptr_per_dozen' => $variant['ptr_per_dozen'],
                    'retailer_discount_percent' => $variant['retailer_discount_percent'],
                    'ptd_per_dozen' => $variant['ptd_per_dozen'],
                    'distributor_discount_percent' => $variant['distributor_discount_percent'],
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
        'hsn'=> 'nullable',
        'category_id' => 'nullable|exists:categories,id',
        'sub_category_id' => 'nullable|exists:categories,id',
        'base_unit' => 'nullable|string|max:20',
        'base_quantity' => 'nullable|string|max:20',
        'type' => 'required|in:simple,variable,variant',

        // For simple product
        // 'dozen_per_case' => 'nullable|numeric',
        // 'free_dozen_per_case' => 'nullable|numeric',
        'mrp_per_unit' => 'nullable|numeric',
        'ptr_per_dozen' => 'nullable|numeric',
        'retailer_discount_percent' => 'nullable|numeric',
        'ptd_per_dozen' => 'nullable|numeric',
        'distributor_discount_percent' => 'nullable|numeric',
        'weight_gm' => 'nullable|numeric',
        'size' => 'nullable|string|max:255',

        // For variant product
        'fragrance' => 'nullable|string|max:255',

        // For multiple variants (variable product)
        'variants.*.code' => 'nullable',
        'variants.*.hsn' => 'nullable',
        'variants' => 'nullable|array',
        'variants.*.id' => 'nullable|exists:products,id',
        'variants.*.fragrance' => 'nullable|string|max:255',
        'variants.*.size' => 'nullable|string|max:255',
        'variants.*.base_quantity' => 'nullable|numeric',
        'variants.*.mrp_per_unit' => 'nullable|numeric',
        'variants.*.ptr_per_dozen' => 'nullable|numeric',
        'variants.*.retailer_discount_percent' => 'nullable|numeric',
        'variants.*.ptd_per_dozen' => 'nullable|numeric',
        'variants.*.distributor_discount_percent' => 'nullable|numeric',
        'variants.*.weight_gm' => 'nullable|numeric',
    ]);

    DB::transaction(function () use ($validated, $request, $product) {


        if ($product->type === 'simple') {
            $product->update([
                'name' => $validated['name'],
                'code'=> $validated['code'],
                'hsn'=> $validated['hsn'],
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'],
                'base_unit' => $validated['base_unit'],
                'base_quantity' => $validated['base_quantity'],
                // 'free_dozen_per_case' => $validated['free_dozen_per_case'],
                'mrp_per_unit' => $validated['mrp_per_unit'],
                'ptr_per_dozen' => $validated['ptr_per_dozen'],
                'retailer_discount_percent' => $validated['retailer_discount_percent'],
                'ptd_per_dozen' => $validated['ptd_per_dozen'],
                'distributor_discount_percent' => $validated['distributor_discount_percent'],
                'weight_gm' => $validated['weight_gm'],
                'size' => $request->type == 'simple' ? $request->size : null,
                'attributes' => $request->type == 'simple' ? null : [],
            ]);
        }

        else if ($product->type === 'variable') {


            $product->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'],
                'base_unit' => $validated['base_unit'],
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
                                'hsn' =>  $variantData['hsn'],                        
                                'base_unit' => null,
                                'base_quantity' => null,
                                'mrp_per_unit' => $variantData['mrp_per_unit'],
                                'ptr_per_dozen' => $variantData['ptr_per_dozen'],
                                'retailer_discount_percent' => $variantData['retailer_discount_percent'],
                                'ptd_per_dozen' => $variantData['ptd_per_dozen'],
                                'distributor_discount_percent' => $variantData['distributor_discount_percent'],
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

        else if ($product->type === 'variant') {  
    
            // Update only the variant record itself (not the parent)
            $product->update([
                // Code (top-level 'code' in form)
                'code' => $validated['code'] ?? $product->code,
                'hsn' => $validated['hsn'] ?? $product->hsn,

                // Pricing / specs
                'mrp_per_unit' => $validated['mrp_per_unit'] ?? $product->mrp_per_unit,
                'ptr_per_dozen' => $validated['ptr_per_dozen'] ?? $product->ptr_per_dozen,
                'retailer_discount_percent' => $validated['retailer_discount_percent'] ?? $product->retailer_discount_percent,
                'ptd_per_dozen' => $validated['ptd_per_dozen'] ?? $product->ptd_per_dozen,
                'distributor_discount_percent' => $validated['distributor_discount_percent'] ?? $product->distributor_discount_percent,
                'weight_gm' => $validated['weight_gm'] ?? $product->weight_gm,

                // Keep base_unit/base_quantity null for variants (they inherit from parent)
                'base_unit' => $product->base_unit,
                'base_quantity' => $product->base_quantity,

                // Attributes (fragrance, size) — store as JSON column
                'attributes' => [
                    'fragrance' => $validated['fragrance'] ?? ($product->attributes['fragrance'] ?? null),
                    'size' => $validated['size'] ?? ($product->attributes['size'] ?? null),
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
    public function storeVariant(Request $request, Product $product){


    if ($product->type !== 'variable') {
        abort(403, 'Only variable products can have variants.');
    }

    $validated = $request->validate([
        'variants' => 'required|array|min:1',
        'variants.*.code' => 'required|string|unique:products,code',
        'variants.*.fragrance' => 'nullable|string',
        'variants.*.size' => 'nullable|string',
        'variants.*.mrp_per_unit' => 'nullable|numeric',
        'variants.*.ptr_per_dozen' => 'nullable|numeric',
        'variants.*.retailer_discount_percent' => 'nullable|numeric',
        'variants.*.ptd_per_dozen' => 'nullable|numeric',
        'variants.*.distributor_discount_percent' => 'nullable|numeric',
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
            'base_unit' => null,
            'base_quantity' => null,
            'mrp_per_unit' => $variant['mrp_per_unit'],
            'ptr_per_dozen' => $variant['ptr_per_dozen'],
            'retailer_discount_percent' => $variant['retailer_discount_percent'],
            'ptd_per_dozen' => $variant['ptd_per_dozen'],
            'distributor_discount_percent' => $variant['distributor_discount_percent'],
            'weight_gm' => $variant['weight_gm'],
            'attributes' => [
                'fragrance' => $variant['fragrance'],
                'size' => $variant['size'],
            ],
        ]);
    }

        // After transaction
        $redirect = $request->input('redirect_to', route('admin.products.index'));

        return redirect($redirect)
                ->with('success', 'Variants saved successfully.');

    //return redirect()->route('admin.products.index')->with('success', 'Variants Added Successfully');
}


}
