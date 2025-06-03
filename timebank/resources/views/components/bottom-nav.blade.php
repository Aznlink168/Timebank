<div class="fixed inset-x-0 bottom-0 z-50 bg-white border-t border-gray-200 md:hidden">
    <div class="flex justify-around items-center h-16">
        {{-- Dashboard/Home Link --}}
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center text-center text-gray-600 hover:text-indigo-600 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-xs mt-1">Home</span>
        </a>

        {{-- Create Service Request Link --}}
        <a href="{{ route('service-requests.create') }}" class="flex flex-col items-center justify-center text-center text-gray-600 hover:text-indigo-600 {{ request()->routeIs('service-requests.create') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-xs mt-1">New Request</span>
        </a>

        {{-- My Activity Hub (includes My Tasks & My Requests) --}}
        <a href="{{ route('user.activity.hub') }}" class="flex flex-col items-center justify-center text-center text-gray-600 hover:text-indigo-600 {{ request()->routeIs('user.activity.hub') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <span class="text-xs mt-1">My Activity</span>
        </a>

        {{-- QR Scanner Link --}}
        <a href="{{ route('qr.scanner') }}" class="flex flex-col items-center justify-center text-center text-gray-600 hover:text-indigo-600 {{ request()->routeIs('qr.scanner') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{-- Placeholder icon, ideally a QR specific one --}}
            <span class="text-xs mt-1">Scan QR</span>
        </a>

        {{-- Profile/Menu Link (assuming Jetstream's default profile route) --}}
        <a href="{{ route('profile.show') }}" class="flex flex-col items-center justify-center text-center text-gray-600 hover:text-indigo-600 {{ request()->routeIs('profile.show') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-xs mt-1">Profile</span>
        </a>
    </div>
</div>
