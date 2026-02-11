<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Retailer;
use App\Models\SalesPerson;
use Illuminate\Http\Request;

class RetailerController extends Controller
{
    public function index()
    {
        return Retailer::where('appointed_by_type', SalesPerson::class)
            ->where('appointed_by_id', auth()->id())
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Basic
            'retailer_name'       => 'nullable|string|max:255',

            'address_line_1'      => 'required|string|max:255',
            'address_line_2'      => 'nullable|string|max:255',
            'town'                => 'nullable|string|max:255',

            'state'            => 'required|string|max:255',
            'district'         => 'required|string|max:255',

            'pincode'             => 'nullable|string|max:10',
            'landmark'            => 'nullable|string|max:255',

            // Contact
            'contact_person'      => 'required|string|max:255',
            'contact_number'      => 'required|string|max:20',
            'email'               => 'nullable|email|max:255',

            // Business
            'gst'                 => 'nullable|string|max:50',
            'nature_of_outlet'    => 'nullable|string|max:255',

            // Dates
            'date_of_birth'       => 'nullable|date',
            'date_of_anniversary' => 'nullable|date',
            'appointment_date'    => 'nullable|date',

            // Location
            'latitude'            => 'nullable|numeric',
            'longitude'           => 'nullable|numeric',

            // Relations
            'distributor_id'      => 'nullable|exists:distributors,id',
        ]);

        // 🔐 Sales ownership (polymorphic)
        $data['appointed_by_type'] = SalesPerson::class;
        $data['appointed_by_id']   = auth()->id();

        // 🕒 Safety: if Flutter didn't send appointment_date
        $data['appointment_date'] ??= now()->toDateString();

        return Retailer::create($data);
    }

    public function show(Retailer $retailer)
    {
        $retailer->load('distributor:id,firm_name');

        return response()->json([
            ...$retailer->toArray(),
            'distributor_name' => $retailer->distributor?->firm_name,
        ]);
    }


    public function update(Request $request, Retailer $retailer)
    {
        $this->authorizeRetailer($retailer);

        $retailer->update(
            $request->only([
                'retailer_name',
                'address_line_1',
                'address_line_2',
                'town',
                'state',
                'district',
                'pincode',
                'landmark',
                'contact_person',
                'contact_number',
                'email',
                'gst',
                'nature_of_outlet',
                'date_of_birth',
                'date_of_anniversary',
                'distributor_id',
                'latitude',
                'longitude',
            ])
        );

        return $retailer;
    }

    public function destroy(Retailer $retailer)
    {
        $this->authorizeRetailer($retailer);
        $retailer->delete();

        return response()->noContent();
    }

    private function authorizeRetailer(Retailer $retailer): void
    {
        abort_if(
            $retailer->appointed_by_type !== SalesPerson::class ||
               (int) $retailer->appointed_by_id !== (int) auth('sale_api')->id(),
            403,
            'Unauthorized retailer access'
        );
    }
}
