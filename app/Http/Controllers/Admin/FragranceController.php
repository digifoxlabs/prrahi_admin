<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Fragrance;
use Illuminate\Http\Request;

class FragranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title ='Fragnances';         
        $fragrances = Fragrance::all();
        return view('admin.fragrances.index', compact('title','fragrances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { 
         $title ='Fragnances';  
         return view('admin.fragrances.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Fragrance::create($request->only('name'));
        return redirect()->route('admin.fragrances.index')->with('success', 'Fragrance created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fragrance $fragrance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fragrance $fragrance)
    {
        $title ='Fragnances';     
        return view('admin.fragrances.edit', compact('fragrance','title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fragrance $fragrance)
    {
         $request->validate(['name' => 'required']);
        $fragrance->update($request->only('name'));
        return redirect()->route('admin.fragrances.index')->with('success', 'Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fragrance $fragrance)
    {
        $fragrance->delete();
        return redirect()->route('admin.fragrances.index')->with('success', 'Deleted!');
    }
}
