<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DistributorController extends Controller
{
   

public function index(Request $request)
{
    // $salesPerson = $request->user();
    $salesPerson = auth('sales_api')->user();


    $distributors = Distributor::where(
            'sales_persons_id',
            $salesPerson->id
        )
        ->orderBy('firm_name')
        ->get()
        ->map(function ($d) {

            // ✅ ALWAYS RETURN FULL URL
            if ($d->profile_photo) {
                $d->profile_photo = asset('storage/' . $d->profile_photo);
            }

            return $d;
        });

    return response()->json([
        'data' => $distributors,
    ]);
}



    //Store New Distributor
    public function store(Request $request)
    {
      
        // $salesPerson = $request->user();
        $salesPerson = auth('sales_api')->user();


        $validated = $request->validate([
            'appointment_date' => ['required', 'date'],
            'firm_name' => ['required', 'string', 'max:255'],
            'nature_of_firm' => ['required', 'string'],
            'address_line_1' => ['nullable', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'town' => ['nullable', 'string'],
            'district' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'pincode' => ['nullable', 'string'],
            'landmark' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'contact_person' => ['nullable', 'string'],
            'designation_contact' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'gst' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'date_of_anniversary' => ['nullable', 'date'],
        ]);

        // Generate login_id (8-digit datetime: ddMMyyHH)
        $now = now();
        // $loginId = $now->format('dmyH');
        // ✅ Generate unique login_id safely
        do {
            $loginId = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Distributor::where('login_id', $loginId)->exists());

        $distributor = Distributor::create([
            ...$validated,
            'sales_persons_id' => $salesPerson->id,
            'login_id' => $loginId,
            'password' => Hash::make('password@123'),
            'appointed_by_type' => get_class($salesPerson),
            'appointed_by_id' => $salesPerson->id,
        ]);

        return response()->json([
            'message' => 'Distributor created successfully',
            'data' => $distributor,
        ], 201);
    }

    //Update Distributor
public function update(Request $request, Distributor $distributor)
{
    $salesPerson = auth('sales_api')->user();

    abort_if(!$salesPerson, 403, 'Unauthenticated');

    abort_if(
        $distributor->sales_persons_id !== $salesPerson->id,
        403,
        'Unauthorized'
    );

    $validated = $request->validate([
        'firm_name' => 'required|string',
        'nature_of_firm' => 'required|string',

        'contact_person' => 'nullable|string',
        'designation_contact' => 'nullable|string',
        'contact_number' => 'nullable|string',
        'email' => 'nullable|email',

        'address_line_1' => 'nullable|string',
        'address_line_2' => 'nullable|string',
        'town' => 'nullable|string',
        'district' => 'nullable|string',
        'state' => 'nullable|string',
        'pincode' => 'nullable|string',
        'landmark' => 'nullable|string',

        'gst' => 'nullable|string',
        'date_of_birth' => 'nullable|date',
        'date_of_anniversary' => 'nullable|date',
    ]);

    $distributor->update($validated);

    return response()->json([
        'message' => 'Distributor updated successfully',
        'data' => $distributor,
    ]);
}


public function uploadProfilePhoto(Request $request, $id)
{
    $request->validate([
        'profile_photo' => 'required|image|max:2048', // max 2MB
    ]);

    $distributor = Distributor::findOrFail($id);

    // ✅ Delete old image if exists
    if (
        $distributor->profile_photo &&
        Storage::disk('public')->exists($distributor->profile_photo)
    ) {
        Storage::disk('public')->delete($distributor->profile_photo);
    }

    // ✅ Store new image (SAME PATH & FORMAT AS WEB)
    $filename =
        'profile_' .
        Str::random(10) .
        '.' .
        $request->file('profile_photo')->getClientOriginalExtension();

    $path = $request
        ->file('profile_photo')
        ->storeAs('distributors', $filename, 'public');

    // ✅ Update DB
    $distributor->profile_photo = $path;
    $distributor->save();

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Profile image updated successfully.',
    //     'path' => asset('storage/' . $path), // 👈 IMPORTANT for Flutter
    // ]);


        return response()->json([
            'success' => true,
            'message' => 'Profile image updated successfully.',
            'data' => $distributor->fresh(),
        ]);


}
    public function changePassword(
    Request $request,
    Distributor $distributor
    ) {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $distributor->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

}
