<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use LogicException;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_categories')->only(['index', 'show']);
        $this->middleware('permission:create_categories')->only(['create', 'store']);
        $this->middleware('permission:edit_categories')->only(['edit', 'update']);
        $this->middleware('permission:delete_categories')->only(['destroy', 'restore', 'forceDelete']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Categories';
        $search = trim((string) $request->query('search', ''));
        $view = $request->query('view', 'active');

        $categoriesQuery = Category::query()
            ->with('parent:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('parent', function ($parentQuery) use ($search) {
                            $parentQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });

        if ($view === 'trashed') {
            $categoriesQuery->onlyTrashed();
        } elseif ($view === 'all') {
            $categoriesQuery->withTrashed();
        }

        $categories = $categoriesQuery
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', compact('title', 'categories', 'search', 'view'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Categories';
        $segments = Category::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.create', compact('title', 'segments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $validated['parent_id'] = $validated['parent_id'] ?? null;

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $title = 'Categories';
        $segments = Category::query()
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.edit', compact('category', 'segments', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId !== null && (int) $parentId === (int) $category->id) {
            return back()
                ->withErrors(['parent_id' => 'A category cannot be its own parent.'])
                ->withInput();
        }

        if ($parentId !== null) {
            $cursor = Category::query()->find($parentId);
            while ($cursor) {
                if ((int) $cursor->id === (int) $category->id) {
                    return back()
                        ->withErrors(['parent_id' => 'Invalid parent selected.'])
                        ->withInput();
                }
                $cursor = $cursor->parent;
            }
        }

        $category->update([
            'name' => $validated['name'],
            'parent_id' => $parentId,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $hasChildren = Category::query()->where('parent_id', $category->id)->exists();
        if ($hasChildren) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Category has sub-categories. Move or delete them first.');
        }

        $usedInProducts = Product::query()
            ->where('category_id', $category->id)
            ->orWhere('sub_category_id', $category->id)
            ->exists();
        if ($usedInProducts) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Category is used in products and cannot be deleted.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->back()->with('success', 'Category restored successfully.');
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        try {
            $hasChildren = Category::withTrashed()
                ->where('parent_id', $category->id)
                ->exists();
            if ($hasChildren) {
                return redirect()
                    ->back()
                    ->with('error', 'Category has sub-categories. Move or delete them first.');
            }

            $usedInProducts = Product::withTrashed()
                ->where('category_id', $category->id)
                ->orWhere('sub_category_id', $category->id)
                ->exists();
            if ($usedInProducts) {
                return redirect()
                    ->back()
                    ->with('error', 'Category is used in products and cannot be permanently deleted.');
            }

            $category->forceDelete();

            return redirect()->back()->with('success', 'Category permanently deleted.');
        } catch (LogicException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
