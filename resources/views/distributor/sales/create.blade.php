@extends('distributor.layout')

@section('page-content')
<div class="max-w-6xl mx-auto p-4">

    <h2 class="text-xl font-bold mb-4">Sell to Retailer</h2>

    <form method="POST" action="{{ route('distributor.sales.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block font-medium mb-1">Retailer</label>
            <select name="retailer_id" class="w-full border p-2 rounded">
                @foreach($retailers as $r)
                    <option value="{{ $r->id }}">{{ $r->retailer_name }}</option>
                @endforeach
            </select>
        </div>

        <table class="w-full text-sm mb-4">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Available</th>
                    <th>Qty</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $idx => $stock)
                <tr>
                    <td>{{ $stock->product->product_name }}</td>
                    <td>{{ $stock->available_qty }}</td>

                    <td>
                        <input type="number" name="items[{{ $idx }}][qty]" min="0"
                               class="w-20 border p-1">
                        <input type="hidden" name="items[{{ $idx }}][product_id]"
                               value="{{ $stock->distributor_product_id }}">
                    </td>

                    <td>
                        <input type="number" name="items[{{ $idx }}][rate]"
                               value="{{ $stock->product->ptr }}"
                               class="w-24 border p-1">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            Save Sale
        </button>
    </form>

</div>
@endsection
