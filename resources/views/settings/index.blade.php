<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-900 leading-tight">Store Settings</h2>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div x-data="{ activeTab: 'general' }" class="flex flex-col md:flex-row gap-8">
                
                {{-- Vertical Tabs Navigation --}}
                <div class="w-full md:w-64 flex-shrink-0">
                    <nav class="flex flex-col space-y-1">
                        <button @click="activeTab = 'general'" :class="{'bg-white shadow-sm text-blue-700 border-blue-500': activeTab === 'general', 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-transparent': activeTab !== 'general'}" class="px-4 py-3 text-sm font-bold rounded-lg border-l-4 text-left transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            General Info
                        </button>
                        <button @click="activeTab = 'receipt'" :class="{'bg-white shadow-sm text-blue-700 border-blue-500': activeTab === 'receipt', 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-transparent': activeTab !== 'receipt'}" class="px-4 py-3 text-sm font-bold rounded-lg border-l-4 text-left transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Receipt Customization
                        </button>
                        <button @click="activeTab = 'tax'" :class="{'bg-white shadow-sm text-blue-700 border-blue-500': activeTab === 'tax', 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-transparent': activeTab !== 'tax'}" class="px-4 py-3 text-sm font-bold rounded-lg border-l-4 text-left transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tax & Currency
                        </button>
                    </nav>
                </div>

                {{-- Forms Container --}}
                <div class="flex-1 bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        
                        {{-- General Info Tab --}}
                        <div x-show="activeTab === 'general'" class="p-6 sm:p-8 space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">General Information</h3>
                                <p class="text-sm text-slate-500 mt-1">Basic details about your business. These will appear on your receipts.</p>
                            </div>
                            
                            <hr class="border-slate-100">

                            <div class="space-y-5">
                                <div>
                                    <label for="store_name" class="block text-sm font-bold text-slate-700 mb-1.5">Store Name</label>
                                    <input type="text" name="settings[store_name]" id="store_name" value="{{ old('settings.store_name', $settings['store_name'] ?? $tenant->name) }}" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors" placeholder="e.g. Acme Supermarket">
                                </div>
                                
                                <div>
                                    <label for="store_phone" class="block text-sm font-bold text-slate-700 mb-1.5">Phone Number</label>
                                    <input type="text" name="settings[store_phone]" id="store_phone" value="{{ old('settings.store_phone', $settings['store_phone'] ?? $tenant->phone) }}" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors" placeholder="(555) 123-4567">
                                </div>

                                <div>
                                    <label for="store_address" class="block text-sm font-bold text-slate-700 mb-1.5">Business Address</label>
                                    <textarea name="settings[store_address]" id="store_address" rows="3" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors" placeholder="123 Main St&#10;City, State 12345">{{ old('settings.store_address', $settings['store_address'] ?? $tenant->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Receipt Tab --}}
                        <div x-show="activeTab === 'receipt'" class="p-6 sm:p-8 space-y-6" style="display: none;">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Receipt Customization</h3>
                                <p class="text-sm text-slate-500 mt-1">Configure how your printed receipts look for customers.</p>
                            </div>
                            
                            <hr class="border-slate-100">

                            <div class="space-y-5">
                                <div>
                                    <label for="receipt_header" class="block text-sm font-bold text-slate-700 mb-1.5">Custom Header Message</label>
                                    <input type="text" name="settings[receipt_header]" id="receipt_header" value="{{ old('settings.receipt_header', $settings['receipt_header'] ?? '') }}" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors" placeholder="Welcome to our store!">
                                    <p class="text-xs text-slate-500 mt-1.5">Appears at the very top of the receipt.</p>
                                </div>

                                <div>
                                    <label for="receipt_footer" class="block text-sm font-bold text-slate-700 mb-1.5">Custom Footer Message</label>
                                    <textarea name="settings[receipt_footer]" id="receipt_footer" rows="2" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors" placeholder="Thank you for your business!&#10;Please come again.">{{ old('settings.receipt_footer', $settings['receipt_footer'] ?? '') }}</textarea>
                                    <p class="text-xs text-slate-500 mt-1.5">Appears at the bottom, after the totals.</p>
                                </div>

                                <div>
                                    <label for="receipt_paper_size" class="block text-sm font-bold text-slate-700 mb-1.5">Printer Paper Size</label>
                                    <select name="settings[receipt_paper_size]" id="receipt_paper_size" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors cursor-pointer">
                                        <option value="80mm" {{ (old('settings.receipt_paper_size', $settings['receipt_paper_size'] ?? '80mm') == '80mm') ? 'selected' : '' }}>80mm Thermal Receipt (Standard)</option>
                                        <option value="a4" {{ (old('settings.receipt_paper_size', $settings['receipt_paper_size'] ?? '') == 'a4') ? 'selected' : '' }}>A4 Standard Page</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Tax & Currency Tab --}}
                        <div x-show="activeTab === 'tax'" class="p-6 sm:p-8 space-y-6" style="display: none;">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Tax & Currency</h3>
                                <p class="text-sm text-slate-500 mt-1">Set your local tax rules and currency formatting.</p>
                            </div>
                            
                            <hr class="border-slate-100">

                            <div class="space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="currency_symbol" class="block text-sm font-bold text-slate-700 mb-1.5">Currency Symbol</label>
                                        <input type="text" name="settings[currency_symbol]" id="currency_symbol" value="{{ old('settings.currency_symbol', $settings['currency_symbol'] ?? '$') }}" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors" placeholder="$">
                                    </div>

                                    <div>
                                        <label for="tax_rate" class="block text-sm font-bold text-slate-700 mb-1.5">Default Tax Rate (%)</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <input type="number" step="0.01" min="0" max="100" name="settings[tax_rate]" id="tax_rate" value="{{ old('settings.tax_rate', $settings['tax_rate'] ?? '0') }}" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring focus:ring-blue-500/20 sm:text-sm font-medium transition-colors pr-8">
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <span class="text-slate-500 sm:text-sm font-bold">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Save Actions --}}
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-bold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
