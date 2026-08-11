<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">View Purchase</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <strong class="block">Total:</strong>
                    <span>{{ $purchase->total }}</span>
                </div>
                <div class="mb-4">
                    <strong class="block">Date:</strong>
                    <span>{{ $purchase->date }}</span>
                </div>
                <a href="{{ route('purchases.index') }}" class="text-blue-500 underline">Back to List</a>
            </div>
        </div>
    </div>
</x-app-layout>