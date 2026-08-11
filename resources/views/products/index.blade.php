<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                Products
            </h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 border border-transparent rounded-md font-semibold text-sm text-white tracking-widest hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Product
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="border-b border-slate-200 bg-white px-6 py-4 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Inventory</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left font-bold text-slate-500 uppercase tracking-wider w-24">ID</th>
                                <th scope="col" class="px-6 py-3 text-left font-bold text-slate-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left font-bold text-slate-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left font-bold text-slate-500 uppercase tracking-wider">Cost</th>
                                <th scope="col" class="px-6 py-3 text-left font-bold text-slate-500 uppercase tracking-wider">Barcode</th>
                                <th scope="col" class="px-6 py-3 text-right font-bold text-slate-500 uppercase tracking-wider w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($products as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-medium">
                                        #{{ $item->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900">{{ $item->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-emerald-600">${{ number_format((float)$item->price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-500">${{ number_format((float)$item->cost_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded inline-block">{{ $item->barcode ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('products.show', $item->id) }}" class="text-slate-400 hover:text-blue-600 transition-colors" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('products.edit', $item->id) }}" class="text-slate-400 hover:text-emerald-600 transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            <p class="text-base font-medium text-slate-900">No products found</p>
                                            <p class="mt-1 text-sm">Get started by creating a new product.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>