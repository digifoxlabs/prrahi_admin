@php
    $isVariant = $product->parent_id !== null;
    $isVariable = $product->type === 'variable' && $product->parent_id === null;
    $isSimple = $product->type === 'simple';
    $adminUser = Auth::guard('admin')->user();
@endphp


    {{-- <button @click="open = !open"
            class="bg-gray-100 text-gray-800 px-3 py-1 rounded hover:bg-gray-200 focus:outline-none focus:ring">
        Actions ▾
    </button> --}}

    {{-- <div x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-50 text-left"
    > --}}
        @if ($product->trashed())
            @if ($adminUser && $adminUser->hasPermission('delete_products'))
            <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 border-b">
                    ♻️ Restore
                </button>
            </form>

            <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST"
                  onsubmit="return confirm('Permanently delete this product? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                    ❌ Permanent Delete
                </button>
            </form>
            @endif
        @else
            {{-- Inventory (for both simple products and variants) --}}
            @if ($adminUser && $adminUser->hasPermission('view_products'))
            <a href="{{ route('admin.inventory.index') }}?product_id={{ $product->id }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                📦 Inventory
            </a>
            @endif

            {{-- Edit Buttons --}}
            @if (($isSimple || $isVariable) && $adminUser && $adminUser->hasPermission('edit_products'))
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                    ✏️ Edit Product
                </a>
            @endif

             @if ($isVariable && $adminUser && $adminUser->hasPermission('create_products'))
                <a href="{{ route('admin.products.add-variant', $product->id) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                    ✏️ Add Variant
                </a> 

             @endif

            @if ($isVariant && $adminUser && $adminUser->hasPermission('edit_products'))
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                    ✏️ Edit Variant
                </a>

                <a href="{{ route('admin.products.edit', $product->parent_id) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                    ✏️ Edit Parent Product
                </a>
            @endif

            @if ($isVariant && $adminUser && $adminUser->hasPermission('create_products'))
                <a href="{{ route('admin.products.add-variant', $product->parent_id) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 border-b">
                    ✏️ Add Variant
                </a>          
            @endif

            {{-- Delete --}}
            @if ($adminUser && $adminUser->hasPermission('delete_products'))
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this product?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-100 hover:text-red-600">
                    🗑️ Delete
                </button>
            </form>
            @endif
        @endif
    {{-- </div> --}}
