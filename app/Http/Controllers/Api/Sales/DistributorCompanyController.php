<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\DistributorCompany;
use Illuminate\Http\Request;

class DistributorCompanyController extends Controller
{
    public function index(Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        return response()->json([
            'data' => $distributor->companies()->orderBy('company_name')->get(),
        ]);
    }

    public function store(Request $request, Distributor $distributor)
    {
        $this->authorizeDistributor($distributor);

        $validated = $request->validate([
            'company_name' => ['required', 'string'],
            'segment' => ['nullable', 'string'],
            'brand_name' => ['nullable', 'string'],
            'products' => ['nullable', 'string'],
            'working_as' => ['nullable', 'string'],
            'margin' => ['nullable', 'string'],
            'payment_terms' => ['nullable', 'string'],
            'working_since' => ['nullable', 'integer'],
            'area_operation' => ['nullable', 'string'],
            'monthly_to' => ['nullable', 'string'],
            'dsr_no' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $company = $distributor->companies()->create($validated);

        return response()->json([
            'message' => 'Company added',
            'data' => $company,
        ], 201);
    }

    public function update(
        Request $request,
        Distributor $distributor,
        DistributorCompany $company
    ) {
        $this->authorizeDistributor($distributor);

        abort_if(
            (int) $company->distributor_id !== (int) $distributor->id,
            403
        );


        $validated = $request->validate([
            'company_name' => ['required', 'string'],
            'segment' => ['nullable', 'string'],
            'brand_name' => ['nullable', 'string'],
            'products' => ['nullable', 'string'],
            'working_as' => ['nullable', 'string'],
            'margin' => ['nullable', 'string'],
            'payment_terms' => ['nullable', 'string'],
            'working_since' => ['nullable', 'integer'],
            'area_operation' => ['nullable', 'string'],
            'monthly_to' => ['nullable', 'string'],
            'dsr_no' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company updated',
            'data' => $company,
        ]);
    }

    public function destroy(Distributor $distributor, DistributorCompany $company)
    {
        $this->authorizeDistributor($distributor);

        abort_if(
            (int) $company->distributor_id !== (int) $distributor->id,
            403
        );


        $company->delete();

        return response()->json([
            'message' => 'Company deleted',
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
