<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Distributor;
use App\Models\SalesPerson;
use App\Models\DistributorDocument;
use App\Models\DistributorCompany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistributorsExport;
use Illuminate\Support\Facades\DB;

class DistributorController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:view_distributors')->only(['index', 'show']);
        $this->middleware('permission:create_distributors')->only(['create', 'store']);
        $this->middleware('permission:edit_distributors')->only(['edit', 'update']);
        $this->middleware('permission:delete_distributors')->only(['destroy']);
    }


    /**
     * Display a listing of the resource.
     */
    // Display all distributors with search
    public function index(Request $request)
    {

        $title ='Distributors';  
   
        $search = $request->query('search');

        $distributors = Distributor::query()
            ->when($search, function ($query, $search) {
                $query->where('firm_name', 'like', '%' . $search . '%')
                ->orWhere('contact_person', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString(); // Preserve search on pagination

        return view('admin.distributors.index', compact('distributors', 'search','title'));


        


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Distributors';  
        // return view('admin.distributors.create', compact('title'));
         // return view('admin.distributors.create', ['distributor' => null]);

             // Fetch Sales Persons for dropdown
            $salesPersons = SalesPerson::select('id', 'name')->orderBy('name')->get();

            return view('admin.distributors.create', [
                'distributor' => null, // No distributor data for create form
                'salesPersons' => $salesPersons,
                'action' => route('admin.distributors.store'),
                'method' => 'POST',
                'title' => $title,
            ]);
    }

   
    public function store(Request $request)
    {
    $validated = $request->validate([
        'sales_persons_id' => 'nullable|exists:sales_persons,id',
        'appointment_date' => 'required|date',
        'firm_name' => 'required|string',
        'nature_of_firm' => 'required|string',
        'address_line_1' => 'nullable|string',
        'address_line_2' => 'nullable|string',
        'town' => 'nullable|string',
        'district' => 'nullable|string',
        'state' => 'nullable|string',
        'pincode' => 'nullable|string',
        'landmark' => 'nullable|string',
        'contact_person' => 'nullable|string',
        'designation_contact' => 'nullable|string',
        'contact_number' => 'nullable|string',
        'email' => 'nullable|email|unique:distributors,email',
        'gst' => 'nullable|string',
        'date_of_birth' => 'nullable|date',
        'date_of_anniversary' => 'nullable|date',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'login_id' => 'required|string|unique:distributors,login_id',
        'password' => 'required|string|min:6',
    ]);




        // Detect who is creating the distributor
        $appointedBy = null;

        if (auth('admin')->check()) {
            $appointedBy = auth('admin')->user();              // App\Models\User
        } elseif (auth('sales')->check()) {
            $appointedBy = auth('sales')->user();              // App\Models\SalesPerson
        } elseif (auth('distributor')->check()) {
            $appointedBy = auth('distributor')->user();        // App\Models\Distributor
        }




    DB::beginTransaction();

    try {
        $distributor = Distributor::create([
            'sales_persons_id' => $validated['sales_persons_id'],
            'appointment_date' => $validated['appointment_date'],
            'firm_name' => $validated['firm_name'],
            'nature_of_firm' => $validated['nature_of_firm'],
            'address_line_1' => $validated['address_line_1'],
            'address_line_2' => $validated['address_line_2'],
            'town' => $validated['town'],
            'district' => $validated['district'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'landmark' => $validated['landmark'],
            'contact_person' => $validated['contact_person'],
            'designation_contact' => $validated['designation_contact'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'gst' => $validated['gst'],
            'date_of_birth' => $validated['date_of_birth'],
            'date_of_anniversary' => $validated['date_of_anniversary'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'login_id' => $validated['login_id'],
            'password' => Hash::make($validated['password']),
        ]);


        /**
         * ---------------------------------------------------------
         * Attach appointed_by polymorphic relation
         * ---------------------------------------------------------
         */
        if ($appointedBy) {
            $distributor->appointedBy()->associate($appointedBy);
            $distributor->save();
        }



        foreach ($request->input('companies', []) as $company) {
            if (!empty($company['company_name'])) {
                $distributor->companies()->create([
                    'company_name' => $company['company_name'],
                    'segment' => $company['segment'] ?? null,
                    'brand_name' => $company['brand_name'] ?? null,
                    'products' => $company['products'] ?? null,
                    'working_as' => $company['working_as'] ?? null,
                    'margin' => $company['margin'] ?? null,
                    'payment_terms' => $company['payment_terms'] ?? null,
                    'working_since' => $company['working_since'] ?? null,
                    'area_operation' => $company['area_operation'] ?? null,
                    'monthly_to' => $company['monthly_to'] ?? null,
                    'dsr_no' => $company['dsr_no'] ?? null,
                    'details' => $company['details'] ?? null,
                ]);
            }
        }

        foreach ($request->input('banks', []) as $bank) {
            if (!empty($bank['bank_name'])) {
                $distributor->banks()->create($bank);
            }
        }

        foreach ($request->input('godowns', []) as $godown) {
            if (!empty($godown['no_godown'])) {
                $distributor->godowns()->create($godown);
            }
        }

        foreach ($request->input('manpowers', []) as $manpower) {
            $distributor->manpowers()->create($manpower);
        }

        foreach ($request->input('vehicles', []) as $vehicle) {
            $distributor->vehicles()->create($vehicle);
        }

        DB::commit();

        return redirect()->route('admin.distributors.index')->with('success', 'Distributor created successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}


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

    return view('admin.distributors.show', compact('distributor','title'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributor $distributor)
    {
        $title ='Distributors';  

        $salesPersons = SalesPerson::all();
        $distributor->load(['companies', 'banks', 'godowns', 'manpowers', 'vehicles']);

    return view('admin.distributors.edit', compact('distributor', 'salesPersons','title'));


    }

    

public function update(Request $request, Distributor $distributor)
{
    $validated = $request->validate([
        'sales_persons_id' => 'nullable|exists:sales_persons,id',
        'appointment_date' => 'required|date',
        'firm_name' => 'required|string',
        'nature_of_firm' => 'required|string',
        'address_line_1' => 'nullable|string',
        'address_line_2' => 'nullable|string',
        'town' => 'nullable|string',
        'district' => 'nullable|string',
        'state' => 'nullable|string',
        'pincode' => 'nullable|string',
        'landmark' => 'nullable|string',
        'contact_person' => 'nullable|string',
        'designation_contact' => 'nullable|string',
        'contact_number' => 'nullable|string',
        'email' => 'nullable|email',
        'gst' => 'nullable|string',
        'date_of_birth' => 'nullable|date',
        'date_of_anniversary' => 'nullable|date',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'login_id' => 'required|string',
        'password' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $distributor->update([
            'sales_persons_id' => $validated['sales_persons_id'],
            'appointment_date' => $validated['appointment_date'],
            'firm_name' => $validated['firm_name'],
            'nature_of_firm' => $validated['nature_of_firm'],
            'address_line_1' => $validated['address_line_1'],
            'address_line_2' => $validated['address_line_2'],
            'town' => $validated['town'],
            'district' => $validated['district'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'landmark' => $validated['landmark'],
            'contact_person' => $validated['contact_person'],
            'designation_contact' => $validated['designation_contact'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'gst' => $validated['gst'],
            'date_of_birth' => $validated['date_of_birth'],
            'date_of_anniversary' => $validated['date_of_anniversary'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'login_id' => $validated['login_id'],
        ]);

        if (!empty($validated['password'])) {
            $distributor->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        // Companies
        $distributor->companies()->delete();
        foreach ($request->input('companies', []) as $company) {
            if (!empty($company['company_name'])) {
                $distributor->companies()->create([
                    'company_name' => $company['company_name'],
                    'segment' => $company['segment'] ?? null,
                    'brand_name' => $company['brand_name'] ?? null,
                    'products' => $company['products'] ?? null,
                    'working_as' => $company['working_as'] ?? null,
                    'margin' => $company['margin'] ?? null,
                    'payment_terms' => $company['payment_terms'] ?? null,
                    'working_since' => $company['working_since'] ?? null,
                    'area_operation' => $company['area_operation'] ?? null,
                    'monthly_to' => $company['monthly_to'] ?? null,
                    'dsr_no' => $company['dsr_no'] ?? null,
                    'details' => $company['details'] ?? null,
                ]);
            }
        }

        // Banks
        $distributor->banks()->delete();
        foreach ($request->input('banks', []) as $bank) {
            if (!empty($bank['bank_name'])) {
                $distributor->banks()->create($bank);
            }
        }

        // Godowns
        $distributor->godowns()->delete();
        foreach ($request->input('godowns', []) as $godown) {
            if (!empty($godown['no_godown'])) {
                $distributor->godowns()->create($godown);
            }
        }

        // Manpower
        $distributor->manpowers()->delete();
        foreach ($request->input('manpowers', []) as $manpower) {
            $distributor->manpowers()->create($manpower);
        }

        // Vehicles
        $distributor->vehicles()->delete();
        foreach ($request->input('vehicles', []) as $vehicle) {
            $distributor->vehicles()->create($vehicle);
        }

        DB::commit();

        return redirect()->route('admin.distributors.index')->with('success', 'Distributor updated successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}



    /**
     * Remove the specified resource from storage.
     */
    // Delete distributor
    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);

        // if ($distributor->profile_photo && Storage::disk('public')->exists($distributor->profile_photo)) {
        //     Storage::disk('public')->delete($distributor->profile_photo);
        // }

        $distributor->delete();
        return redirect()->route('admin.distributors.index')->with('success', 'Distributor deleted.');
    }


    //Export Distributors 
    public function export(Request $request)
    {       
        $search = $request->query('search');
        return Excel::download(new DistributorsExport($search), 'distributors.xlsx');
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // max 2MB
        ]);

        $distributor = Distributor::findOrFail($id);

        // Delete old image if exists
        if ($distributor->profile_photo && Storage::disk('public')->exists($distributor->profile_photo)) {
            Storage::disk('public')->delete($distributor->profile_photo);
        }

        // Store new image
        $filename = 'distributors/profile_' . Str::random(10) . '.' . $request->file('profile_photo')->getClientOriginalExtension();
        $path = $request->file('profile_photo')->storeAs('distributors', $filename, 'public');

        // Update profile path in DB
        $distributor->profile_photo = $path;
        $distributor->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile image updated successfully.',
            'path' => asset('storage/' . $path),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'distributor_id' => 'required|exists:distributors,id',
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);
        $distributor->password = Hash::make($request->password);
        $distributor->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }



public function uploadProfile(Request $request, Distributor $distributor)
{
    $request->validate([
        'image' => 'required|image|max:2048', // max 2MB image
    ]);

    // Delete old image if exists
    if ($distributor->profile_photo) {
        Storage::disk('public')->delete($distributor->profile_photo);
    }

    // Store new image
    $path = $request->file('image')->store('distributor', 'public');
    $distributor->profile_photo = $path;
    $distributor->save();

    return response()->json([
        'success' => true,
        'image_url' => asset('storage/' . $path),
    ]);
}



}
