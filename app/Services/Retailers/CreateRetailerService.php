<?php

namespace App\Services\Retailers;

use App\Models\Retailer;
use Illuminate\Support\Facades\DB;

class CreateRetailerService
{
    /**
     * Create Retailer 
     */
    public static function create(array $data): Retailer
    {

        return DB::transaction(function () use ($data) {

            return Retailer::create([


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

          // polymorphic creator
            'appointed_by_type'  => $data['created_by_type'],
            'appointed_by_id'    => $data['created_by_id'],

            ]);
        });
    }
}
