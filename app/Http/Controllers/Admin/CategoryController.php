<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title ='Catgeories';          
        $categories = Category::with('parent')->get();
        return view('admin.categories.index', compact('title','categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Catgeories'; 
        $segments = Category::whereNull('parent_id')->get();
         return view('admin.categories.create', compact('title','segments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Category::create($request->only('name', 'parent_id'));
        return redirect()->route('admin.categories.index')->with('success', 'Category created!');
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
         $title ='Catgeories';     
        $segments = Category::whereNull('parent_id')->get();
        return view('admin.categories.edit', compact('category', 'segments','title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required']);
        $category->update($request->only('name', 'parent_id'));
        return redirect()->route('admin.categories.index')->with('success', 'Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Deleted!');
    }
}
