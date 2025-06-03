<div>
    <div class="mb-4 p-4 bg-gray-100 rounded-lg shadow">
        <h3 class="font-semibold text-lg text-gray-700 mb-2">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="filter_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="filter_status" id="filter_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label for="filter_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model.live="filter_category_id" id="filter_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Categories</option>
                    @foreach($serviceCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(!$show_only_mine) {{-- Only show this toggle if it's not implicitly "my requests" page --}}
            <div>
                <label for="show_only_mine_toggle" class="block text-sm font-medium text-gray-700 invisible">Visibility</label> {{-- Invisible label for alignment --}}
                <label for="show_only_mine_toggle" class="flex items-center mt-1">
                    <input type="checkbox" wire:model.live="show_only_mine" id="show_only_mine_toggle" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600">Show only my requests</span>
                </label>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($requests as $request)
                <li wire:key="request-{{ $request->id }}">
                    <a href="{{ route('service-requests.show', $request) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $request->title }}
                                </p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @switch($request->status)
                                            @case('pending') bg-yellow-100 text-yellow-800 @break
                                            @case('assigned') bg-blue-100 text-blue-800 @break
                                            @case('in_progress') bg-purple-100 text-purple-800 @break
                                            @case('completed') bg-green-100 text-green-800 @break
                                            @case('cancelled') bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800 @break
                                        @endswitch">
                                        {{ Str::title(str_replace('_', ' ', $request->status)) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $request->location ?: 'Not specified' }}
                                    </p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                          <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                        </svg>
                                        {{ $request->category->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    <p>
                                        Requested on <time datetime="{{ $request->created_at->toDateString() }}">{{ $request->created_at->format('M d, Y') }}</time>
                                        by {{ $request->requester->name ?? 'Unknown' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li>
                    <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
                        No service requests found matching your criteria.
                    </div>
                </li>
            @endforelse
        </ul>

        @if($requests->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
