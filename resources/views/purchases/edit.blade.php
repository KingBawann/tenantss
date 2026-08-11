<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('purchases.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-2xl text-slate-900 leading-tight">
                    Edit Purchase #{{ $purchase->id }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                
                <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Note:</strong> To protect inventory integrity, line items cannot be edited after a purchase is made. If you need to change items, delete this purchase (which will automatically reverse the stock additions) and create a new one.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        {{-- Supplier --}}
                        <div>
                            <x-input-label for="supplier_id" value="Supplier *" />
                            <select id="supplier_id" name="supplier_id" required class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm">
                                <option value="" disabled>-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>

                        {{-- Date --}}
                        <div>
                            <x-input-label for="date" value="Purchase Date *" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', $purchase->date)" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end space-x-3">
                        <a href="{{ route('purchases.index') }}">
                            <x-secondary-button>
                                Cancel
                            </x-secondary-button>
                        </a>
                        <x-primary-button>
                            Update Purchase Details
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>