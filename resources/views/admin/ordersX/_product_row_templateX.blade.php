<template id="product-row-template">
<tr>
    <td class="px-2 py-1 border">
      <input type="hidden" class="product-id" name="product_ids[__INDEX__]" value="__ID__">
        <span>__NAME__</span>
    </td>

    <td class="px-2 py-1 border">
        <input type="hidden" name="prices[]" value="__PRICE__">
        <span class="price">__PRICE__</span>
    </td>

    <td class="px-2 py-1 border">
        <input type="hidden" name="dozen_cases[]" value="__DOZEN_CASE__">
        <span class="dozen-case">__DOZEN_CASE__</span>
    </td>

    <td class="px-2 py-1 border">
        <input type="number" name="quantities[]" value="1" min="1" class="qty-input w-20 border rounded px-2 py-1">
    </td>

    <td class="px-2 py-1 border">
        <input type="text" name="totals[]" readonly value="__PRICE__" class="row-total w-24 border rounded px-2 py-1 bg-gray-100 text-right">
    </td>

    <td class="px-2 py-1 border text-center">
        <button type="button" class="text-red-600 remove-row">Remove</button>
    </td>
</tr>
</template>
