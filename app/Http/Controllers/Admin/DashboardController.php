<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Distributor;
use App\Models\Retailer;
use App\Models\SalesPerson;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    //Show Dashboard

    public function dashboard()
    {

        $title = 'Dashboard';
        $allRoles = Role::all();
        $allPermissions = Permission::all();
        $user = auth('admin')->user();

        $ordersPending = Order::where('status', 'pending')->count();

        $ordersConfirmed = Order::where('status', 'confirmed')->count();

        $ordersDispatchedDelivered = Order::whereIn('dispatch_status', ['dispatched', 'delivered'])->count();

        $ordersCancelled = Order::where('status', 'cancelled')->count();

        $distributors = Distributor::select('id', 'firm_name', 'latitude', 'longitude')->get();
        $retailers = Retailer::select('id', 'retailer_name', 'latitude', 'longitude')->get();

        $totalProducts = Product::with(['category', 'subCategory', 'parent.category', 'parent.subCategory'])
            ->whereIn('type', ['simple', 'variant'])
            ->count();

        $totalDistributor = Distributor::count();
        $totalSalesPerson = SalesPerson::count();
        $totalRetailers = Retailer::count();

        $orders = Order::with('distributor')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return view('admin.pages.dashboard', compact(
            'allRoles',
            'user',
            'allPermissions',
            'totalProducts',
            'title',
            'totalDistributor',
            'totalSalesPerson',
            'orders',
            'distributors',
            'ordersPending',
            'ordersConfirmed',
            'ordersDispatchedDelivered',
            'ordersCancelled',
            'retailers',
            'totalRetailers'
        ));
    }


    //Show Profile Page
    public function profile()
    {

        $title = 'Profile';
        $user = auth('admin')->user();
        return view('admin.pages.profile', compact('title', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mobile_number' => 'required|string|numeric',
            // Add other validations as needed
        ]);

        $user = auth('admin')->user(); // or however you're fetching the admin user
        $user->update($request->only(['fname', 'lname', 'mobile_number', 'address', 'district', 'city_town', 'state', 'country', 'pincode'])); // Add more if needed

        return response()->json(['success' => true, 'message' => 'Profile updated']);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['success' => true]);
    }


    public function uploadImage(Request $request)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $user = auth('admin')->user();

        // Delete old image
        // if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
        //     Storage::disk('public')->delete($user->profile_image);
        // }

        $filename = 'profile_' . $user->id . '.' . $request->file('image')->extension();
        $path = $request->file('image')->storeAs('profile_images', $filename, 'public');

        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'path' => asset('storage/' . $path)
        ]);
    }
}
