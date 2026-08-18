<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-xl text-white leading-tight">Bulk Import Products</h2>
            </div>
            <a href="{{ route('products.create') }}" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                + Add single product
            </a>
        </div>
    </x-slot>

    <div class="py-10 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="bg-red-950 border border-red-800/50 text-red-300 px-4 py-3 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Template Download --}}
            <div class="bg-slate-900 rounded-xl border border-white/5 p-6">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Step 1 — Download Template</h3>
                <p class="text-sm text-slate-400 mb-4">Your CSV must have these column headers in the first row. Columns marked <span class="text-red-400 font-bold">*</span> are required.</p>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-xs font-mono">
                        <thead>
                            <tr class="border-b border-white/10">
                                @foreach(['name*', 'barcode', 'category', 'price*', 'cost_price*', 'stock_quantity*', 'reorder_point', 'description'] as $col)
                                <th class="py-2 px-3 text-left text-slate-400 font-bold">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-slate-500">
                                <td class="py-2 px-3">Coca-Cola 500ml</td>
                                <td class="py-2 px-3">5000112637922</td>
                                <td class="py-2 px-3">Beverages</td>
                                <td class="py-2 px-3">1.99</td>
                                <td class="py-2 px-3">1.20</td>
                                <td class="py-2 px-3">100</td>
                                <td class="py-2 px-3">10</td>
                                <td class="py-2 px-3">500ml bottle</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Generate downloadable CSV template --}}
                <div class="mt-4">
                    <a href="data:text/csv;charset=utf-8,name%2Cbarcode%2Ccategory%2Cprice%2Ccost_price%2Cstock_quantity%2Creorder_point%2Cdescription%0ACoca-Cola+500ml%2C5000112637922%2CBeverages%2C1.99%2C1.20%2C100%2C10%2C500ml+bottle" 
                       download="product_import_template.csv"
                       class="inline-flex items-center gap-2 bg-slate-800 border border-white/10 text-slate-200 hover:bg-slate-700 hover:text-white px-4 py-2 rounded-lg text-sm font-bold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download CSV Template
                    </a>
                </div>
            </div>

            {{-- Upload Form --}}
            <div class="bg-slate-900 rounded-xl border border-white/5 p-6"
                 x-data="{ dragging: false, fileName: null }">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Step 2 — Upload Your CSV</h3>

                <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Drag & Drop Zone --}}
                    <div class="relative"
                         @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name; $refs.fileInput.files = $event.dataTransfer.files">
                        <input x-ref="fileInput" type="file" name="csv_file" accept=".csv,.txt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="fileName = $event.target.files[0]?.name">
                        <div class="border-2 border-dashed rounded-xl p-10 text-center transition-all"
                             :class="dragging ? 'border-blue-500 bg-blue-950/30' : 'border-white/10 bg-slate-800/50 hover:border-white/20'">
                            <div x-show="!fileName">
                                <svg class="w-10 h-10 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-bold text-slate-300">Drag & drop your CSV here</p>
                                <p class="text-xs text-slate-500 mt-1">or click to browse — max 10MB</p>
                            </div>
                            <div x-show="fileName" x-cloak>
                                <svg class="w-10 h-10 text-emerald-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-bold text-emerald-400" x-text="fileName"></p>
                                <p class="text-xs text-slate-500 mt-1">Ready to import</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-all shadow-lg shadow-blue-900/30"
                                :disabled="!fileName"
                                :class="!fileName ? 'opacity-50 cursor-not-allowed' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Products
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Callout --}}
            <div class="bg-slate-800/50 border border-white/5 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-xs text-slate-400 space-y-1">
                    <p><span class="font-bold text-slate-300">Duplicate handling:</span> If a product with the same name AND barcode already exists, it will be updated — not duplicated.</p>
                    <p><span class="font-bold text-slate-300">Categories:</span> If the category name doesn't exist yet, it will be created automatically.</p>
                    <p><span class="font-bold text-slate-300">Encoding:</span> Save your spreadsheet as UTF-8 CSV to avoid character issues.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
