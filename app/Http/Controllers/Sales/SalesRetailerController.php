<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Distributor,
    Retailer,
    SalesPerson,
    State,
    District,
};

class SalesRetailerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title  = 'Retailers';
        $search = $request->query('search');

        $appointed_by = auth('sales')->id();

        $retailers = Retailer::with(['district', 'distributor'])
            ->where(function ($q) use ($appointed_by) {
          
                    $q->where('appointed_by_type', SalesPerson::class)
                        ->where('appointed_by_id', $appointed_by);              

            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('retailer_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('town', 'like', "%{$search}%");
                });
            })
            ->orderBy('retailer_name')
            ->paginate(15)
            ->withQueryString();

        return view('sales.retailers.index', compact(
            'retailers',
            'search',
            'title'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        $title ='Add Retailer'; 

        return view('retailers.create', [
            'layout'      => 'sales.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'sales',               // or distributor / sales
            'states'     => State::orderBy('name')->get(),
            'retailer'=> Retailer::orderBy('id')->get(),
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'returnURL' =>'sales.retailers.index',
            'title' => $title,
        ]);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Shared RetailerController
    }

    /**
     * Display the specified resource.
     */
    public function show(Retailer $retailer)
    {

        $this->authorizeSalesRetailerAccess($retailer);

        $retailer->load(['distributor', 'state', 'district']);

        return view('sales.retailers.show', [
            'title' => 'Retailer Details',
            'retailer' => $retailer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Retailer $retailer)
    {

        $this->authorizeSalesRetailerAccess($retailer);

        $title ='Update Retailer'; 

        return view('retailers.edit', [
            'layout'      => 'sales.layout', // or distributor.layout / sales.layout
            'routePrefix' => 'sales',               // or distributor / sales
            'states'     => State::orderBy('name')->get(),
            'retailer'=> $retailer,
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'districts' => District::where('state_id', $retailer->state_id)->get(),
            'returnURL' =>'sales.retailers.index',
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
    public function destroy(Retailer $retailer)
    {

        $this->authorizeSalesRetailerAccess($retailer);

        $retailer->delete();

        return redirect()
            ->route('sales.retailers.index')
            ->with('success', 'Retailer deleted successfully.');
    }



    private function authorizeSalesRetailerAccess(Retailer $retailer): void
    {
        $salesPerson = auth('sales')->user();

        // If not accessed via sales guard, skip check (admin, etc.)
        if (!$salesPerson) {
            return;
        }

        if (
            $retailer->appointed_by_type !== SalesPerson::class ||
            $retailer->appointed_by_id !== $salesPerson->id
        ) {
            abort(403, 'Unauthorized access to retailer');
        }
    }



}
