<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\VisitNote;
use App\Models\VisitDocument;
use App\Models\Distributor;
use App\Models\Retailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    /**
     * Visit List (Chat List)
     */

    public function visitThreads()
{
    $salesPersonId = auth('sales_api')->id();

    // DISTRIBUTOR VISITS ONLY
    $distributors = DB::table('visit_notes as vn')
        ->join('distributors as d', function ($join) {
            $join->on('vn.entity_id', '=', 'd.id')
                 ->where('vn.entity_type', 'distributor');
        })
        ->where('vn.sales_person_id', $salesPersonId)
        ->select(
            'd.id as entity_id',
            'd.firm_name as entity_name',
            DB::raw("'distributor' as entity_type"),
            'vn.message as last_message',
            'vn.created_at as last_visited_at'
        )
        ->whereRaw('vn.id = (
            SELECT id FROM visit_notes
            WHERE entity_id = d.id
              AND entity_type = "distributor"
              AND sales_person_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        )', [$salesPersonId]);

    // RETAILER VISITS ONLY
    $retailers = DB::table('visit_notes as vn')
        ->join('retailers as r', function ($join) {
            $join->on('vn.entity_id', '=', 'r.id')
                 ->where('vn.entity_type', 'retailer');
        })
        ->where('vn.sales_person_id', $salesPersonId)
        ->select(
            'r.id as entity_id',
            'r.retailer_name as entity_name',
            DB::raw("'retailer' as entity_type"),
            'vn.message as last_message',
            'vn.created_at as last_visited_at'
        )
        ->whereRaw('vn.id = (
            SELECT id FROM visit_notes
            WHERE entity_id = r.id
              AND entity_type = "retailer"
              AND sales_person_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        )', [$salesPersonId]);

    return response()->json(
        $distributors
            ->unionAll($retailers)
            ->orderByDesc('last_visited_at')
            ->get()
    );
}



    /**
     * Visit Chat History
     */
    public function visitNotes(Request $request)
    {
        $request->validate([
            'entity_id'   => 'required|integer',
            'entity_type' => 'required|in:distributor,retailer',
        ]);

        return VisitNote::with('documents')
            ->where('sales_person_id', auth('sales_api')->id())
            ->where('entity_id', $request->entity_id)
            ->where('entity_type', $request->entity_type)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Store Visit Note (with GEO)
     */
    public function store(Request $request)
    {
        $request->validate([
            'entity_id'   => 'required|integer',
            'entity_type' => 'required|in:distributor,retailer',
            'message'     => 'nullable|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'documents.*' => 'file|max:5120',
        ]);

        $note = VisitNote::create([
            'sales_person_id' => auth('sales_api')->id(),
            'entity_id'       => $request->entity_id,
            'entity_type'     => $request->entity_type,
            'message'         => $request->message,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('visit_documents', 'public');

                VisitDocument::create([
                    'visit_note_id' => $note->id,
                    'file_path'     => $path,
                    'file_type'     => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Visit note saved',
        ]);
    }

    public function visitParties()
    {
        $salesPersonId = auth('sales_api')->id();

        $distributors = \App\Models\Distributor::where(
            'sales_persons_id',
            $salesPersonId
        )->selectRaw("
        id,
        firm_name as name,
        'distributor' as type
    ");

        $retailers = \App\Models\Retailer::where(
            'appointed_by_id',
            $salesPersonId
        )->selectRaw("
        id,
        retailer_name as name,
        'retailer' as type
    ");

        return response()->json(
            $distributors->unionAll($retailers)
                ->orderBy('name')
                ->get()
        );
    }
}
