<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\Distributor;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use LogicException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RetailersExport;

class AdminRetailerController extends Controller
{

    public function index(Request $request)
    {
        $title ='Retailer';
        $search = $request->query('search');
        $view = $request->query('view', 'active');

        $retailersQuery = Retailer::with(['district', 'distributor']);

        if ($view === 'trashed') {
            $retailersQuery->onlyTrashed();
        } elseif ($view === 'all') {
            $retailersQuery->withTrashed();
        }

        $retailers = $retailersQuery
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('retailer_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('town', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->appends(request()->query());

        return view('admin.retailers.index', [
            'retailers' => $retailers,
            'search'    => $search,
            'view'      => $view,
            'title'     => $title,
        ]);
    }

    public function create()
    {
        $title ='Add Retailer'; 

        return view('retailers.create', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'states'     => State::orderBy('name')->get(),
            'retailer'=> Retailer::orderBy('id')->get(),
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'returnURL' =>'admin.retailers.index',
            'title' => $title,
        ]);

    }


    public function show(Retailer $retailer)
    {
        $retailer->load(['distributor', 'state', 'district']);

        return view('admin.retailers.show', [
            'title' => 'Retailer Details',
            'retailer' => $retailer,
        ]);
    }

    public function edit(Retailer $retailer)
    {

        $title = 'Edit Retailer';

        return view('retailers.edit', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'states'     => State::orderBy('name')->get(),
            'retailer'=> $retailer,
            'distributors'=> Distributor::orderBy('firm_name')->get(),
            'districts' => District::where('state_id', $retailer->state_id)->get(),
            'returnURL' =>'admin.retailers.index',
            'title' => $title,
        ]);


    }

    public function destroy(Retailer $retailer)
    {
        $retailer->delete();

        return redirect()
            ->route('admin.retailers.index')
            ->with('success', 'Retailer deleted successfully.');
    }

    public function restore($id)
    {
        $retailer = Retailer::onlyTrashed()->findOrFail($id);
        $retailer->restore();

        return redirect()->back()->with('success', 'Retailer restored successfully.');
    }

    public function forceDelete($id)
    {
        $retailer = Retailer::onlyTrashed()->findOrFail($id);

        try {
            $retailer->forceDelete();
            return redirect()->back()->with('success', 'Retailer permanently deleted.');
        } catch (LogicException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $search = $request->query('search');
        $view = $request->query('view', 'active');

        return Excel::download(
            new RetailersExport($search, $view),
            'retailers.xlsx'
        );
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            
            'retailer_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',

            'state' => 'required|string',
            'district' => 'required|string',

            'pincode' => 'nullable|string|max:10',
            'landmark' => 'nullable|string|max:255',

            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email',

            'gst' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'date_of_anniversary' => 'nullable|date',
            'nature_of_outlet' => 'nullable|string|max:255',
            'appointment_date' => 'required|date',
            'distributor_id' => 'nullable|exists:distributors,id',
        ]);
    }   


}
