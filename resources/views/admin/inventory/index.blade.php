@extends('admin.admin-layout')
@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    @include('admin.inventory._breadcrump')

    {{-- Success & Error messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="bg-yellow-100 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif


    <div
        class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
        <div class="mx-auto w-full max-w-6xl">

            {{-- Title --}}
            @if($productName)
                <h1 class="text-2xl font-semibold mb-4">Inventory Ledger :: {{ $productName }}</h1>
            @else
                <h1 class="text-2xl font-semibold mb-4">Inventory Ledger :: All Products</h1>
            @endif

            {{-- Action Bar --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-4">

                {{-- Filter & Export --}}
                <form method="GET" action="{{ route('admin.inventory.index') }}"
                      class="flex flex-wrap gap-2 items-center">

                    <label class="text-sm font-medium">Filter by Product:</label>
                    <select name="product_id" class="border rounded px-3 py-1">
                        <option value="">-- All Product --</option>
                        @foreach($products as $product)
                            @continue($product->type === 'variable') {{-- Skip variable parent --}}
                            @php
                                $productName = $product->type === 'variant'
                                    ? ($product->parent->category->name ?? '') . ' - ' . ($product->attributes['fragrance'] ?? '')
                                    : $product->name;
                            @endphp
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $productName }}
                            </option>
                        @endforeach
                    </select>

                    <button class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">Filter</button>

                    @if (request('product_id'))
                        <a href="{{ route('admin.inventory.index') }}"
                           class="text-sm text-gray-600 underline ml-1">Clear</a>
                    @endif

                    <a href="{{ route('admin.inventory.export', ['product_id' => request('product_id')]) }}"
                       class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">
                        ⬇️ Export
                    </a>
                </form>

                {{-- Add Inventory --}}
                <a href="{{ route('admin.inventory.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center">
                    + Add Inventory Transaction
                </a>
            </div>

            {{-- Responsive Table --}}
            <div class="overflow-x-auto">
                <table class="w-full table-auto border text-center min-w-[700px]">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Date</th>
                            <th class="p-2 border">Product</th>
                            <th class="p-2 border">Type</th>
                            <th class="p-2 border">Quantity</th>
                            <th class="p-2 border">Remarks</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                            <tr class="border-t">
                                <td class="p-2">{{ $tx->date }}</td>
                                <td class="p-2">
                                    @php
                                        $product = $tx->product;
                                        $productName = $product->type === 'variant'
                                            ? ($product->parent->name ?? '') . ' - ' . ($product->attributes['fragrance'] ?? '')
                                            : $product->name;
                                    @endphp
                                    {{ $productName }}

                                    @if ($product->type === 'variant')
                                        <br>
                                        <span class="text-sm text-gray-600">
                                            ({{ implode(', ', array_filter([$product->attributes['fragrance'] ?? null, $product->attributes['size'] ?? null])) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="p-2 capitalize">{{ $tx->type }}</td>
                                <td class="p-2">{{ $tx->quantity }}</td>
                                <td class="p-2">{{ $tx->remarks }}</td>
                                <td class="p-2 whitespace-nowrap text-center relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="bg-gray-200 text-sm px-3 py-1 rounded hover:bg-gray-300 focus:outline-none">
                                        Actions ▾
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition
                                        class="absolute right-0 z-20 mt-1 w-32 bg-white border border-gray-200 rounded shadow-md text-left">
                                        <a href="{{ route('admin.inventory.edit', $tx->id) }}"
                                           class="block px-3 py-2 text-sm text-blue-600 hover:bg-gray-100 hover:text-blue-800">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('admin.inventory.destroy', $tx->id) }}" method="POST"
                                              onsubmit="return confirm('Delete this entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 hover:text-red-700">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-400">No Inventories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.pageXData = { page: 'inventory' };
</script>
@endpush
