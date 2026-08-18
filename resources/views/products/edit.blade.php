<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-slate-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-2xl text-white leading-tight">
                Edit Product
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-900 border border-white/5 sm:rounded-xl shadow-sm overflow-hidden">
                <form action="{{ route('products.update', $product) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Left Column --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-white border-b border-white/5 pb-2">Basic Info</h3>
                            
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-300 mb-1">Product Name *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required autofocus
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Barcode --}}
                            <div>
                                <label for="barcode" class="block text-sm font-medium text-slate-300 mb-1">Barcode</label>
                                <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 font-mono rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('barcode') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Category --}}
                            <div x-data="{ addingNew: {{ old('new_category_name') ? 'true' : 'false' }} }">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="category_id" class="block text-sm font-medium text-slate-300">Category</label>
                                    <button type="button" @click="addingNew = !addingNew; if(addingNew) { $nextTick(() => $refs.newCat.focus()); document.getElementById('category_id').value=''; } else { document.getElementById('new_category_name').value=''; }" class="text-xs text-blue-500 hover:text-blue-400 font-semibold" x-text="addingNew ? 'Cancel' : '+ Add New Category'"></button>
                                </div>
                                
                                <div x-show="!addingNew">
                                    <select id="category_id" name="category_id" class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                        <option value="">-- No Category --</option>
                                        @if(isset($categories))
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('category_id') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div x-show="addingNew" style="display: none;">
                                    <input type="text" x-ref="newCat" id="new_category_name" name="new_category_name" value="{{ old('new_category_name') }}" placeholder="Enter new category name..."
                                        class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                    @error('new_category_name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Supplier --}}
                            <div>
                                <label for="supplier" class="block text-sm font-medium text-slate-300 mb-1">Supplier</label>
                                <input type="text" id="supplier" name="supplier" value="{{ old('supplier', $product->supplier ?? '') }}"
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('supplier') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-white border-b border-white/5 pb-2">Pricing & Inventory</h3>
                            
                            {{-- Price --}}
                            <div>
                                <label for="price" class="block text-sm font-medium text-slate-300 mb-1">Selling Price ($) *</label>
                                <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price) }}" required
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('price') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Cost Price --}}
                            <div>
                                <label for="cost_price" class="block text-sm font-medium text-slate-300 mb-1">Cost Price ($) *</label>
                                <input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('cost_price') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Current Stock --}}
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-slate-300 mb-1">Current Stock Quantity *</label>
                                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('stock_quantity') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Reorder Point --}}
                            <div>
                                <label for="reorder_point" class="block text-sm font-medium text-slate-300 mb-1">Reorder Point</label>
                                <input type="number" id="reorder_point" name="reorder_point" value="{{ old('reorder_point', $product->reorder_point ?? 5) }}"
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                                @error('reorder_point') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Full-width Description --}}
                        <div class="md:col-span-2 mt-4 space-y-6">
                            <h3 class="text-lg font-bold text-white border-b border-white/5 pb-2">Additional Info</h3>
                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-300 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                                @error('description') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 flex justify-end space-x-3">
                        <a href="{{ route('products.index') }}" class="bg-slate-800 border border-white/10 text-slate-200 hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>