<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\DistributorBank;
use Illuminate\Http\Request;

class DistributorBankController extends Controller
{
    public function index(Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        return response()->json([
            'data' => $distributor->banks()->orderBy('bank_name')->get(),
        ]);
    }

    public function store(Request $request, Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        $validated = $request->validate([
            'bank_name' => ['required', 'string'],
            'branch_name' => ['nullable', 'string'],
            'current_ac' => ['nullable', 'string'],
            'ifsc' => ['nullable', 'string'],
        ]);

        $bank = $distributor->banks()->create($validated);

        return response()->json([
            'message' => 'Bank added',
            'data' => $bank,
        ], 201);
    }

    public function update(
        Request $request,
        Distributor $distributor,
        DistributorBank $bank
    ) {
        $this->authorizeDistributor($distributor);

        abort_if($bank->distributor_id !== $distributor->id, 403);

        $validated = $request->validate([
            'bank_name' => ['required', 'string'],
            'branch_name' => ['nullable', 'string'],
            'current_ac' => ['nullable', 'string'],
            'ifsc' => ['nullable', 'string'],
        ]);

        $bank->update($validated);

        return response()->json([
            'message' => 'Bank updated',
            'data' => $bank,
        ]);
    }

    public function destroy(Distributor $distributor, DistributorBank $bank)
    {
        $this->authorizeDistributor($distributor);

        abort_if($bank->distributor_id !== $distributor->id, 403);

        $bank->delete();

        return response()->json([
            'message' => 'Bank deleted',
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
