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
use App\Models\AttendanceEntry;
use App\Models\AttendanceRegister;
use App\Models\AttendanceRegisterParticipant;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //Show Dashboard

    public function dashboard()
    {

        $title = 'Dashboard';
        $allRoles = Role::all();
        $allPermissions = Permission::all();
        $user = auth('admin')->user();
        $today = Carbon::today()->toDateString();

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

        $attendanceRegister = $this->findTodayAttendanceRegister();
        $todayAttendanceEntry = null;
        $nextAttendanceAction = null;

        if ($attendanceRegister && $user) {
            $attendanceParticipant = $this->findUserParticipant($attendanceRegister, $user);

            if ($attendanceParticipant) {
                $todayAttendanceEntry = AttendanceEntry::query()
                    ->where('attendance_register_id', $attendanceRegister->id)
                    ->where('participant_id', $attendanceParticipant->id)
                    ->whereDate('attendance_date', $today)
                    ->first();
            }

            if (! $todayAttendanceEntry || ! $todayAttendanceEntry->in_time) {
                $nextAttendanceAction = 'in';
            } elseif (! $todayAttendanceEntry->out_time) {
                $nextAttendanceAction = 'out';
            } else {
                $nextAttendanceAction = 'done';
            }
        }

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
            'totalRetailers',
            'attendanceRegister',
            'todayAttendanceEntry',
            'nextAttendanceAction'
        ));
    }

    public function markMyAttendance(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        /** @var User|null $user */
        $user = auth('admin')->user();
        if (! $user) {
            return redirect()->route('admin.login')->with('error', 'Please login to continue.');
        }

        $today = Carbon::today()->toDateString();
        $register = $this->findTodayAttendanceRegister();

        if (! $register) {
            return redirect()->back()->with('error', 'No attendance register found for today.');
        }

        $holidayOverride = $register->dateOverrides()->whereDate('attendance_date', $today)->first();
        if ($holidayOverride && $holidayOverride->is_holiday) {
            return redirect()->back()->with('error', 'Today is marked as holiday. Attendance cannot be marked.');
        }

        $participant = $this->ensureUserParticipant($register, $user);

        $entry = AttendanceEntry::firstOrNew([
            'attendance_register_id' => $register->id,
            'participant_id' => $participant->id,
            'attendance_date' => $today,
        ]);

        $now = now();
        $currentTime = $now->format('H:i:s');
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;

        if (! $entry->in_time) {
            $entry->status = 'present';
            $entry->in_time = $currentTime;
            $entry->in_latitude = $latitude;
            $entry->in_longitude = $longitude;
            $entry->marked_by = $user->id;
            $entry->source = 'admin';
            $entry->save();

            return redirect()->back()->with('success', 'Attendance IN marked at ' . $now->format('h:i A') . '.');
        }

        if (! $entry->out_time) {
            $outTime = $now;
            $inDateTime = Carbon::parse($today . ' ' . $entry->in_time);
            if ($outTime->lte($inDateTime)) {
                $outTime = $inDateTime->copy()->addMinute();
            }

            $entry->status = 'present';
            $entry->out_time = $outTime->format('H:i:s');
            $entry->out_latitude = $latitude;
            $entry->out_longitude = $longitude;
            $entry->marked_by = $user->id;
            $entry->source = 'admin';
            $entry->save();

            return redirect()->back()->with('success', 'Attendance OUT marked at ' . $outTime->format('h:i A') . '.');
        }

        return redirect()->back()->with('error', 'Today attendance is already fully marked (IN and OUT).');
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

    protected function findTodayAttendanceRegister(): ?AttendanceRegister
    {
        $today = Carbon::today()->toDateString();

        return AttendanceRegister::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();
    }

    protected function ensureUserParticipant(AttendanceRegister $register, User $user): AttendanceRegisterParticipant
    {
        $displayName = trim((string) ($user->fname ?? '') . ' ' . (string) ($user->lname ?? ''));
        if ($displayName === '') {
            $displayName = (string) ($user->email ?? ('User #' . $user->id));
        }

        return $register->participants()->firstOrCreate(
            [
                'employee_type' => 'user',
                'employee_id' => $user->id,
            ],
            [
                'identifier' => (string) ($user->email ?? ('user-' . $user->id)),
                'display_name' => $displayName,
                'sort_name' => strtolower($displayName),
            ]
        );
    }

    protected function findUserParticipant(AttendanceRegister $register, User $user): ?AttendanceRegisterParticipant
    {
        return $register->participants()
            ->where('employee_type', 'user')
            ->where('employee_id', $user->id)
            ->first();
    }
}
