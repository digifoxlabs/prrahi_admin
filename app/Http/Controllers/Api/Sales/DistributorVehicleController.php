<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\DistributorVehicle;
use Illuminate\Http\Request;

class DistributorVehicleController extends Controller
{
    public function index(Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        return response()->json([
            'data' => $distributor->vehicles,
        ]);
    }

    public function store(Request $request, Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        $validated = $request->validate([
            'two_wheeler' => ['nullable', 'integer'],
            'three_wheeler' => ['nullable', 'integer'],
            'four_wheeler' => ['nullable', 'integer'],
        ]);

        $vehicle = $distributor->vehicles()->create($validated);

        return response()->json([
            'message' => 'Vehicle added',
            'data' => $vehicle,
        ], 201);
    }

    public function update(
        Request $request,
        Distributor $distributor,
        DistributorVehicle $vehicle
    ) {
        $this->authorizeDistributor($distributor);

          abort_if(
            (int) $vehicle->distributor_id !== (int) $distributor->id,
            403
        );

        $validated = $request->validate([
            'two_wheeler' => ['nullable', 'integer'],
            'three_wheeler' => ['nullable', 'integer'],
            'four_wheeler' => ['nullable', 'integer'],
        ]);

        $vehicle->update($validated);

        return response()->json([
            'message' => 'Vehicle updated',
            'data' => $vehicle,
        ]);
    }

    public function destroy(Distributor $distributor, DistributorVehicle $vehicle)
    {
        $this->authorizeDistributor($distributor);


        abort_if(
            (int) $vehicle->distributor_id !== (int) $distributor->id,
            403
        );

        $vehicle->delete();

        return response()->json([
            'message' => 'Vehicle deleted',
        ]);
    }

    protected function authorizeDistributor(Distributor $distributor): void
    {
        $salesPerson = auth('sales_api')->user();

        abort_if(!$salesPerson, 403, 'Unauthenticated');

        abort_if(
            (int) $distributor->sales_persons_id !== (int) $salesPerson->id,
            403,
            'Unauthorized'
        );
    }
}
