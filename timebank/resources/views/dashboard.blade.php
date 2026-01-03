<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="mt-8 text-2xl font-medium text-gray-900">
                        Welcome to Timebank!
                    </h1>

                    <p class="mt-6 text-gray-500 leading-relaxed">
                        Timebank is a community platform where you can offer your skills and services, or request help from others. Build connections and exchange services based on time rather than money.
                    </p>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
                    <div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 stroke-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                <a href="{{ route('service-requests.index') }}">Browse Service Requests</a>
                            </h2>
                        </div>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Explore requests from community members who need help. Find opportunities to share your skills and earn time credits.
                        </p>

                        <p class="mt-4 text-sm">
                            <a href="{{ route('service-requests.index') }}" class="inline-flex items-center font-semibold text-indigo-700">
                                View All Requests

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 stroke-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                <a href="{{ route('service-requests.create') }}">Create a Service Request</a>
                            </h2>
                        </div>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Need help with something? Create a service request and let the community know what you need. Specify your requirements and urgency level.
                        </p>

                        <p class="mt-4 text-sm">
                            <a href="{{ route('service-requests.create') }}" class="inline-flex items-center font-semibold text-indigo-700">
                                Create Request

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 stroke-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                <a href="{{ route('profile.extended') }}">Manage Your Profile</a>
                            </h2>
                        </div>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Update your skills, availability, and profile information. Let others know what you can offer and when you're available.
                        </p>

                        <p class="mt-4 text-sm">
                            <a href="{{ route('profile.extended') }}" class="inline-flex items-center font-semibold text-indigo-700">
                                Edit Profile

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 stroke-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                <a href="{{ route('user.activity.hub') }}">My Activity Hub</a>
                            </h2>
                        </div>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            View your assigned tasks and track your service requests. Keep up with your commitments and activities in the community.
                        </p>

                        <p class="mt-4 text-sm">
                            <a href="{{ route('user.activity.hub') }}" class="inline-flex items-center font-semibold text-indigo-700">
                                View Activity

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pb-16 md:pb-0"> {{-- Padding for bottom nav on mobile --}}
        {{-- This div is just for padding, content is above --}}
    </div>
    <x-bottom-nav />
</x-app-layout>
