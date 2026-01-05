    <div class="overflow-x-auto">
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">Product</th>
                            <th class="p-2">Code</th>
                            <th class="p-2">Qty</th>
                            <th class="p-2">Rate</th>
                            <th class="p-2">Per</th>
                            <th class="p-2">Disc %</th>
                            <th class="p-2 text-right">Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="(item,index) in cart" :key="item.id">
                            <tr class="border-t">
                                <td class="p-2" x-text="item.name"></td>
                                <td class="p-2" x-text="item.code"></td>
                                <td class="p-2">
                                    <input type="number" min="1"
                                           x-model.number="item.qty"
                                           @input="recalculate()"
                                           class="w-16 border rounded p-1 text-sm">
                                </td>
                                <td class="p-2" x-text="item.rate.toFixed(2)"></td>
                                <td class="p-2" x-text="item.base_unit"></td>
                                <td class="p-2" x-text="item.discount"></td>
                                <td class="p-2 text-right" x-text="item.amount.toFixed(2)"></td>
                                <td class="p-2 text-center">
                                    <button type="button"
                                            @click="removeItem(index)"
                                            class="text-red-500 hover:text-red-700">
                                        ✕
                                    </button>
                                </td>

                                <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                                <input type="hidden" :name="`items[${index}][quantity]`" :value="item.qty">
                                <input type="hidden" :name="`items[${index}][rate]`" :value="item.rate">
                                <input type="hidden" :name="`items[${index}][base_unit]`" :value="item.base_unit">
                                <input type="hidden" :name="`items[${index}][discount_percent]`" :value="item.discount">
                                <input type="hidden" :name="`items[${index}][amount]`" :value="item.amount">
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>