<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitNote;
use App\Models\SalesPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitController extends Controller
{
    // public function index(Request $request)
    // {
    //     $selectedSalesPersonId = $request->sales_person_id;

    //     $title = "Sales-Visits";

    //     // Sidebar grouped chats
    //     $chatGroups = VisitNote::with('documents')
    //         ->select('sales_person_id')
    //         ->groupBy('sales_person_id')
    //         ->latest()
    //         ->get()
    //         ->map(function ($visit) {

    //             $lastVisit = VisitNote::where('sales_person_id', $visit->sales_person_id)
    //                 ->latest()
    //                 ->first();

    //             return [
    //                 'sales_person' => SalesPerson::find($visit->sales_person_id),
    //                 'last_message' => $lastVisit?->message,
    //                 'last_time'    => $lastVisit?->created_at,
    //             ];
    //         });

    //     $visits = collect();
    //     $selectedSalesPerson = null;

    //     if ($selectedSalesPersonId) {
    //         $selectedSalesPerson = SalesPerson::find($selectedSalesPersonId);

    //         $visits = VisitNote::with('documents')
    //             ->where('sales_person_id', $selectedSalesPersonId)
    //             ->orderByDesc('created_at')
    //             ->paginate(15);
    //     }

    //     return view('admin.visits.index', compact(
    //         'chatGroups',
    //         'visits',
    //         'selectedSalesPerson',
    //         'selectedSalesPersonId',
    //         'title',
    //     ));
    // }

public function index(Request $request)
{
    $title = "Sales-Visits";

    $selectedSalesPersonId = $request->sales_person_id;

    // Sidebar grouped chats (optimized version)
    $chatGroups = VisitNote::select('sales_person_id')
        ->groupBy('sales_person_id')
        ->get()
        ->map(function ($visit) {

            $lastVisit = VisitNote::where('sales_person_id', $visit->sales_person_id)
                ->latest()
                ->first();

            return [
                'sales_person' => SalesPerson::find($visit->sales_person_id),
                'last_message' => $lastVisit?->message,
                'last_time'    => $lastVisit?->created_at,
            ];
        });

    // Always define variables
    $visits = VisitNote::with([
        'documents',
        'retailer',
        'distributor'
    ])
    ->where('sales_person_id', $selectedSalesPersonId)
    ->orderBy('created_at')
    ->paginate(15);



    $selectedSalesPerson = null;

    if ($selectedSalesPersonId) {

        $selectedSalesPerson = SalesPerson::find($selectedSalesPersonId);

        $visits = VisitNote::with('documents')
            ->where('sales_person_id', $selectedSalesPersonId)
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    return view('admin.visits.index', [
        'chatGroups' => $chatGroups,
        'visits' => $visits,
        'selectedSalesPerson' => $selectedSalesPerson,
        'selectedSalesPersonId' => $selectedSalesPersonId,
        'title' => $title,
    ]);
}





    /**
     * Infinite Scroll Loader
     */
    public function loadMore(Request $request)
    {
        $visits = VisitNote::with('documents')
            ->where('sales_person_id', $request->sales_person_id)
            ->where('created_at', '<', $request->last_created_at)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return view('admin.visits.partials.messages', compact('visits'))->render();
    }


//     public function show(SalesPerson $salesPerson)
// {
//     $visits = VisitNote::with('documents')
//         ->where('sales_person_id', $salesPerson->id)
//         ->orderBy('created_at')
//         ->get()
//         ->map(function ($visit) {

//             if ($visit->entity_type === 'distributor') {
//                 $entity = \App\Models\Distributor::find($visit->entity_id);
//                 $visit->entity_label = 'Distributor';
//                 $visit->entity_name = $entity?->firm_name ?? 'Unknown';
//             } else {
//                 $entity = \App\Models\Retailer::find($visit->entity_id);
//                 $visit->entity_label = 'Retailer';
//                 $visit->entity_name = $entity?->retailer_name ?? 'Unknown';
//             }

//             return $visit;
//         });

//     return view('admin.visits.show', compact('salesPerson', 'visits'));
// }



    // public function destroy(VisitNote $visitNote)
    // {
    //     $visitNote->delete();

    //     //  return redirect()->route('admin.visits.index')->with('success', 'Chat deleted successfully.');
    //     return response()->json(['success' => true]);
    // }

    public function destroy(VisitNote $visitNote)
    {
        $visitNote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully'
        ]);
    }
}
