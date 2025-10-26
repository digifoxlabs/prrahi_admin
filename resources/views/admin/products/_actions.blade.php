@php
    $isVariant = $product->parent_id !== null;
    $isVariable = $product->type === 'variable' && $product->parent_id === null;
    $isSimple = $product->type === 'simple';
@endphp


    <button @click="open = !open"
            class="bg-gray-100 text-gray-800 px-3 py-1 rounded hover:bg-gray-200 focus:outline-none focus:ring">
        Actions ▾
    </button>

    <div x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-50 text-left"
    >
        {{-- Inventory (for both simple products and variants) --}}
        <a href="{{ route('admin.inventory.index') }}?product_id={{ $product->id }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
            📦 Inventory
        </a>

        {{-- Edit Buttons --}}
        @if ($isSimple || $isVariable)
            <a href="{{ route('admin.products.edit', $product) }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                ✏️ Edit Product
            </a>
        @endif

        @if ($isVariant)
            <a href="{{ route('admin.products.edit', $product) }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                ✏️ Edit Variant
            </a>

            <a href="{{ route('admin.products.edit', $product->parent_id) }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                ✏️ Edit Parent Product
            </a>

            <a href="{{ route('admin.products.add-variant', $product->parent_id) }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                ✏️ Add Variant
            </a>          

        @endif

        {{-- Manage Variants --}}
        @if ($product->type === 'variable')
            <a href="{{ route('admin.products.variants.index', $product->id) }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                🧩 Manage Variants
            </a>
        @endif

        {{-- Delete --}}
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this product?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-100 hover:text-red-600">
                🗑️ Delete
            </button>
        </form>
    </div>

