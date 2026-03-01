<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\SalesPerson;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistributorsExport;
use LogicException;

class DistributorController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view_distributors')->only(['index', 'show']);
        $this->middleware('permission:create_distributors')->only(['create', 'store']);
        $this->middleware('permission:edit_distributors')->only(['edit', 'update']);
        $this->middleware('permission:delete_distributors')->only(['destroy', 'restore', 'forceDelete']);
    }


    /**
     * Display a listing of the resource.
     */
    // Display all distributors with search
    public function index(Request $request)
    {

        $title = 'Distributors';

        $search = $request->query('search');
        $view = $request->query('view', 'active');

        $distributorsQuery = Distributor::query();

        if ($view === 'trashed') {
            $distributorsQuery->onlyTrashed();
        } elseif ($view === 'all') {
            $distributorsQuery->withTrashed();
        }

        $distributors = $distributorsQuery
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('firm_name', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString(); // Preserve search on pagination

        return view('admin.distributors.index', compact('distributors', 'search', 'title', 'view'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $title = 'Create Distributor';

        // Fetch Sales Persons for dropdown
        $salesPersons = SalesPerson::select('id', 'name')->orderBy('name')->get();

        return view('distributor.manage.create', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'salesPersons' => $salesPersons,
            'distributor' => null, // No distributor data for create form
            'returnURL' => 'admin.distributors.index',
            'title' => $title,
        ]);
    }


    public function show(Distributor $distributor)
    {

        $title = 'Distributors';
        // Eager load relationships to avoid N+1 queries
        $distributor->load([
            'salesPerson',
            'companies',
            'banks',
            'godowns',
            'manpowers',
            'vehicles'
        ]);

        return view('admin.distributors.show', compact('distributor', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributor $distributor)
    {

        $title = 'Edit Distributor';

        // Fetch Sales Persons for dropdown
        $salesPersons = SalesPerson::select('id', 'name')->orderBy('name')->get();

        $distributor = $distributor->load(['companies', 'banks', 'godowns', 'manpowers', 'vehicles']);

        return view('distributor.manage.edit', [
            'layout'      => 'admin.admin-layout', // or distributor.layout / sales.layout
            'routePrefix' => 'admin',               // or distributor / sales
            'salesPersons' => $salesPersons,
            'distributor' => $distributor,
            'returnURL' => 'admin.distributors.index',
            'title' => $title,
        ]);
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

    public function restore($id)
    {
        $distributor = Distributor::onlyTrashed()->findOrFail($id);
        $distributor->restore();

        return redirect()->back()->with('success', 'Distributor restored successfully.');
    }

    public function forceDelete($id)
    {
        $distributor = Distributor::onlyTrashed()->findOrFail($id);

        try {
            $distributor->forceDelete();
            return redirect()->back()->with('success', 'Distributor permanently deleted.');
        } catch (LogicException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
