      <div class="hidden lg:block lg:col-span-4 bg-white rounded-xl p-4 h-[95vh] overflow-y-auto border">
            <div class="relative mb-4">
                <input type="text"
                       x-model="search"
                       placeholder="Search products..."
                       class="w-full border rounded-lg p-2 pr-8 text-sm">
                <button x-show="search.length"
                        @click="search=''"
                        type="button"
                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-700">
                    ✕
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="product in filteredProducts()" :key="product.id">
                    <div class="mb-3">

                        <!-- SIMPLE -->
                        <template x-if="product.type === 'simple'">
                            <button @click="addProduct(product)"
                                class="w-full flex items-center gap-3 p-3 border rounded-lg hover:bg-indigo-50 transition-colors group">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center font-bold text-lg group-hover:bg-indigo-200 transition-colors"
                                     x-text="product.name.charAt(0).toUpperCase()">
                                </div>
                                <div class="text-left flex-1">
                                    <p class="font-semibold text-sm" x-text="product.name"></p>
                                    <p class="text-xs text-gray-600" x-text="product.category?.name || 'No Category'"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="product.code"></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-medium text-green-600">
                                        ₹<span x-text="Number(product.ptd_per_dozen || 0).toFixed(2)"></span>
                                    </span>
                                </div>
                            </button>
                        </template>

                        <!-- VARIABLE -->
                        <template x-if="product.type === 'variable'">
                            <div x-data="{ showVariants: false }" class="border rounded-lg overflow-hidden">
                                <!-- Variable Product Header -->
                                <div class="bg-gray-50 p-3 cursor-pointer hover:bg-gray-100 transition-colors"
                                     @click="showVariants = !showVariants">

                                    {{-- <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center font-bold"
                                                 x-text="product.name.charAt(0).toUpperCase()">
                                            </div>
                                            <div class="text-left">
                                                <p class="font-semibold text-sm leading-none tracking-tighter" x-text="product.name"></p>
                                                <p class="text-xs text-gray-600" x-text="product.category?.name || 'No Category'"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs leading-none tracking-tighter bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                <span x-text="product.variants?.length || 0"> </span> variants
                                            </span>
                                            <svg class="w-4 h-4 transition-transform" 
                                                 :class="{ 'rotate-180': showVariants }" 
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div> --}}

                            <div class="flex items-start justify-between gap-3">
                                <!-- LEFT -->
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg
                            flex items-center justify-center
                            font-bold shrink-0" x-text="product.name.charAt(0).toUpperCase()">
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-semibold text-sm leading-snug break-words" x-text="product.name">
                                        </p>

                                        <p class="text-xs text-gray-600 leading-snug break-words"
                                            x-text="product.category?.name || 'No Category'">
                                        </p>
                                    </div>
                                </div>

                                <!-- RIGHT (FIXED WIDTH, NEVER WRAPS) -->
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs bg-blue-100 text-blue-800
                            px-2 py-1 rounded-full whitespace-nowrap">
                                        <span x-text="product.variants?.length || 0"></span> variants
                                    </span>

                                    <svg class="w-4 h-4 transition-transform shrink-0"
                                        :class="{ 'rotate-180': showVariants }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>



                                </div>
                                
                                <!-- Variants List (Collapsible) -->
                                <div x-show="showVariants" x-collapse class="bg-white border-t">
                                    <div class="p-3 space-y-2">
                                        <template x-for="variant in product.variants" :key="variant.id">
                                            <button @click="addProduct(variant)"
                                                class="w-full text-left px-3 py-2 rounded hover:bg-indigo-50 transition-colors text-sm border flex items-center justify-between">
                                                <div>
                                                    <span class="text-gray-700">
                                                        <span x-text="variant.attributes?.fragrance || 'Variant'"></span>
                                                        <span x-show="variant.attributes?.size">
                                                            (<span x-text="variant.attributes.size"></span>)
                                                        </span>
                                                    </span>
                                                    <span class="text-xs text-gray-500 block mt-1" x-text="variant.code"></span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs font-medium text-green-600">
                                                        ₹<span x-text="Number(variant.ptd_per_dozen || 0).toFixed(2)"></span>
                                                    </span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>
            </div>
        </div>