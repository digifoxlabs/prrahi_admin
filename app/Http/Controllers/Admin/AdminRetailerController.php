<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\Distributor;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Facades\DB;

class AdminRetailerController extends Controller
{
    public function index(Request $request)
    {
        $title ='Retailer';
        $search = $request->query('search');

        $retailers = Retailer::with(['district', 'distributor'])
            ->when($search, function ($q) use ($search) {
                $q->where('retailer_name', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
                ->orWhere('contact_number', 'like', "%{$search}%")
                ->orWhere('town', 'like', "%{$search}%");
            })
            ->orderBy('retailer_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.retailers.index', [
            'retailers' => $retailers,
            'search'    => $search,
            'title'     => $title,
        ]);
    }

    public function create()
    {
        $title ='Add Retailer'; 
        $states = State::orderBy('name')->get();
        $distributors = Distributor::orderBy('firm_name')->get();
        return view('admin.retailers.create',compact('title','states','distributors'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        // Detect who is creating the retailer
        if (auth('admin')->check()) {
            $data['appointed_by_type'] = \App\Models\User::class;
            $data['appointed_by_id']   = auth('admin')->id();
        } elseif (auth('sales')->check()) {
            $data['appointed_by_type'] = \App\Models\SalesPerson::class;
            $data['appointed_by_id']   = auth('sales')->id();
        } elseif (auth('distributor')->check()) {
            $data['appointed_by_type'] = \App\Models\Distributor::class;
            $data['appointed_by_id']   = auth('distributor')->id();
            $data['distributor_id']    = auth('distributor')->id(); // auto-link
        }

        Retailer::create($data);

        return redirect()
            ->route('admin.retailers.index')
            ->with('success', 'Retailer created successfully.');
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
        return view('admin.retailers.edit', [
            'title' => 'Edit Retailer',
            'retailer' => $retailer,
            'distributors' => Distributor::orderBy('firm_name')->get(),
            'states' => State::all(),
            'districts' => District::where('state_id', $retailer->state_id)->get(),
        ]);
    }

    public function update(Request $request, Retailer $retailer)
    {
        $data = $this->validatedData($request);

        $retailer->update($data);

        return redirect()
            ->route('admin.retailers.index')
            ->with('success', 'Retailer updated successfully.');
    }

    public function destroy(Retailer $retailer)
    {
        $retailer->delete();

        return redirect()
            ->route('admin.retailers.index')
            ->with('success', 'Retailer deleted successfully.');
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