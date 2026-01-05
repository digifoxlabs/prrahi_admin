<tr>
    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="hidden" class="product-id" name="product_ids[{{ $index ?? 0 }}]" value="{{ $product->id }}">
        <span class="text-gray-800 dark:text-white/90">
            {{ $product->parent
                ? ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ')
                : $product->name
            }}
        </span>
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        @php $price = $price ?? $product->ptd_per_dozen; @endphp
        <input type="hidden" name="prices[]" value="{{ $price }}">
        <span class="price text-gray-600 dark:text-gray-300">{{ number_format($price, 2) }}</span>
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        @php $dozen_case = $dozen_case ?? $product->dozen_per_case; @endphp
        <input type="hidden" name="dozen_cases[]" value="{{ $dozen_case }}">
        <span class="dozen-case text-gray-600 dark:text-gray-300">{{ $dozen_case * $qty }}</span>
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        @php $free_dozen_case = $free_dozen_case ?? 0; @endphp
        <input type="hidden" name="free_dozen_case[]" class="free-dozen-input" value="{{ $free_dozen_case }}">
        <input type="hidden" name="has_free_qty[]" class="has-free-qty" value="{{ $product->has_free_qty }}">
        <span id="free-dozen-case" class="free-dozen text-gray-600 dark:text-gray-300">{{ $free_dozen_case * $qty }}</span>
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="number" name="quantities[]" value="{{ $qty ?? 1 }}" min="1"
               class="qty-input w-20 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 bg-white dark:bg-transparent text-gray-900 dark:text-white/90">
    </td>

    @php
        $qty = $qty ?? 1;
        $total = $total ?? ($price * $dozen_case * $qty);
    @endphp

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="text" name="totals[]" readonly value="{{ $total }}"
               class="row-total w-24 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 bg-gray-100 dark:bg-white/5 text-right text-gray-900 dark:text-white/90">
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700 text-center">
        <button type="button" class="text-red-600 dark:text-red-400 remove-row">Remove</button>
    </td>
</tr>
