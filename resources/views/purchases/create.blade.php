<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('purchases.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-2xl text-slate-900 leading-tight">
                    Create Purchase Order
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">

                <form action="{{ route('purchases.store') }}" method="POST" class="p-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        {{-- Supplier --}}
                        <div>
                            <x-input-label for="supplier_id" value="Supplier *" />
                            <select id="supplier_id" name="supplier_id" required class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm">
                                <option value="" disabled selected>-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>

                        {{-- Date --}}
                        <div>
                            <x-input-label for="date" value="Purchase Date *" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Line Items --}}
                    <div class="mb-8">
                        <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Received Items (Stock-In)</h3>
                        <div class="border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                            <table class="min-w-full text-left border-collapse" id="items-table">
                                <thead class="bg-slate-50/80 border-b border-slate-200">
                                    <tr>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Product</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32 text-center">Current Stock</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32">Unit Cost ($)</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32">Quantity</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32 text-right">Subtotal</th>
                                        <th class="p-4 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body" class="bg-white divide-y divide-slate-100">
                                    {{-- Rows added by JS --}}
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-50/50 border-t border-slate-200">
                                        <td colspan="4" class="p-4 text-right font-bold text-slate-600 uppercase tracking-wider text-sm">Total Purchase Amount:</td>
                                        <td class="p-4 font-black text-xl text-slate-900 text-right" id="grand-total">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" onclick="addRow()" class="mt-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:border-slate-400 font-bold px-4 py-2 rounded-lg text-sm transition-all flex items-center shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Product Line
                        </button>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end space-x-3">
                        <a href="{{ route('purchases.index') }}">
                            <x-secondary-button>
                                Cancel
                            </x-secondary-button>
                        </a>
                        <x-primary-button>
                            Complete Purchase & Add to Stock
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const products = @json($products);
        let rowIndex = 0;

        function addRow() {
            const tbody = document.getElementById('items-body');
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-50/50 transition-colors duration-150";
            const i = rowIndex++;

            let options = '<option value="" disabled selected>-- Select Product --</option>';
            products.forEach(p => {
                options += `<option value="${p.id}" data-cost="${p.cost_price || 0}" data-stock="${p.stock_quantity || 0}">${p.name}</option>`;
            });

            tr.innerHTML = `
                <td class="p-4">
                    <select name="items[${i}][product_id]" class="block w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm product-select" onchange="updateRow(this)" required>
                        ${options}
                    </select>
                </td>
                <td class="p-4 stock-cell text-sm font-medium text-slate-500 text-center">-</td>
                <td class="p-4">
                    <input type="number" step="0.01" name="items[${i}][cost]" class="block w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm cost-input" value="0.00" min="0" onchange="updateTotal()" oninput="updateTotal()" required>
                </td>
                <td class="p-4">
                    <input type="number" name="items[${i}][quantity]" class="block w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm qty-input" value="1" min="1" onchange="updateTotal()" oninput="updateTotal()" required>
                </td>
                <td class="p-4 subtotal-cell text-right font-bold text-slate-700">$0.00</td>
                <td class="p-4 text-center">
                    <button type="button" onclick="this.closest('tr').remove(); updateTotal();" class="text-slate-400 hover:text-red-600 transition-colors" title="Remove">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function updateRow(select) {
            const tr = select.closest('tr');
            const option = select.options[select.selectedIndex];
            const cost = option.dataset.cost || 0;
            const stock = option.dataset.stock || 0;
            
            tr.querySelector('.stock-cell').textContent = stock;
            tr.querySelector('.cost-input').value = parseFloat(cost).toFixed(2);
            
            updateTotal();
        }

        function updateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('#items-body tr').forEach(tr => {
                const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
                const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
                const subtotal = cost * qty;
                tr.querySelector('.subtotal-cell').textContent = '$' + subtotal.toFixed(2);
                grandTotal += subtotal;
            });
            document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
        }

        // Initialize with one empty row
        addRow();
    </script>
</x-app-layout>