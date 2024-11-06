<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-purple-100 to-pink-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gradient-to-br from-purple-50/30 to-pink-50/30 border-b border-gray-200">
                    <!-- Profile Information Section -->
                    <div class="mb-8">
                        <div class="flex items-center">
                            <div class="relative">
                                <img src="{{ $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) : asset('images/default-avatar.png') }}" 
                                     alt="Profile Picture" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                            </div>
                            <div class="ml-6">
                                <h3 class="text-2xl font-bold text-gray-900">
                                    <span class="text-purple-600">{{ $user->name }}</span>
                                </h3>
                                <p class="text-gray-600">
                                    <span class="text-pink-500">{{ $user->email }}</span>
                                </p>
                                @if($user->bio)
                                    <p class="mt-2 text-gray-700 italic">
                                        "{{ $user->bio }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="flex justify-end">
                            <a href="{{ route('profile.edit') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 active:from-purple-800 active:to-pink-700 focus:outline-none focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Edit Profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
