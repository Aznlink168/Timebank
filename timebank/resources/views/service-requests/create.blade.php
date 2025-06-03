<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Service Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    @livewire('service-requests.create-form')
                </div>
            </div>
        </div>
    </div>

    <div class="pb-16 md:pb-0"> {{-- Padding for bottom nav on mobile --}}
        {{-- This div is just for padding, content is above --}}
    </div>
    <x-bottom-nav />
</x-app-layout>
