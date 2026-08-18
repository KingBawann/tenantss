<x-app-layout>
    <div x-data="posSystem()" x-init="initPos()" class="flex flex-1 h-full min-h-0 bg-slate-50 overflow-hidden font-sans">
        
        {{-- LEFT PANEL: PRODUCT CATALOG --}}
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative">
            
            {{-- Top Control Bar --}}
            <div class="bg-white border-b border-slate-200 px-6 py-4 flex-shrink-0 flex flex-col gap-4 z-10 shadow-sm relative">
                
                <div class="flex items-center gap-4">
                    {{-- Sidebar Toggle Button (Desktop) --}}
                    <button @click="sidebarOpen = true" x-show="!sidebarOpen" style="display: none;" class="hidden md:block text-slate-400 hover:text-slate-900 focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    {{-- Search Bar --}}
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input id="search_input" x-model="searchQuery" type="text" class="block w-full pl-10 pr-4 py-2.5 border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-inner" placeholder="Scan barcode or type to search... (F2)">
                    </div>
                </div>

                {{-- Category Pills --}}
                <div class="flex overflow-x-auto gap-2 scrollbar-hide items-center justify-start pb-1">
                    <button @click="selectedCategory = null" 
                            :class="{'bg-slate-900 text-white shadow-md': selectedCategory === null, 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50': selectedCategory !== null}"
                            class="px-4 py-2 rounded-full text-xs font-bold border transition-colors whitespace-nowrap tracking-wide">
                        All Items
                    </button>
                    <template x-for="cat in categories" :key="cat.id">
                        <button @click="selectedCategory = cat.id"
                                :class="{'bg-slate-900 text-white shadow-md': selectedCategory === cat.id, 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50': selectedCategory !== cat.id}"
                                class="px-4 py-2 rounded-full text-xs font-bold border transition-colors whitespace-nowrap tracking-wide"
                                x-text="cat.name">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto p-6 bg-transparent relative custom-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-5">
                    <template x-for="(product, index) in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)" 
                             :style="'animation-delay: ' + (Math.min(index, 20) * 30) + 'ms'"
                             class="animate-fade-up bg-white rounded-2xl border border-slate-200 cursor-pointer hover:border-blue-500 hover:shadow-lg hover:-translate-y-1 active:translate-y-0 transition-all duration-200 flex flex-col h-full group relative select-none p-4">
                            
                            <div class="flex flex-col flex-grow justify-between h-full">
                                {{-- Stock Badge --}}
                                <div class="absolute top-3 right-3 shadow-sm rounded-full px-2 py-0.5 text-[9px] font-black tracking-widest uppercase border bg-white"
                                     :class="{'text-emerald-600 border-emerald-100': product.stock_quantity > 5, 'text-amber-600 border-amber-100': product.stock_quantity > 0 && product.stock_quantity <= 5, 'text-red-600 border-red-100': product.stock_quantity === 0}"
                                     x-text="product.stock_quantity === 0 ? 'OUT' : product.stock_quantity">
                                </div>

                                <div>
                                    <h3 class="text-slate-900 font-black text-sm leading-tight mb-1 group-hover:text-blue-600 transition-colors line-clamp-2 pr-8" x-text="product.name"></h3>
                                    <p class="text-[10px] font-mono text-slate-400 uppercase tracking-tight truncate" x-text="product.barcode || 'NO BARCODE'"></p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-lg font-black text-slate-900" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                                    <div class="w-6 h-6 rounded-full bg-slate-50 text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-full text-slate-400">
                    <div class="w-16 h-16 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm mb-4">
                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-500">No items match your search</p>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: CHECKOUT TERMINAL (Older Design Retained) --}}
        <div class="w-80 lg:w-[380px] bg-white border-l border-slate-200 flex flex-col flex-shrink-0 shadow-[-10px_0_30px_-15px_rgba(0,0,0,0.05)] z-20">
            
            {{-- Terminal Header --}}
            <div class="px-5 py-3.5 bg-white border-b border-slate-100 flex flex-col gap-3 text-slate-900 shadow-sm z-10 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h2 class="text-xs font-black tracking-widest uppercase">Current Sale</h2>
                    </div>
                    <button @click="if(confirm('Void entire sale?')) cart = []" x-show="cart.length > 0" class="text-[10px] font-bold uppercase tracking-wider text-red-500 hover:text-red-600 transition-colors bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded flex-shrink-0">Void All</button>
                </div>
                
                {{-- Shortcut Legend Row --}}
                <div class="flex flex-wrap gap-1.5 mt-0.5 text-[8.5px] leading-none tracking-wider">
                    <div class="flex items-center bg-slate-50 rounded border border-slate-200 px-1.5 py-1 transition-colors hover:border-slate-300 cursor-default">
                        <span class="font-black text-slate-700 mr-1">F1</span>
                        <span class="text-slate-400 font-bold uppercase">New Sale</span>
                    </div>
                    <div class="flex items-center bg-blue-50 rounded border border-blue-100 px-1.5 py-1 transition-colors hover:border-blue-200 cursor-default">
                        <span class="font-black text-blue-600 mr-1">F2</span>
                        <span class="text-blue-500 font-bold uppercase">Search</span>
                    </div>
                    <div class="flex items-center bg-indigo-50 rounded border border-indigo-100 px-1.5 py-1 transition-colors hover:border-indigo-200 cursor-default">
                        <span class="font-black text-indigo-600 mr-1">F12</span>
                        <span class="text-indigo-400 font-bold uppercase">Reprint</span>
                    </div>
                    <div class="flex items-center bg-emerald-50 rounded border border-emerald-100 px-1.5 py-1 transition-colors hover:border-emerald-200 cursor-default">
                        <span class="font-black text-emerald-600 mr-1">↑/↓</span>
                        <span class="text-emerald-500 font-bold uppercase">Qty</span>
                    </div>
                    <div class="flex items-center bg-amber-50 rounded border border-amber-100 px-1.5 py-1 transition-colors hover:border-amber-200 cursor-default">
                        <span class="font-black text-amber-600 mr-1">F8</span>
                        <span class="text-amber-500 font-bold uppercase">Void Last</span>
                    </div>
                    <div class="flex items-center bg-red-50 rounded border border-red-100 px-1.5 py-1 transition-colors hover:border-red-200 cursor-default">
                        <span class="font-black text-red-600 mr-1">DEL</span>
                        <span class="text-red-500 font-bold uppercase">Void All</span>
                    </div>
                </div>
            </div>

            {{-- Cart Items List --}}
            <div class="flex-1 overflow-y-auto bg-slate-50 p-3 custom-scrollbar relative">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="group relative py-2.5 px-3 border border-transparent hover:border-slate-200 hover:bg-white rounded-xl transition-all flex items-start justify-between mb-1 shadow-sm hover:shadow-md">
                        <div class="flex-1 pr-3 min-w-0">
                            <h4 class="font-bold text-slate-900 text-sm leading-tight truncate mb-1" x-text="item.name"></h4>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5 font-mono">
                                <span class="font-bold text-slate-700 bg-slate-100 px-1.5 rounded" x-text="item.qty"></span>
                                <span>×</span>
                                <span x-text="'$' + parseFloat(item.price).toFixed(2)"></span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end justify-between self-stretch flex-shrink-0">
                            <span class="font-black text-slate-900 text-sm" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                            
                            {{-- Micro Controls --}}
                            <div class="flex items-center bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden opacity-0 group-hover:opacity-100 transition-opacity mt-auto">
                                <button type="button" @click.stop="updateQty(index, -1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors border-r border-slate-200 active:bg-slate-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                </button>
                                <button type="button" @click.stop="updateQty(index, 1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors active:bg-slate-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty Cart State --}}
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-500 text-center px-4">
                    <div class="w-12 h-12 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center shadow-inner mb-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-400 mb-1">Cart is Empty</p>
                    <p class="text-[11px] font-medium text-slate-400">Scan barcode or select an item</p>
                </div>
            </div>

            {{-- Checkout Control Panel --}}
            <div class="bg-white border-t border-slate-200 p-5 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.05)] z-10 relative">
                
                {{-- Subtotals --}}
                <div class="space-y-1.5 mb-5 font-mono text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span class="tracking-widest uppercase font-semibold">Subtotal</span>
                        <span class="font-bold text-slate-900" x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500 pb-2 border-b border-slate-100" x-show="discountAmount > 0">
                        <span class="tracking-widest uppercase font-semibold text-amber-600">Discount <span x-text="discountType === 'percentage' ? '('+discountValue+'%)' : ''"></span></span>
                        <span class="font-bold text-amber-600" x-text="'-$' + discountAmount.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-end pt-1">
                        <span class="text-sm font-black text-slate-900 tracking-widest uppercase">Total</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tighter" x-text="'$' + grandTotal.toFixed(2)"></span>
                    </div>
                </div>

                {{-- Error Displays --}}
                @if(session('error') || $errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded mb-4 shadow-sm text-xs font-semibold">
                        {{ session('error') ?? $errors->first() }}
                    </div>
                @endif

                {{-- Checkout Form --}}
                <form action="{{ route('sales.store') }}" method="POST" id="checkout-form" class="space-y-4">
                    @csrf
                    <input type="hidden" name="payment_method" x-model="paymentMethod">
                    <input type="hidden" name="amount_tendered" :value="amountTendered">

                    <template x-for="(item, index) in cart" :key="item.id">
                        <div>
                            <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.id">
                            <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.qty">
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Customer Selection --}}
                        <div class="col-span-2">
                            <select name="customer_id" class="block w-full border-slate-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-white h-10 py-0 pl-3 pr-8 shadow-sm transition-all cursor-pointer">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Discount Controls --}}
                        <div class="col-span-2 flex gap-2">
                            <input type="hidden" name="discount_type" x-model="discountType">
                            <select x-model="discountType" class="block w-1/2 border-slate-200 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-[10px] font-bold text-slate-700 bg-slate-50 hover:bg-white h-10 py-0 pl-3 pr-8 shadow-sm transition-all uppercase tracking-wider cursor-pointer">
                                <option value="none">No Discount</option>
                                <option value="percentage">% Off</option>
                                <option value="fixed">$ Off</option>
                            </select>
                            <input type="number" name="discount_value" x-model.number="discountValue" x-show="discountType !== 'none'" min="0" step="0.01" class="block w-1/2 border-slate-200 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white h-10 py-0 px-3 shadow-inner transition-all text-right" placeholder="Amount">
                        </div>

                        {{-- Payment Status Toggle --}}
                        <div class="col-span-2">
                            <input type="hidden" name="payment_status" x-model="paymentStatus">
                            <div class="flex rounded-lg shadow-sm border border-slate-200 bg-slate-100 p-0.5">
                                <button type="button" @click="paymentStatus = 'paid'" :class="{'bg-white text-slate-900 shadow rounded': paymentStatus === 'paid', 'text-slate-500 hover:text-slate-700': paymentStatus !== 'paid'}" class="flex-1 h-8 text-[10px] font-black uppercase tracking-wider transition-all">Paid</button>
                                <button type="button" @click="paymentStatus = 'partial'" :class="{'bg-white text-slate-900 shadow rounded': paymentStatus === 'partial', 'text-slate-500 hover:text-slate-700': paymentStatus !== 'partial'}" class="flex-1 h-8 text-[10px] font-black uppercase tracking-wider transition-all">Partial</button>
                                <button type="button" @click="paymentStatus = 'pending'" :class="{'bg-white text-slate-900 shadow rounded': paymentStatus === 'pending', 'text-slate-500 hover:text-slate-700': paymentStatus !== 'pending'}" class="flex-1 h-8 text-[10px] font-black uppercase tracking-wider transition-all">Pending</button>
                            </div>
                        </div>
                    </div>

                    {{-- Pay Button --}}
                    <div class="pt-3">
                        <button type="button" @click="handleCheckout"
                                :disabled="cart.length === 0"
                                :class="{'opacity-50 cursor-not-allowed bg-slate-200 text-slate-500 border-slate-300': cart.length === 0, 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white shadow-md hover:shadow-lg hover:-translate-y-px active:translate-y-0': cart.length > 0}"
                                class="w-full font-black text-sm uppercase tracking-widest rounded-xl h-14 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Checkout (Enter)
                        </button>
                        

                    </div>
                </form>
            </div>
        </div>

        {{-- Cash Modal Overlay --}}
        <div x-show="showCashModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="showCashModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-sm overflow-hidden transform transition-all"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-black text-slate-900 uppercase tracking-widest text-sm">Cash Tendered</h3>
                    <button @click="showCashModal = false" class="text-slate-400 hover:text-slate-700 bg-white hover:bg-slate-100 p-1.5 rounded-full transition-colors border border-slate-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 font-sans">
                    <div class="mb-6">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2.5">Amount Received</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold text-xl">$</span>
                            <input type="number" id="amount_tendered_input" x-model.number="amountTendered" min="0" step="0.01" class="block w-full pl-10 pr-4 py-4 text-3xl font-black text-slate-900 border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition-shadow text-right shadow-inner bg-slate-50">
                        </div>
                    </div>
                    
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 mb-6">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Total Due</span>
                            <span class="font-bold text-slate-900" x-text="'$' + grandTotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 mt-2">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Change Due</span>
                            <span class="text-2xl font-black tracking-tight" :class="changeDue >= 0 ? 'text-emerald-600' : 'text-red-500'" x-text="'$' + Math.max(0, changeDue).toFixed(2)"></span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button @click="confirmCheckout(true)" type="button" :disabled="amountTendered < grandTotal" :class="amountTendered < grandTotal ? 'opacity-50 cursor-not-allowed bg-slate-200 text-slate-500 border-slate-300' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-md'" class="w-full py-3.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-all">
                            <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Confirm & Print (Shift+Enter)
                        </button>
                        <button @click="confirmCheckout(false)" type="button" :disabled="amountTendered < grandTotal" :class="amountTendered < grandTotal ? 'opacity-50 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'" class="w-full py-3.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-all">
                            Confirm No Print (Shift+Backspace)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js Logic --}}
    <script>
        function posSystem() {
            return {
                products: @json($products),
                categories: @json($categories),
                cart: [],
                searchQuery: '',
                selectedCategory: null,
                paymentStatus: 'paid',
                paymentMethod: 'cash',
                discountType: 'none',
                discountValue: 0,
                showCashModal: false,
                amountTendered: 0,
                barcodeBuffer: '',
                lastKeyTime: 0,
                
                initPos() {
                    this.products.forEach(p => p.price = parseFloat(p.price));
                    window.addEventListener('keydown', this.handleKeydown.bind(this));
                },

                handleKeydown(e) {
                    const isInputFocus = document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA';

                    // Global Shortcuts
                    if (e.key === 'F1') {
                        e.preventDefault();
                        if (confirm('Start new sale?')) this.cart = [];
                        return;
                    }
                    if (e.key === 'F2') {
                        e.preventDefault();
                        const searchInput = document.getElementById('search_input');
                        if (searchInput) searchInput.focus();
                        return;
                    }
                    if (e.key === 'F12') {
                        e.preventDefault();
                        window.open('/sales/latest/receipt', '_blank');
                        return;
                    }

                    // Cart Quantity Shortcuts (Up/Down)
                    if (e.key === 'ArrowUp' && !isInputFocus && this.cart.length > 0) {
                        e.preventDefault();
                        this.updateQty(0, 1);
                        return;
                    }
                    if (e.key === 'ArrowDown' && !isInputFocus && this.cart.length > 0) {
                        e.preventDefault();
                        this.updateQty(0, -1);
                        return;
                    }

                    if (e.key === 'F8') {
                        e.preventDefault();
                        if (this.cart.length > 0) this.removeFromCart(0);
                        return;
                    }
                    if (e.key === 'Delete') {
                        e.preventDefault();
                        if (this.cart.length > 0 && confirm('Void entire sale?')) this.cart = [];
                        return;
                    }
                    if (e.key === 'Escape') {
                        if (this.showCashModal) {
                            this.showCashModal = false;
                        } else {
                            this.searchQuery = '';
                            document.activeElement.blur();
                        }
                        return;
                    }

                    // Shift+Backspace = No Print
                    if (e.key === 'Backspace' && e.shiftKey) {
                        e.preventDefault();
                        if (this.showCashModal || this.paymentMethod !== 'cash') {
                            this.confirmCheckout(false);
                        }
                        return;
                    }

                    // Barcode & Enter Logic
                    const currentTime = new Date().getTime();
                    if (currentTime - this.lastKeyTime > 50) this.barcodeBuffer = '';
                    this.lastKeyTime = currentTime;

                    if (e.key === 'Enter') {
                        if (this.barcodeBuffer.length > 0) {
                            e.preventDefault();
                            this.processBarcode(this.barcodeBuffer);
                            this.barcodeBuffer = '';
                            return;
                        }
                        
                        if (e.shiftKey) {
                            e.preventDefault();
                            if (this.showCashModal || this.paymentMethod !== 'cash') {
                                this.confirmCheckout(true);
                            }
                        } else {
                            // Standard Enter
                            if (!this.showCashModal && this.cart.length > 0 && !isInputFocus) {
                                e.preventDefault();
                                this.handleCheckout();
                            }
                        }
                        return;
                    }
                    
                    // Only buffer single characters
                    if (e.key.length === 1 && !isInputFocus) {
                        this.barcodeBuffer += e.key;
                    }
                },

                processBarcode(barcode) {
                    const product = this.products.find(p => p.barcode === barcode || p.id.toString() === barcode);
                    if (product) {
                        this.addToCart(product);
                        this.searchQuery = '';
                    }
                },

                get filteredProducts() {
                    return this.products.filter(p => {
                        const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                              (p.barcode && p.barcode.toLowerCase().includes(this.searchQuery.toLowerCase()));
                        const matchesCategory = this.selectedCategory === null || p.category_id === this.selectedCategory;
                        return matchesSearch && matchesCategory;
                    });
                },

                get subtotal() {
                    return this.cart.reduce((total, item) => total + (item.price * item.qty), 0);
                },

                get discountAmount() {
                    if (this.discountType === 'none' || this.discountValue <= 0) return 0;
                    if (this.discountType === 'fixed') return parseFloat(this.discountValue);
                    if (this.discountType === 'percentage') return this.subtotal * (parseFloat(this.discountValue) / 100);
                    return 0;
                },

                get grandTotal() {
                    return Math.max(0, this.subtotal - this.discountAmount);
                },

                get changeDue() {
                    return parseFloat(this.amountTendered) - this.grandTotal;
                },

                handleCheckout() {
                    if (this.cart.length === 0) return;
                    
                    if (this.paymentMethod === 'cash') {
                        this.amountTendered = this.grandTotal;
                        this.showCashModal = true;
                        setTimeout(() => {
                            const input = document.getElementById('amount_tendered_input');
                            if (input) {
                                input.focus();
                                input.select();
                            }
                        }, 100);
                    } else {
                        document.getElementById('checkout-form').submit();
                    }
                },

                confirmCheckout(print = true) {
                    if (this.amountTendered >= this.grandTotal) {
                        const form = document.getElementById('checkout-form');
                        if (!print) {
                            const noPrintInput = document.createElement('input');
                            noPrintInput.type = 'hidden';
                            noPrintInput.name = 'no_print';
                            noPrintInput.value = '1';
                            form.appendChild(noPrintInput);
                        }
                        form.submit();
                    }
                },

                addToCart(product) {
                    if (product.stock_quantity <= 0) {
                        alert('This product is out of stock!');
                        return;
                    }
                    const existingIndex = this.cart.findIndex(i => i.id === product.id);
                    if (existingIndex !== -1) {
                        if (this.cart[existingIndex].qty < product.stock_quantity) {
                            this.cart[existingIndex].qty++;
                        }
                    } else {
                        this.cart.unshift({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            max_stock: product.stock_quantity,
                            qty: 1
                        });
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateQty(index, delta) {
                    const item = this.cart[index];
                    const newQty = item.qty + delta;
                    if (newQty > 0 && newQty <= item.max_stock) {
                        item.qty = newQty;
                    } else if (newQty <= 0) {
                        this.removeFromCart(index);
                    }
                }
            }
        }
    </script>
</x-app-layout>