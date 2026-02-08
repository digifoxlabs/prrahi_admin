<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\DistributorGodown;
use Illuminate\Http\Request;

class DistributorGodownController extends Controller
{
    public function index(Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        return response()->json([
            'data' => $distributor->godowns()->get(),
        ]);
    }

    public function store(Request $request, Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        $validated = $request->validate([
            'no_godown' => ['required', 'integer'],
            'godown_size' => ['nullable', 'string'],
        ]);

        $godown = $distributor->godowns()->create($validated);

        return response()->json([
            'message' => 'Godown added',
            'data' => $godown,
        ], 201);
    }

    public function update(
        Request $request,
        Distributor $distributor,
        DistributorGodown $godown
    ) {
        $this->authorizeDistributor($distributor);

        abort_if(
            (int) $godown->distributor_id !== (int) $distributor->id,
            403
        );

        $validated = $request->validate([
            'no_godown' => ['required', 'integer'],
            'godown_size' => ['nullable', 'string'],
        ]);

        $godown->update($validated);

        return response()->json([
            'message' => 'Godown updated',
            'data' => $godown,
        ]);
    }

    public function destroy(Distributor $distributor, DistributorGodown $godown)
    {
        $this->authorizeDistributor($distributor);



        abort_if(
            (int) $godown->distributor_id !== (int) $distributor->id,
            403
        );

        $godown->delete();

        return response()->json([
            'message' => 'Godown deleted',
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
