<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-white leading-tight">
                Products
            </h2>
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                + New Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Filter Row --}}
            <div class="bg-slate-900 border border-white/5 rounded-xl p-4">
                <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or barcode..." class="w-full bg-slate-800 border-white/10 text-slate-100 placeholder-slate-500 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                    </div>
                    @if(isset($categories))
                    <div class="w-full sm:w-64">
                        <select name="category_id" class="w-full bg-slate-800 border-white/10 text-slate-100 rounded-lg focus:border-blue-500 focus:ring-blue-500/20 text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="bg-slate-800 border border-white/10 text-slate-200 hover:bg-slate-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Filter
                    </button>
                    @if(request()->has('search') || request()->has('category_id'))
                        <a href="{{ route('products.index') }}" class="bg-slate-800 border border-white/10 text-slate-200 hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Products Table --}}
            <div class="bg-slate-900 border border-white/5 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-800/50 border-b border-white/5 text-white">
                            <tr>
                                <th class="px-6 py-4 font-medium">ID</th>
                                <th class="px-6 py-4 font-medium">Name</th>
                                <th class="px-6 py-4 font-medium">Category</th>
                                <th class="px-6 py-4 font-medium">Barcode</th>
                                <th class="px-6 py-4 font-medium">Cost</th>
                                <th class="px-6 py-4 font-medium">Price</th>
                                <th class="px-6 py-4 font-medium">Stock</th>
                                <th class="px-6 py-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-slate-300">
                            @forelse($products as $product)
                                <tr class="hover:bg-slate-800 transition-colors">
                                    <td class="px-6 py-4 text-slate-500 font-mono text-xs">#{{ $product->id }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-200">{{ $product->name }}</td>
                                    <td class="px-6 py-4">{{ $product->category ? $product->category->name : '-' }}</td>
                                    <td class="px-6 py-4 font-mono text-slate-400 text-xs">{{ $product->barcode ?: '-' }}</td>
                                    <td class="px-6 py-4">${{ number_format($product->cost_price, 2) }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-200">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($product->stock_quantity > 5)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-900/50 text-emerald-400 border border-emerald-800/50">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        @elseif($product->stock_quantity > 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-900/50 text-amber-400 border border-amber-800/50">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-900/50 text-red-400 border border-red-800/50">
                                                OUT
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('products.show', $product) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 border border-white/10 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 border border-white/10 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p class="text-base">No products found</p>
                                            <a href="{{ route('products.create') }}" class="text-blue-500 hover:text-blue-400 text-sm mt-2">Create your first product</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($products, 'hasPages') && $products->hasPages())
                <div class="bg-slate-900 border border-white/5 rounded-xl p-4">
                    {{ $products->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>