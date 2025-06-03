<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scan QR Code') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('qr-scanner-page')
        </div>
    </div>

    <div class="pb-16 md:pb-0"> {{-- Padding for bottom nav on mobile --}}
        {{-- This div is just for padding, content is above --}}
    </div>
    <x-bottom-nav />
</x-app-layout>
