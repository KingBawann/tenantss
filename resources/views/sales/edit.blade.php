<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Sale</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block font-bold mb-1">Total</label>
                        <input type="text" name="total" value="{{ $sale->total }}" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold mb-1">Payment status</label>
                        <input type="text" name="payment_status" value="{{ $sale->payment_status }}" class="border rounded p-2 w-full" required>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('sales.index') }}" class="ml-4 text-gray-500">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>