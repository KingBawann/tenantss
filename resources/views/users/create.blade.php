<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-2xl text-slate-900 leading-tight">
                    Add New Staff
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <form action="{{ route('users.store') }}" method="POST" class="p-8">
                    @csrf
                    
                    <div class="space-y-8">
                        {{-- Basic Info Section --}}
                        <div>
                            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name" value="Full Name" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="email" value="Email Address" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Role & Assignment --}}
                        <div>
                            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Role & Assignment</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="role" value="System Role" />
                                    <select id="role" name="role" required class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm">
                                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="branch_id" value="Assigned Branch (Optional)" />
                                    <select id="branch_id" name="branch_id" class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg shadow-sm transition-all duration-200 ease-in-out text-sm">
                                        <option value="">None / Main Branch</option>
                                        @isset($branches)
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Security --}}
                        <div>
                            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Security</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="password" value="Temporary Password" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" value="Confirm Password" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end space-x-3">
                        <a href="{{ route('users.index') }}">
                            <x-secondary-button>
                                Cancel
                            </x-secondary-button>
                        </a>
                        <x-primary-button>
                            Create Staff Account
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
