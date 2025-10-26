<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TallyInvoice;
use App\Models\Order;

class TallyInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Invoices';
        // $invoices = TallyInvoice::with('order')
        //     ->latest()
        //     ->paginate(15);


    $search = $request->input('search');
  

    $invoices = TallyInvoice::with('order')
        ->when($search, function ($query, $search) {
            $query->where('order_number', 'like', "%{$search}%");    
        })
        ->latest()
        ->paginate(15);

        return view('admin.tally_invoices.index', compact('invoices','title','search'));

    }

    public function show($id)
    {

        $title = 'Invoices';

        $invoice = TallyInvoice::with('order')->findOrFail($id);

        // // Parse XML (same as earlier)
        // $xml = simplexml_load_string($invoice->xml_data);
        // $voucher = $xml->BODY->IMPORTDATA->REQUESTDATA->TALLYMESSAGE->VOUCHER ?? null;

        // $data = [
        //     'order_number'   => $invoice->order_number,
        //     'voucher_number' => (string)($voucher->VOUCHERNUMBER ?? ''),
        //     'date'           => (string)($voucher->DATE ?? ''),
        //     'party'          => (string)($voucher->PARTYNAME ?? ''),
        //     'narration'      => (string)($voucher->NARRATION ?? ''),
        //     'entries'        => [],
        // ];

        // if ($voucher && isset($voucher->{'ALLLEDGERENTRIES.LIST'})) {
        //     foreach ($voucher->{'ALLLEDGERENTRIES.LIST'} as $entry) {
        //         $data['entries'][] = [
        //             'ledger' => (string)($entry->LEDGERNAME ?? ''),
        //             'amount' => (string)($entry->AMOUNT ?? ''),
        //         ];
        //     }
        // }



    // Pretty print XML safely
    $xmlContent = $invoice->xml_data;

    try {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlContent);
        $prettyXml = $dom->saveXML();
    } catch (\Exception $e) {
        $prettyXml = $xmlContent; // fallback if malformed XML
    }



        // return view('admin.tally_invoices.show', compact('invoice', 'data'));




        //$invoice = TallyInvoice::findOrFail($id);
        return view('admin.tally_invoices.show', compact('invoice','title','prettyXml'));

     }

    public function destroy($id)
    {
        $invoice = TallyInvoice::findOrFail($id);
        $orderNumber = $invoice->order_number;
        $invoice->delete();

        return redirect()->route('admin.tally.invoices.index')
            ->with('success', "Invoice for Order {$orderNumber} has been deleted.");
    }
}
