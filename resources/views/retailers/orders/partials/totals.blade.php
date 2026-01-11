          <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-2 text-sm">
                    <!-- Mobile: Add Product Button -->
                    <div class="lg:hidden mb-4">
                        <button @click="showProductPopup = true"
                                type="button"
                                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            📦 Add More Products
                        </button>
                    </div>
                    
                    <div class="lg:col-start-2">
                        <div class="space-y-1 text-right">
                            <p>Sub Total: <span x-text="subtotal.toFixed(2)"></span></p>

                            <div class="flex justify-end gap-2">
                                <span>Discount:</span>
                                <input type="number" step="0.01"
                                       x-model.number="orderDiscount"
                                       @input="recalculate()"
                                       class="w-24 border rounded p-1 text-right">
                            </div>

                            {{-- <p>CGST (2.5%): <span x-text="cgst.toFixed(2)"></span></p>
                            <p>SGST (2.5%): <span x-text="sgst.toFixed(2)"></span></p> --}}

                            <p x-show="isIntraState">
                                CGST (2.5%): <span x-text="cgst.toFixed(2)"></span>
                            </p>

                            <p x-show="isIntraState">
                                SGST (2.5%): <span x-text="sgst.toFixed(2)"></span>
                            </p>

                            <p x-show="!isIntraState">
                                IGST (5%): <span x-text="igst.toFixed(2)"></span>
                            </p>


                            <div class="flex justify-end gap-2">
                                <span>Round Off:</span>
                                <input type="number" step="0.01"
                                       x-model.number="roundOff"
                                       @input="applyRoundOff()"
                                       class="w-24 border rounded p-1 text-right">
                            </div>

                            <p class="font-bold text-lg mt-2">
                                Total: <span x-text="total.toFixed(2)"></span>
                            </p>


                            <!-- ================= HIDDEN TOTAL FIELDS ================= -->
                            <input type="hidden" name="subtotal" :value="subtotal">
                            <input type="hidden" name="discount_amount" :value="orderDiscount">
                            <input type="hidden" name="cgst" :value="cgst">
                            <input type="hidden" name="sgst" :value="sgst">
                            <input type="hidden" name="igst" :value="igst">
                            <input type="hidden" name="round_off" :value="roundOff">
                            <input type="hidden" name="total_amount" :value="total">

                            <button class="mt-3 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                {{ $isEdit ? 'Update Order' : 'Create Order' }}
                            </button>
                        </div>
                    </div>
                </div>