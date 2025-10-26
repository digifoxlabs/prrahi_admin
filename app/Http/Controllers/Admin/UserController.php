<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

  public function __construct()
    {
        $this->middleware('permission:view_users')->only(['index', 'show']);
        $this->middleware('permission:create_users')->only(['create', 'store']);
        $this->middleware('permission:edit_users')->only(['edit', 'update']);
        $this->middleware('permission:delete_users')->only(['destroy']);
    }



    /**
     * Display a listing of the resource.
     */
      public function index(Request $request)
    {

        $title ='Roles';   

        $search = $request->query('search');

        $users = User::with('roles')
            ->when($search, fn($query) =>
                $query->where('fname', 'like', "%$search%")
                      ->orWhere('lname', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
            )
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title ='Roles';  
      
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles','title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'mobile_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city_town' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $title ='Users';  
        $user = User::with('roles')->findOrFail($id);
        return view('admin.users.show', compact('user','title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
         $title ='users';  
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles','title'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city_town' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
        ]);

        $user->update($validated);
        $user->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
        public function destroy(User $user)
        {
           // $user = User::findOrFail($id);


            // Optional: Prevent deletion of currently logged-in user
            if (auth('admin')->id() === $user->id) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        }

     public function assignRoles(User $userdata)
    {

        $title ='Roles';  
        $user = auth('admin')->user(); 
        $roles = Role::all();
        return view('admin.users.assign_roles', compact('user', 'roles','title','userdata'));
    }

    public function storeRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array|nullable',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($request->roles ?? []);
        return redirect()->route('admin.users.index')->with('success', 'Roles updated successfully.');
    }



    public function export(Request $request)
    {
        $search = $request->query('search');
        return Excel::download(new UsersExport($search), 'users_with_roles.xlsx');
    }

    
    public function uploadImage(Request $request) 

        {
            $request->validate([
                'image' => 'required|image|max:2048',
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->user_id);

            // Delete old image if exists
            // if ($user->profile_image) {
            //     Storage::disk('public')->delete($user->profile_image);
            // }

            $path = $request->file('image')->store('profile_images', 'public');
            $user->profile_image = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'image_url' => asset('storage/' . $path)
            ]);
        }

        //  public function updatePassword(Request $request)
        //     {
        //         $request->validate([
        //             'password' => ['required', 'string', 'min:8', 'confirmed'],
        //         ]);

        //        // $user = auth()->user();
        //         $user->password = bcrypt($request->password);
        //         $user->save();

        //         return response()->json(['success' => true]);
        //     }

       public function updatePassword(Request $request)
        {
            $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = User::findOrFail($request->user_id);
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
        }
            
            




}
