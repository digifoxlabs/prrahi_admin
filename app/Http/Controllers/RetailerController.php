<?php

namespace App\Http\Controllers;
use App\Models\Retailer;
use App\Models\Distributor;
use App\Models\State;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\OrderActor;
use App\Services\Retailers\{
    CreateRetailerService,
};


class RetailerController extends Controller
{


    //Store
    public function store(Request $request){

        //validate data
        $data = $this->validatedData($request);

        //Check Who is creating the Retailer
        $actor = OrderActor::resolve();

        //Call Create Retailer Service
         $retailer = CreateRetailerService::create([



            'retailer_name' => $data['retailer_name'],
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'],
            'town' => $data['town'],

            'state' => $data['state'],
            'district' => $data['district'],

            'pincode' => $data['pincode'],
            'landmark' => $data['landmark'],

            'contact_person' => $data['contact_person'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],

            'gst' => $data['gst'],
            'date_of_birth' => $data['date_of_birth'],
            'date_of_anniversary' => $data['date_of_anniversary'],
            'nature_of_outlet' => $data['nature_of_outlet'],
            'appointment_date' => $data['appointment_date'],
            'distributor_id' => $data['distributor_id'],


            'created_by_type'  => $actor['type'],
            'created_by_id'    => $actor['id'],

        ]);


        //Return
        return $this->redirectAfterSave($retailer, $actor['role'])
        ->with('success', 'Retailer Created successfully.');

    }


    //Update
    public function update(Request $request, Retailer $retailer)
    {

        $data = $this->validatedData($request, $retailer);
        $actor = OrderActor::resolve();

        //Update Retailer
            $retailer->update([

            'retailer_name' => $data['retailer_name'],
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'],
            'town' => $data['town'],

            'state' => $data['state'],
            'district' => $data['district'],

            'pincode' => $data['pincode'],
            'landmark' => $data['landmark'],

            'contact_person' => $data['contact_person'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],

            'gst' => $data['gst'],
            'date_of_birth' => $data['date_of_birth'],
            'date_of_anniversary' => $data['date_of_anniversary'],
            'nature_of_outlet' => $data['nature_of_outlet'],
            'appointment_date' => $data['appointment_date'],
            'distributor_id' => $data['distributor_id'],


            ]);


        return $this->redirectAfterUpdate($retailer, $actor['role'])
        ->with('success', 'Retailer Updated successfully.');



    }
















    //Common Validation Data
   private function validatedData(Request $request, ?Retailer $retailer = null): array
    {
        return $request->validate([

            'retailer_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',

            'state' => 'required|string',
            'district' => 'required|string',

            'pincode' => 'nullable|string|max:10',
            'landmark' => 'nullable|string|max:255',

            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email',

            'gst' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'date_of_anniversary' => 'nullable|date',
            'nature_of_outlet' => 'nullable|string|max:255',
            'appointment_date' => 'required|date',
            'distributor_id' => 'nullable|exists:distributors,id',

        ],
    
        [
                'retailer_name.required' => 'Please enter Retailer Name',
        ] );
    }



    //Common Return Function
    protected function redirectAfterSave(Retailer $retailer, string $actor)
    {
        return match ($actor) {
            'admin'       => redirect()->route('admin.retailers.index', $retailer)->with('success', 'Retailer created successfully.'),
            //'distributor' => redirect()->route('distributor.orders.index', $order),
            'sales'       => redirect()->route('sales.retailers.index', $retailer),
            default       => abort(403),
        };
    }


    protected function redirectAfterUpdate(Retailer $retailer, string $actor)
    {
        return match ($actor) {
           // 'admin'       => redirect()->route('admin.orders.index', $retailer)->with('success', 'Retailer updated successfully.'),
           // 'distributor' => redirect()->route('distributor.orders.index', $retailer),
            'sales'       => redirect()->route('sales.retailers.index', $retailer),
            default       => abort(403),
        };
    }



    //Fetch Districts List
    public function getDistricts(Request $request)
    {
        $state = State::where('name', $request->state)->first();
        $districts = $state ? $state->districts()->orderBy('name')->pluck('name') : [];
        return response()->json($districts);
    }




}
