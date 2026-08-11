<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('suppliers.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-2xl text-slate-900 leading-tight">
                    Edit Supplier
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" value="Supplier Name *" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $supplier->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Phone Number *" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $supplier->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end space-x-3">
                        <a href="{{ route('suppliers.index') }}">
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