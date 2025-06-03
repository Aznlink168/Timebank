<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="mt-8 text-2xl font-medium text-gray-900">
                        Welcome to the Admin Dashboard!
                    </h1>

                    <p class="mt-6 text-gray-500 leading-relaxed">
                        This is the central place to manage users, service categories, skills, and oversee platform activity.
                    </p>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-medium text-gray-900">Management Sections</h2>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">Manage Users</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.skills.index') }}" class="text-indigo-600 hover:text-indigo-900">Manage Skills</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.service-categories.index') }}" class="text-indigo-600 hover:text-indigo-900">Manage Service Categories</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.service-requests.index') }}" class="text-indigo-600 hover:text-indigo-900">Manage Service Requests</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.service-assignments.index') }}" class="text-indigo-600 hover:text-indigo-900">Manage Service Assignments</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="pb-16 md:pb-0"> {{-- Padding for bottom nav on mobile --}}
        {{-- This div is just for padding, content is above --}}
    </div>
    <x-bottom-nav />
</x-app-layout>
