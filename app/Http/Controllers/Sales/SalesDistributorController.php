<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\SalesPerson;

class SalesDistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Distributors';

        $sales_id = auth('sales')->id();
        $search   = $request->query('search');

        $distributors = Distributor::query()
            ->where('sales_persons_id', $sales_id) // ✅ ONLY THIS LINE ADDED
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firm_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();

        return view('sales.distributors.index', compact('distributors', 'search', 'title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Create Distributor';  

        // Fetch Sales Persons for dropdown
        $salesPersons = SalesPerson::select('id', 'name')->orderBy('name')->get();

        return view('distributor.manage.create', [
            'layout'      => 'sales.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'sales',               // or distributor / sales
            'salesPersons' => $salesPersons,
            'distributor' => null, // No distributor data for create form
            'returnURL' =>'admin.distributors.index',
            'title' => $title,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Distributor $distributor)
    {

        $title ='Distributors';  
        // Eager load relationships to avoid N+1 queries
        $distributor->load([
            'salesPerson',
            'companies',
            'banks',
            'godowns',
            'manpowers',
            'vehicles'
        ]);

        return view('sales.distributors.show', compact('distributor','title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributor $distributor)
    {
        $title ='Edit Distributor';  

        // Fetch Sales Persons for dropdown
        $salesPersons = SalesPerson::select('id', 'name')->orderBy('name')->get();

        $distributor = $distributor->load(['companies', 'banks', 'godowns', 'manpowers', 'vehicles']);

        return view('distributor.manage.edit', [
            'layout'      => 'sales.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'sales',               // or distributor / sales
            'salesPersons' => $salesPersons,
            'distributor' => $distributor,
            'returnURL' =>'sales.distributors.index',
            'title' => $title,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
