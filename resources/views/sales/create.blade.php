<x-app-layout>
    <div x-data="posSystem()" x-init="initPos()" class="flex h-[calc(100vh-65px)] bg-slate-100 overflow-hidden font-sans">
        
        {{-- LEFT PANEL: PRODUCT CATALOG --}}
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            
            {{-- Top Control Bar (Compact) --}}
            <div class="bg-white border-b border-slate-200 px-4 py-3 flex-shrink-0 flex flex-col gap-3 z-10 shadow-sm">
                
                <div class="flex items-center gap-2">
                    {{-- Sidebar Toggle Button (Desktop) --}}
                    <button @click="sidebarOpen = true" x-show="!sidebarOpen" style="display: none;" class="hidden md:block text-slate-500 hover:text-slate-900 focus:outline-none transition-colors mr-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    {{-- Category Chips (Scrollable horizontally) --}}
                    <div class="flex-1 overflow-x-auto flex gap-1.5 scrollbar-hide items-center justify-start">
                        <button @click="selectedCategory = null" 
                                :class="{'bg-slate-800 text-white border-slate-800 shadow-sm': selectedCategory === null, 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300': selectedCategory !== null}"
                                class="px-3 py-1.5 rounded text-xs font-bold border transition-colors whitespace-nowrap">
                            All Items
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="selectedCategory = cat.id"
                                    :class="{'bg-slate-800 text-white border-slate-800 shadow-sm': selectedCategory === cat.id, 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300': selectedCategory !== cat.id}"
                                    class="px-3 py-1.5 rounded text-xs font-bold border transition-colors whitespace-nowrap"
                                    x-text="cat.name">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input x-model="searchQuery" type="text" class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md text-sm bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-inner" placeholder="Scan barcode or type to search...">
                </div>

            </div>

            {{-- Product Grid (High Density) --}}
            <div class="flex-1 overflow-y-auto p-4 bg-slate-100/50">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)" 
                             class="bg-white rounded border border-slate-200 cursor-pointer hover:border-blue-400 hover:shadow-md transition-all duration-150 overflow-hidden flex flex-col h-full group relative select-none">
                            
                            {{-- Stock Indicator Line (Subtle) --}}
                            <div class="absolute top-0 left-0 w-full h-1" :class="{'bg-emerald-500': product.stock_quantity > 5, 'bg-amber-400': product.stock_quantity > 0 && product.stock_quantity <= 5, 'bg-red-500': product.stock_quantity === 0}"></div>
                            
                            <div class="p-3 flex flex-col flex-grow justify-between mt-1">
                                <div class="mb-3">
                                    <h3 class="text-slate-900 font-bold text-sm leading-tight mb-1 group-hover:text-blue-700 transition-colors line-clamp-2" x-text="product.name"></h3>
                                    <p class="text-[10px] font-mono text-slate-400 uppercase tracking-tight truncate" x-text="product.barcode || 'NO BARCODE'"></p>
                                </div>
                                <div class="mt-auto pt-2 border-t border-slate-100 flex flex-col gap-0.5">
                                    <span class="text-base font-black text-slate-900" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                                    <span class="text-[10px] font-bold tracking-wide" 
                                          :class="{'text-emerald-600': product.stock_quantity > 5, 'text-amber-600': product.stock_quantity > 0 && product.stock_quantity <= 5, 'text-red-600': product.stock_quantity === 0}" 
                                          x-text="product.stock_quantity + ' left in stock'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-24 text-slate-400">
                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-sm font-bold text-slate-500">No items found</p>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: CHECKOUT TERMINAL --}}
        <div class="w-80 lg:w-96 bg-white border-l border-slate-200 flex flex-col flex-shrink-0 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.03)] z-20">
            
            {{-- Terminal Header --}}
            <div class="px-4 py-3 bg-slate-900 flex justify-between items-center text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <h2 class="text-xs font-bold tracking-widest uppercase">Current Sale</h2>
                </div>
                <button @click="cart = []" x-show="cart.length > 0" class="text-[10px] font-bold uppercase tracking-wider text-red-400 hover:text-red-300 transition-colors">Void All</button>
            </div>

            {{-- Cart Items (Dense List) --}}
            <div class="flex-1 overflow-y-auto bg-white p-2">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="group relative py-1.5 px-2 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded transition-colors flex items-center justify-between">
                        <div class="flex-1 pr-2 min-w-0">
                            <h4 class="font-bold text-slate-800 text-xs leading-tight truncate" x-text="item.name"></h4>
                            <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1.5 font-mono">
                                <span class="font-bold text-slate-700" x-text="item.qty"></span>
                                <span>×</span>
                                <span x-text="'$' + parseFloat(item.price).toFixed(2)"></span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="font-black text-slate-900 text-sm" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                            
                            {{-- Micro Controls --}}
                            <div class="flex items-center bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click.stop="updateQty(index, -1)" class="w-5 h-5 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors border-r border-slate-200">-</button>
                                <button type="button" @click.stop="updateQty(index, 1)" class="w-5 h-5 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors">+</button>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty Cart State --}}
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-300 text-center px-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-1">Cart Empty</p>
                    <p class="text-[10px] text-slate-400">Scan barcode or select an item</p>
                </div>
            </div>

            {{-- Checkout Control Panel --}}
            <div class="bg-slate-50 border-t border-slate-200 p-4">
                
                {{-- Subtotals (Dense) --}}
                <div class="space-y-1 mb-4 font-mono text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span class="tracking-wide">SUBTOTAL</span>
                        <span class="font-bold text-slate-700" x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500 pb-2 border-b border-slate-200" x-show="discountAmount > 0">
                        <span class="tracking-wide text-amber-600">DISCOUNT <span x-text="discountType === 'percentage' ? '('+discountValue+'%)' : ''"></span></span>
                        <span class="font-bold text-amber-600" x-text="'-$' + discountAmount.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-end pt-1">
                        <span class="text-sm font-black text-slate-900 tracking-widest">TOTAL</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tighter" x-text="'$' + grandTotal.toFixed(2)"></span>
                    </div>
                </div>

                {{-- Error Displays --}}
                @if(session('error') || $errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded mb-4 shadow-sm text-xs">
                        {{ session('error') ?? $errors->first() }}
                    </div>
                @endif

                {{-- Checkout Form --}}
                <form action="{{ route('sales.store') }}" method="POST" id="checkout-form" class="space-y-3">
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
                            <select name="customer_id" class="block w-full border-slate-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-xs font-bold text-slate-700 bg-white h-8 py-0 pl-2 pr-8 shadow-sm transition-all">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Discount Controls --}}
                        <div class="col-span-2 flex gap-2">
                            <input type="hidden" name="discount_type" x-model="discountType">
                            <select x-model="discountType" class="block w-1/2 border-slate-300 rounded focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-[10px] font-bold text-slate-700 bg-white h-8 py-0 pl-2 pr-6 shadow-sm transition-all uppercase tracking-wider">
                                <option value="none">No Discount</option>
                                <option value="percentage">% Off</option>
                                <option value="fixed">$ Off</option>
                            </select>
                            <input type="number" name="discount_value" x-model.number="discountValue" x-show="discountType !== 'none'" min="0" step="0.01" class="block w-1/2 border-slate-300 rounded focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-xs font-bold text-slate-900 bg-white h-8 py-0 px-2 shadow-sm transition-all text-right" placeholder="Amount">
                        </div>

                        {{-- Payment Status Toggle --}}
                        <div class="col-span-2">
                            <input type="hidden" name="payment_status" x-model="paymentStatus">
                            <div class="flex rounded shadow-sm overflow-hidden border border-slate-300 bg-white">
                                <button type="button" @click="paymentStatus = 'paid'" :class="{'bg-slate-900 text-white': paymentStatus === 'paid', 'text-slate-600 hover:bg-slate-100 border-r border-slate-200': paymentStatus !== 'paid'}" class="flex-1 h-8 text-[10px] font-black uppercase tracking-wider transition-colors">Paid</button>
                                <button type="button" @click="paymentStatus = 'partial'" :class="{'bg-slate-900 text-white': paymentStatus === 'partial', 'text-slate-600 hover:bg-slate-100 border-r border-slate-200': paymentStatus !== 'partial'}" class="flex-1 h-8 text-[10px] font-black uppercase tracking-wider transition-colors">Partial</button>
                                <button type="button" @click="paymentStatus = 'pending'" :class="{'bg-slate-900 text-white': paymentStatus === 'pending', 'text-slate-600 hover:bg-slate-100': paymentStatus !== 'pending'}" class="flex-1 h-8 text-[10px] font-black uppercase tracking-wider transition-colors">Pending</button>
                            </div>
                        </div>
                    </div>

                    {{-- Pay Button --}}
                    <div class="pt-2">
                        <button type="button" @click="handleCheckout"
                                :disabled="cart.length === 0"
                                :class="{'opacity-50 cursor-not-allowed bg-slate-300': cart.length === 0, 'bg-blue-600 hover:bg-blue-700 hover:shadow-md hover:-translate-y-px active:bg-blue-800 active:translate-y-0': cart.length > 0}"
                                class="w-full text-white font-black text-sm uppercase tracking-widest rounded h-12 transition-all flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Checkout (Cash)
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Cash Modal Overlay --}}
        <div x-show="showCashModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="showCashModal = false" class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-sm overflow-hidden transform transition-all"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-sm">Cash Tendered</h3>
                    <button @click="showCashModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-5 font-sans">
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Amount Received</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold text-lg">$</span>
                            <input type="number" id="amount_tendered_input" x-model.number="amountTendered" min="0" step="0.01" class="block w-full pl-9 pr-3 py-3 text-3xl font-black text-slate-900 border-slate-300 rounded focus:ring-blue-500 focus:border-blue-500 transition-shadow text-right shadow-inner bg-slate-50">
                        </div>
                    </div>
                    
                    <div class="p-4 rounded bg-slate-50 border border-slate-200 mb-6">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Due</span>
                            <span class="font-bold text-slate-900" x-text="'$' + grandTotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 mt-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Change Due</span>
                            <span class="text-2xl font-black tracking-tight" :class="changeDue >= 0 ? 'text-emerald-600' : 'text-red-500'" x-text="'$' + Math.max(0, changeDue).toFixed(2)"></span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showCashModal = false" type="button" class="flex-1 py-3 bg-white border border-slate-300 rounded text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">Cancel</button>
                        <button @click="confirmCheckout" type="button" :disabled="amountTendered < grandTotal" :class="amountTendered < grandTotal ? 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-md'" class="flex-1 py-3 rounded text-sm font-bold flex items-center justify-center gap-2 transition-all">
                            <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Confirm Sale
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
                    window.addEventListener('keydown', this.handleBarcodeScanner.bind(this));
                },

                handleBarcodeScanner(e) {
                    const currentTime = new Date().getTime();
                    if (currentTime - this.lastKeyTime > 50) this.barcodeBuffer = '';
                    this.lastKeyTime = currentTime;

                    if (e.key === 'Enter' && this.barcodeBuffer.length > 0) {
                        e.preventDefault();
                        this.processBarcode(this.barcodeBuffer);
                        this.barcodeBuffer = '';
                        return;
                    }
                    if (e.key.length === 1) this.barcodeBuffer += e.key;
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

                confirmCheckout() {
                    if (this.amountTendered >= this.grandTotal) {
                        document.getElementById('checkout-form').submit();
                    }
                },

                addToCart(product) {
                    if (product.stock_quantity <= 0) {
                        alert('This product is out of stock!');
                        return;
                    }
                    if (product.expiry_status === 'expired') {
                        if (!confirm('WARNING: The earliest tracked batch of this product is EXPIRED. Are you sure you want to add it to the cart?')) {
                            return;
                        }
                    } else if (product.expiry_status === 'expiring_soon') {
                        if (!confirm('NOTICE: A batch of this product is expiring within 30 days. Proceed?')) {
                            return;
                        }
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