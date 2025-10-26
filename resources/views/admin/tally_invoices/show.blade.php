{{-- resources/views/tally_invoices/xml.blade.php --}}
@extends('admin.admin-layout')

@section('page-content')

<div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-semibold mb-4">
        Tally XML for Order: {{ $invoice->order_number }}
    </h2>

    <pre class="bg-black text-green-200 p-4 rounded overflow-x-auto text-sm leading-relaxed">
<code>{{ htmlentities($prettyXml) }}</code>
    </pre>

    <div class="mt-4">
        {{-- <a href="{{ route('tally.invoice.download', $invoice->id) }}" 
           class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
           Download XML
        </a> --}}
        <a href="{{ route('admin.tally.invoices.index') }}" 
           class="ml-2 px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
           Back to Invoices
        </a>
    </div>
</div>
@endsection
