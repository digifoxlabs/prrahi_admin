<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RolesExport;

class RoleController extends Controller
{


  public function __construct()
    {
        $this->middleware('permission:view_roles')->only(['index', 'show']);
        $this->middleware('permission:create_roles')->only(['create', 'store']);
        $this->middleware('permission:edit_roles')->only(['edit', 'update']);
        $this->middleware('permission:delete_roles')->only(['destroy']);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $title ='Roles';  
        $user = auth('admin')->user(); 

        $search = $request->query('search');

        // $roles = Role::query()
        //     ->when($search, function ($query, $search) {
        //         $query->where('name', 'like', '%' . $search . '%');
        //     })
        //     ->orderBy('id', 'desc')
        //     ->paginate(10)
        //     ->onEachSide(1)
        //     ->withQueryString(); // Preserve search on pagination


            $roles = Role::with('permissions') // Eager load permissions
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orderBy('id', 'asc')
                ->paginate(10)
                ->onEachSide(1)
                ->withQueryString(); // Preserve search on pagination

         return view('admin.roles.index', compact('roles', 'search','title','user'));


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Roles';  
        $user = auth('admin')->user(); 
          $permissions = Permission::all();
         return view('admin.roles.create', compact('title','user','permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'array|exists:permissions,id',
            ]);

            // Create Role
            $role = Role::create([
                'name' => $validated['name'],
            ]);

            // Attach Permissions
            if (!empty($validated['permissions'])) {
                $role->permissions()->attach($validated['permissions']);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
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
public function edit(Role $role)
{

      if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Admin role cannot be edited.');
    }

    $title ='Roles';  
    $user = auth('admin')->user(); 
    $permissions = Permission::all();
    $role->load('permissions');
    return view('admin.roles.edit', compact('role', 'permissions','title','user'));

}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {


        //Protect Admin Role from update
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Admin role cannot be updated.');
        }



        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array|exists:permissions,id',
        ]);

        // Update Role name
        $role->update([
            'name' => $validated['name'],
        ]);

        // Sync Permissions
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {

        //Protect Admin Role from delete
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Admin role cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }


    public function export(Request $request)
    {
        $search = $request->query('search');

        return Excel::download(new RolesExport($search), 'roles.xlsx');
    }   

}
