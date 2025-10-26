<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TallyInvoice;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class TallyController extends Controller
{
    public function store(Request $request, $orderNumber) {


        // ✅ Security check: verify secret header
        $headerSecret = $request->header('X-Tally-Secret');
        $expectedSecret = config('services.tally.secret');

        if (!$expectedSecret || $headerSecret !== $expectedSecret) {
            return response($this->tallyResponse(0, "Unauthorized: Invalid or missing X-Tally-Secret"), 401)
                ->header('Content-Type', 'text/xml');
        }


        try {


        // ✅ Validate order exists
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response($this->buildTallyResponse(0, "Invalid order number: {$orderNumber}"), 404)
                ->header('Content-Type', 'text/xml');
        }

        // ✅ Prevent duplicate upload
        if ($order->tallyInvoice) {
            return response($this->buildTallyResponse(0, "Invoice already exists for Order {$orderNumber}. Delete it first."))
                ->header('Content-Type', 'text/xml');
        }

        // ✅ Get raw XML
        $xmlData = $request->getContent();
        if (empty($xmlData)) {
            return response($this->buildTallyResponse(0, "Empty XML received"), 400)
                ->header('Content-Type', 'text/xml');
        }

        // ✅ Save invoice
        // $invoice = TallyInvoice::create([
        //     'order_number' => $orderNumber,
        //     'xml_data'     => $xmlData,
        // ]);


            $invoice = DB::transaction(function () use ($orderNumber, $xmlData, $order) {

                $invoice = TallyInvoice::create([
                    'order_number' => $orderNumber,
                    'xml_data'     => $xmlData,
                ]);

                // Try to set 'completed' if your DB supports it; otherwise fallback to 'confirmed'
           $order->status = 'confirmed';
                    $order->save();

                return $invoice;
            });



        return response($this->buildTallyResponse(1, "Invoice saved with ID {$invoice->id} for Order {$orderNumber}"))
            ->header('Content-Type', 'text/xml');


         } catch (\Throwable $e) {
            return response($this->tallyResponse(0, "Error: ".$e->getMessage()), 500)
                ->header('Content-Type', 'text/xml');
        }




    }

    public function destroy($id)
    {
        $invoice = TallyInvoice::findOrFail($id);
        $orderNumber = $invoice->order_number;
        $invoice->delete();

        return redirect()->route('tally.invoices.index')
            ->with('success', "Invoice for Order {$orderNumber} deleted. You can upload a new one.");
    }

    private function buildTallyResponse($status, $message)
            {
                return <<<XML
        <RESPONSE>
        <STATUS>{$status}</STATUS>
        <MESSAGE>{$message}</MESSAGE>
        </RESPONSE>
        XML;
            }



     private function tallyResponse(int $status, string $message): string
    {
        $status = $status ? 1 : 0;
        $msg = htmlspecialchars($message, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        return "<RESPONSE><STATUS>{$status}</STATUS><MESSAGE>{$msg}</MESSAGE></RESPONSE>";
    }


               /**
     * View the stored XML (HTML view)
     */
    public function show($id)
    {
        $invoice = TallyInvoice::findOrFail($id);
        return view('tally_invoices.show', compact('invoice'));
    }



}
