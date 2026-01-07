<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesPerson;
use App\Models\Distributor;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesPersonExport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class SalesPersonController extends Controller
{


  public function __construct()
    {
        $this->middleware('permission:view_sales')->only(['index', 'show']);
        $this->middleware('permission:create_sales')->only(['create', 'store']);
        $this->middleware('permission:edit_sales')->only(['edit', 'update']);
        $this->middleware('permission:delete_sales')->only(['destroy']);
    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $title ='Sales-Persons'; 
       $search = $request->query('search');

        $salesPersons = SalesPerson::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('headquarter', 'like', "%$search%")
                ->orWhere('town', 'like', "%$search%")
                ->orWhere('town_covered', 'like', "%$search%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString(); // Preserve search on pagination

        return view('admin.sales_persons.index', compact('salesPersons','title','search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $title ='Sales-Persons'; 
             $states = State::orderBy('name')->get();

         return view('admin.sales_persons.create',compact('title','states'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'login_id' => 'required|string|unique:sales_persons,login_id',
        'password' => 'required|string',
        'state' => 'required|string',
        'district' => 'required|string',
        'designation' => 'nullable|string',
        'headquarter' => 'nullable|string',
        'address_line_1' => 'nullable|string',
        'address_line_2' => 'nullable|string',
        'town' => 'nullable|string',
        'pincode' => 'nullable|string',
        'phone' => 'nullable|string',
        'official_email' => 'nullable|email',
        'personal_email' => 'nullable|email',
        'date_of_birth' => 'nullable|date',
        'date_of_anniversary' => 'nullable|date',
        'zone' => 'nullable|string',
        'state_covered' => 'nullable|array',
        'district_covered' => 'nullable|array',
        'town_covered' => 'nullable|array',
    ]);

    $salesPerson = new SalesPerson();
    $salesPerson->fill($request->except(['password', 'state_covered', 'district_covered','town_covered']));

    $salesPerson->password = bcrypt($request->password ?? 'password@123');

    $salesPerson->state_covered = $request->filled('state_covered')
        ? implode(',', $request->state_covered) : null;
    $salesPerson->district_covered = $request->filled('district_covered')
        ? implode(',', $request->district_covered) : null; 
     $salesPerson->town_covered = $request->filled('town_covered')
        ? implode(',', $request->town_covered) : null;

    $salesPerson->save();

    return redirect()->route('admin.sales-persons.index')->with('success', 'Sales Person created successfully.');
}

    /**
     * Display the specified resource.
     */
  public function show(SalesPerson $salesPerson)
    {
         $title ='Sales-Persons'; 
        return view('admin.sales_persons.show', compact('salesPerson','title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesPerson $salesPerson)
    {
         $title ='Sales-Persons'; 
          $states = State::orderBy('name')->get();
          return view('admin.sales_persons.edit', compact('salesPerson','title','states'));
    }

    /**
     * Update the specified resource in storage.
     */
 public function update(Request $request, SalesPerson $salesPerson)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'login_id' => 'required|string|unique:sales_persons,login_id,' . $salesPerson->id,
        'password' => 'nullable|string',
        'state' => 'required|string',
        'district' => 'required|string',
        'designation' => 'nullable|string',
        'headquarter' => 'nullable|string',
        'address_line_1' => 'nullable|string',
        'address_line_2' => 'nullable|string',
        'town' => 'nullable|string',
        'pincode' => 'nullable|string',
        'phone' => 'nullable|string',
        'official_email' => 'nullable|email',
        'personal_email' => 'nullable|email',
        'date_of_birth' => 'nullable|date',
        'date_of_anniversary' => 'nullable|date',
        'zone' => 'nullable|string',
        'state_covered' => 'nullable|array',
        'district_covered' => 'nullable|array',
         'town_covered' => 'nullable|array',
    ]);

    $salesPerson->fill($request->except(['password', 'state_covered', 'district_covered','town_covered']));

    if ($request->filled('password')) {
        $salesPerson->password = bcrypt($request->password);
    }

    $salesPerson->state_covered = $request->filled('state_covered')
        ? implode(',', $request->state_covered) : null;

    $salesPerson->district_covered = $request->filled('district_covered')
        ? implode(',', $request->district_covered) : null;

    $salesPerson->town_covered = $request->filled('town_covered')
        ? implode(',', $request->town_covered) : null;

    $salesPerson->save();

    return redirect()->route('admin.sales-persons.index')->with('success', 'Sales Person updated successfully.');
    
}

    /**
     * Remove the specified resource from storage.
     */
 public function destroy(SalesPerson $salesPerson)
{
    $salesPerson->delete();

    return redirect()->route('admin.sales-persons.index')->with('success', 'Sales Person deleted successfully.');
}

    // AJAX District Fetcher
    public function getDistricts(Request $request)
    {
        $state = State::where('name', $request->state)->first();
        $districts = $state ? $state->districts()->orderBy('name')->pluck('name') : [];
        return response()->json($districts);
    }




public function uploadProfile(Request $request, SalesPerson $salesPerson)
{
    $request->validate([
        'image' => 'required|image|max:2048', // max 2MB image
    ]);

    // Delete old image if exists
    if ($salesPerson->profile_photo) {
        Storage::disk('public')->delete($salesPerson->profile_photo);
    }

    // Store new image
    $path = $request->file('image')->store('sales-persons', 'public');
    $salesPerson->profile_photo = $path;
    $salesPerson->save();

    return response()->json([
        'success' => true,
        'image_url' => asset('storage/' . $path),
    ]);
}

// public function updatePassword(Request $request, SalesPerson $salesPerson)
// {
//     $request->validate([
//         'password' => 'required|string|min:6'
//     ]);

//     $salesPerson->password = Hash::make($request->password);
//     $salesPerson->save();

//     return response()->json(['success' => true]);
// }


public function updatePassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'password' => 'required|string|min:8|confirmed',
        'sales_person_id' => 'required|exists:sales_persons,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    $salesPerson = SalesPerson::findOrFail($request->sales_person_id);
    $salesPerson->password = Hash::make($request->password);
    $salesPerson->save();

    return response()->json([
        'success' => true,
        'message' => 'Password updated successfully.',
    ]);
}





    //Export Sales persons 
    public function export(Request $request)
    {       
        $search = $request->query('search');
        return Excel::download(new SalesPersonExport($search), 'sales-person.xlsx');
    }



//Map Distributor to Sales Person
public function mapDistributors(Request $request, SalesPerson $salesPerson)
{
    $request->validate([
        'distributor_ids' => 'array',
        'distributor_ids.*' => 'integer|exists:distributors,id',
    ]);

    $ids = $request->input('distributor_ids', []);

    // 1) Unassign distributors that were previously assigned to this sales person but not in $ids
    \App\Models\Distributor::where('sales_persons_id', $salesPerson->id)
        ->whereNotIn('id', $ids)
        ->update(['sales_persons_id' => null]);

    // 2) Assign selected distributors to this sales person (skip those already assigned to someone else)
    \App\Models\Distributor::whereIn('id', $ids)
        ->where(function($q) use ($salesPerson) {
            $q->whereNull('sales_persons_id')
              ->orWhere('sales_persons_id', $salesPerson->id);
        })
        ->update(['sales_persons_id' => $salesPerson->id]);

   return redirect()->back()->with('success', 'Distributors mapping updated.');




}





public function unmapDistributor(Request $request, SalesPerson $salesPerson)
{
    $request->validate([
        'distributor_id' => 'required|integer|exists:distributors,id',
    ]);

    $distId = $request->input('distributor_id');

    $distributor = Distributor::find($distId);

    if (! $distributor) {
        return redirect()->back()->with('error', 'Distributor not found.');
    }

    // ensure this distributor is currently mapped to this sales person
    if ($distributor->sales_persons_id !== $salesPerson->id) {
        return redirect()->back()->with('error', 'This distributor is not assigned to the selected sales person.');
    }

    // Unassign
    $distributor->sales_persons_id = null;
    $distributor->save();

   return redirect()->back()->with('success', 'Distributor successfully removed from the sales person.');





}





















}
