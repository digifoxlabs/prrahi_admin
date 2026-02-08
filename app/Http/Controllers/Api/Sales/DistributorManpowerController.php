<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\DistributorManpower;
use Illuminate\Http\Request;

class DistributorManpowerController extends Controller
{
    public function index(Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        return response()->json([
            'data' => $distributor->manpowers,
        ]);
    }

    public function store(Request $request, Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        $validated = $request->validate([
            'sales' => ['nullable', 'string'],
            'accounts' => ['nullable', 'string'],
            'godown' => ['nullable', 'string'],
        ]);

        $manpower = $distributor->manpowers()->create($validated);

        return response()->json([
            'message' => 'Manpower added',
            'data' => $manpower,
        ], 201);
    }

    public function update(
        Request $request,
        Distributor $distributor,
        DistributorManpower $manpower
    ) {
        $this->authorizeDistributor($distributor);

        abort_if($manpower->distributor_id !== $distributor->id, 403);

        $validated = $request->validate([
            'sales' => ['nullable', 'string'],
            'accounts' => ['nullable', 'string'],
            'godown' => ['nullable', 'string'],
        ]);

        $manpower->update($validated);

        return response()->json([
            'message' => 'Manpower updated',
            'data' => $manpower,
        ]);
    }

    public function destroy(Distributor $distributor, DistributorManpower $manpower)
    {
        $this->authorizeDistributor($distributor);

        abort_if($manpower->distributor_id !== $distributor->id, 403);

        $manpower->delete();

        return response()->json([
            'message' => 'Manpower deleted',
        ]);
    }

    protected function authorizeDistributor(Distributor $distributor)
    {
        abort_if(
            $distributor->sales_persons_id !== auth('sales_api')->id(),
            403
        );
    }
}
