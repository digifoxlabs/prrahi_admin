<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesType;
use Illuminate\Validation\Rule;

class SalesTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_sales_type')->only(['index']);
        $this->middleware('permission:create_sales_type')->only(['store']);
        $this->middleware('permission:edit_sales_type')->only(['update']);
        $this->middleware('permission:delete_sales_type')->only(['destroy', 'restore', 'forceDelete']);
    }

    public function index(Request $request)
    {
        $title = 'SalesType';
        $view = $request->query('view', 'active');

        $salesTypesQuery = SalesType::query();
        if ($view === 'trashed') {
            $salesTypesQuery->onlyTrashed();
        } elseif ($view === 'all') {
            $salesTypesQuery->withTrashed();
        }

        $salesTypes = $salesTypesQuery
            ->latest()
            ->paginate(20);
        $salesTypes->appends($request->query());

        return view('admin.sales-types.index', compact('salesTypes', 'title', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_type' => 'required|string|max:100|unique:sales_types,sales_type',
        ]);

        SalesType::create($validated);

        return back()->with('success', 'Sales type added successfully.');
    }

    public function update(Request $request, SalesType $salesType)
    {
        $validated = $request->validate([
            'sales_type' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sales_types', 'sales_type')->ignore($salesType->id),
            ],
        ]);

        $salesType->update($validated);

        return back()->with('success', 'Sales type updated successfully.');
    }

    public function destroy(SalesType $salesType)
    {
        $salesType->delete();
        return back()->with('success', 'Sales type deleted.');
    }

    public function restore($id)
    {
        $salesType = SalesType::onlyTrashed()->findOrFail($id);
        $salesType->restore();

        return back()->with('success', 'Sales type restored successfully.');
    }

    public function forceDelete($id)
    {
        $salesType = SalesType::onlyTrashed()->findOrFail($id);
        $salesType->forceDelete();

        return back()->with('success', 'Sales type permanently deleted.');
    }

}
