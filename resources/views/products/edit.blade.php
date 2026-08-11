<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-2xl text-slate-900 leading-tight">
                    Edit Product
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <form action="{{ route('products.update', $product) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="name" value="Product Name *" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- Category --}}
                            <div class="col-span-1 md:col-span-2" x-data="{ addingNew: {{ old('new_category_name') ? 'true' : 'false' }} }">
                                <div class="flex items-center justify-between mb-1">
                                    <x-input-label for="category_id" value="Category" />
                                    <button type="button" @click="addingNew = !addingNew; if(addingNew) { $nextTick(() => $refs.newCat.focus()); document.getElementById('category_id').value=''; } else { document.getElementById('new_category_name').value=''; }" class="text-xs text-blue-600 hover:text-blue-800 font-semibold" x-text="addingNew ? 'Cancel' : '+ Add New Category'"></button>
                                </div>
                                
                                <div x-show="!addingNew">
                                    <select id="category_id" name="category_id" class="block w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm">
                                        <option value="">-- No Category --</option>
                                        @isset($categories)
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div x-show="addingNew" style="display: none;">
                                    <x-text-input x-ref="newCat" id="new_category_name" class="block w-full" type="text" name="new_category_name" :value="old('new_category_name')" placeholder="Enter new category name..." />
                                    <x-input-error :messages="$errors->get('new_category_name')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Price --}}
                            <div>
                                <x-input-label for="price" value="Selling Price ($) *" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price', $product->price)" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            {{-- Cost Price --}}
                            <div>
                                <x-input-label for="cost_price" value="Cost Price ($) *" />
                                <x-text-input id="cost_price" class="block mt-1 w-full" type="number" step="0.01" name="cost_price" :value="old('cost_price', $product->cost_price)" required />
                                <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                            </div>

                            {{-- Barcode --}}
                            <div>
                                <x-input-label for="barcode" value="Barcode" />
                                <x-text-input id="barcode" class="block mt-1 w-full" type="text" name="barcode" :value="old('barcode', $product->barcode)" />
                                <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                            </div>

                            {{-- Current Stock --}}
                            <div>
                                <x-input-label for="stock_quantity" value="Current Stock Quantity *" />
                                <x-text-input id="stock_quantity" class="block mt-1 w-full" type="number" name="stock_quantity" :value="old('stock_quantity', $product->stock_quantity)" required />
                                <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end space-x-3">
                        <a href="{{ route('products.index') }}">
                            <x-secondary-button>
                                Cancel
                            </x-secondary-button>
                        </a>
                        <x-primary-button>
                            Save Changes
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>