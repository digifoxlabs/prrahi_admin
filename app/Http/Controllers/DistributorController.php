<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\Distributor;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\OrderActor;
use App\Services\Distributors\{
    CreateDistributorService,
    StoreDistributorRelationsService,
};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DistributorController extends Controller
{
    
    // Store Distributor Data
    public function store(Request $request)
    {
        //validate data
        $data = $this->validatedData($request);

        //Check Who is creating the Retailer
        $actor = OrderActor::resolve();

        try {

        // Call Create Distributor Service
          $distributor = CreateDistributorService::create([


                'sales_persons_id' => $data['sales_persons_id'],
                'appointment_date' => $data['appointment_date'],
                'firm_name' => $data['firm_name'],
                'nature_of_firm' => $data['nature_of_firm'],
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'],
                'town' => $data['town'],
                'district' => $data['district'],
                'state' => $data['state'],
                'pincode' => $data['pincode'],
                'landmark' => $data['landmark'],
                'contact_person' => $data['contact_person'],
                'designation_contact' => $data['designation_contact'],
                'contact_number' => $data['contact_number'],
                'email' => $data['email'],
                'gst' => $data['gst'],
                'date_of_birth' => $data['date_of_birth'],
                'date_of_anniversary' => $data['date_of_anniversary'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'login_id' => $data['login_id'],
                'password' => Hash::make($data['password']),

                //Polymorphic
                'appointed_by_type'=> $actor['type'],
                'appointed_by_id'=> $actor['id'],

          ]);


        //Save Distributor Relations Data
          StoreDistributorRelationsService::execute(
                $distributor,
                $request->all()
            );

            //Return
            return $this->redirectAfterSave($distributor, $actor['role'])
            ->with('success', 'Distributor Created successfully.');

        } catch (\Throwable $e) {
        return back()
            ->withErrors(['error' => $e->getMessage()])
            ->withInput(); // 🔴 REQUIRED
         }

    }


    //Update

    public function update(Request $request, Distributor $distributor){

        //validate data
        $data = $this->validatedData($request, $distributor);

        //Check Who is creating the Retailer
        $actor = OrderActor::resolve();

        DB::beginTransaction();

        try{

        

        //Update Distributor
        $distributor->update([

            'sales_persons_id' => $data['sales_persons_id'],
            'appointment_date' => $data['appointment_date'],
            'firm_name' => $data['firm_name'],
            'nature_of_firm' => $data['nature_of_firm'],
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'],
            'town' => $data['town'],
            'district' => $data['district'],
            'state' => $data['state'],
            'pincode' => $data['pincode'],
            'landmark' => $data['landmark'],
            'contact_person' => $data['contact_person'],
            'designation_contact' => $data['designation_contact'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],
            'gst' => $data['gst'],
            'date_of_birth' => $data['date_of_birth'],
            'date_of_anniversary' => $data['date_of_anniversary'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'login_id' => $data['login_id'],         

        ]);

        //Handle password Change
        if (!empty($data['password'])) {
            $distributor->update([
                'password' => Hash::make($data['password'])
            ]);
        }

        //Remove Old Relations
        $distributor->companies()->delete();
        $distributor->banks()->delete();
        $distributor->godowns()->delete();
        $distributor->manpowers()->delete();
        $distributor->vehicles()->delete();

        //Save Distributor Relations Data
        StoreDistributorRelationsService::execute(
            $distributor,
            $request->all()
        );

        DB::commit();

        //Return
        return $this->redirectAfterUpdate($distributor, $actor['role'])
        ->with('success', 'Distributor Updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }



    }



    //Common Validation Data
    private function validatedData(Request $request, ?Distributor $distributor = null): array
    {
    $distributorId = $distributor?->id;

    return $request->validate(
        [
            'sales_persons_id' => ['nullable', 'exists:sales_persons,id'],
            'appointment_date' => ['required', 'date'],
            'firm_name'        => ['required', 'string'],
            'nature_of_firm'   => ['required', 'string'],

            'address_line_1'   => ['nullable', 'string'],
            'address_line_2'   => ['nullable', 'string'],
            'town'             => ['nullable', 'string'],
            'district'         => ['nullable', 'string'],
            'state'            => ['nullable', 'string'],
            'pincode'          => ['nullable', 'string'],
            'landmark'         => ['nullable', 'string'],

            'contact_person'       => ['nullable', 'string'],
            'designation_contact'  => ['nullable', 'string'],
            'contact_number'       => ['nullable', 'string'],

            // ✅ EMAIL (ignore current distributor on update)
            'email' => [
                'nullable',
                'email',
                Rule::unique('distributors', 'email')->ignore($distributorId),
            ],

            'gst'                 => ['nullable', 'string'],
            'date_of_birth'       => ['nullable', 'date'],
            'date_of_anniversary' => ['nullable', 'date'],
            'latitude'            => ['nullable', 'numeric'],
            'longitude'           => ['nullable', 'numeric'],

            // ✅ LOGIN ID (ignore current distributor on update)
            'login_id' => [
                'required',
                'string',
                Rule::unique('distributors', 'login_id')->ignore($distributorId),
            ],

            // ✅ PASSWORD: required only on CREATE
            'password' => [
                $distributor ? 'nullable' : 'required',
                'string',
                'min:6',
            ],
        ],
        [
            'firm_name.required'  => 'Please enter Firm Name',
            'login_id.required'   => 'Please enter Distributor Login ID',
            'login_id.unique'     => 'This Login ID is already in use',
            'email.unique'        => 'This email is already registered',
            'password.required'   => 'Password is required while creating distributor',
        ]
    );
}



   //Common Return Function
    protected function redirectAfterSave(Distributor $distributor, string $actor)
    {
        return match ($actor) {
            'admin'       => redirect()->route('admin.distributors.index'),
            // 'distributor' => redirect()->route('distributor.retailers.index'),
            'sales'       => redirect()->route('sales.retailers.index'),
            default       => abort(403),
        };
    }

    //cOMMON fUNCTION AFTER UPDATE
    protected function redirectAfterUpdate(Distributor $distributor, string $actor)
    {
        return match ($actor) {
           'admin'       => redirect()->route('admin.distributors.show', $distributor),
           //'distributor' => redirect()->route('distributor.retailers.show', $distributor),
            'sales'       => redirect()->route('sales.distributors.show', $distributor),
            default       => abort(403),
        };
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












}
