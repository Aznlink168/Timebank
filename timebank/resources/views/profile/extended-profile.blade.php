<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Extended Profile Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @livewire('user-profile.manage-skills')
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @livewire('user-profile.manage-availability')
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @livewire('user-profile.manage-availability-exceptions')
            </div>
        </div>
    </div>

    <div class="pb-16 md:pb-0"> {{-- Padding for bottom nav on mobile --}}
        {{-- This div is just for padding, content is above --}}
    </div>
    <x-bottom-nav />
</x-app-layout>
