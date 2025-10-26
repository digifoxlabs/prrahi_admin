<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermissionsExport;


class PermissionController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:view_permissions')->only(['index', 'show']);
        $this->middleware('permission:create_permissions')->only(['create', 'store']);
        $this->middleware('permission:edit_permissions')->only(['edit', 'update']);
        $this->middleware('permission:delete_permissions')->only(['destroy']);
    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $title ='Permissions';  
        $user = auth('admin')->user(); 

        $search = $request->query('search');

        $permissions = Permission::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString(); // Preserve search on pagination

        return view('admin.permissions.index', compact('permissions', 'search','title','user'));
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Permissions';  
        $user = auth('admin')->user(); 
         return view('admin.permissions.create', compact('title','user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|unique:permissions,name|max:255',
        ]);

       // Permission::create(['name' => $request->name]);

            $permission = Permission::create([
            'name' => $request->name,
        ]);

        // Automatically assign to Admin role
         $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->attach($permission->id);
        }

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $title ='Permissions';  
        $user = auth('admin')->user(); 
        return view('admin.permissions.edit', compact('permission','title','user'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id . '|max:255',
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated.');
    }
    /**
     * Remove the specified resource from storage.
     */
      public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('success', 'Permission deleted.');
    }


    //Export Permissions
    public function export(Request $request)
    {
        $search = $request->input('search');

        $permissions = Permission::query()
            ->when($search, fn($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->orderBy('id', 'desc')
            ->get();

        return Excel::download(new PermissionsExport($permissions), 'permissions.xlsx');
    }

}
