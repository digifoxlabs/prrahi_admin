<?php

namespace App\Services\Distributors;

use App\Models\Distributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDistributorService
{
    /**
     * Create Distributor 
     */
    public static function create(array $data): Distributor
    {
         return DB::transaction(function () use ($data) {

            return Distributor::create([

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
                'password' => $data['password'],
                'appointed_by_type'=> $data['appointed_by_type'],
                'appointed_by_id'=> $data['appointed_by_id'],



            ]);
        });


    }

}