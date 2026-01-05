<template id="product-row-template">
<tr>
    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
      <input type="hidden" class="product-id" name="product_ids[__INDEX__]" value="__ID__">
      <span class="text-gray-800 dark:text-white/90">__NAME__</span>
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="hidden" name="prices[]" value="__PRICE__">
        <span class="price text-gray-600 dark:text-gray-300">__PRICE__</span>
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="hidden" class="dozen-cases-input" name="dozen_cases[]" value="__DOZEN_CASE__">
        <span class="dozen-case text-gray-600 dark:text-gray-300">__DOZEN_CASE__</span>
    </td>

    <!-- NEW Free Dozen Per Case column -->
    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <span id="free-dozen-case" class="free-dozen text-gray-600 dark:text-gray-300">__FREE_DOZEN_CASE__</span>
        <input type="hidden"
               name="free_dozen_case[]"
               value="__FREE_DOZEN_CASE__"
               class="free-dozen-input w-20 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 text-right dark:bg-transparent dark:text-white/90">
        
        <input type="hidden"
               name="has_free_qty[]"
               value="__HAS_FREE_QTY__"
               class="has-free-qty w-20 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 text-right dark:bg-transparent dark:text-white/90">
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="number" name="quantities[]" value="__QTY__" min="1"
               class="qty-input w-20 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 bg-white dark:bg-transparent text-gray-900 dark:text-white/90">
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700">
        <input type="text" name="totals[]" readonly value="__TOTAL__"
               class="row-total w-24 border border-gray-300 dark:border-gray-700 rounded px-2 py-1 bg-gray-100 dark:bg-white/5 text-right text-gray-900 dark:text-white/90">
    </td>

    <td class="px-2 py-1 border border-gray-200 dark:border-gray-700 text-center">
        <button type="button" class="text-red-600 dark:text-red-400 remove-row">Remove</button>
    </td>
</tr>
</template>
