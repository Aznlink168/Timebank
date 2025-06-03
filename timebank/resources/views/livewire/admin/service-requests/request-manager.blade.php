<div>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold text-gray-900">Service Requests Management</h1>
                <p class="mt-2 text-sm text-gray-700">View and manage all service requests on the platform.</p>
            </div>
        </div>

        <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow">
            <h3 class="font-semibold text-lg text-gray-700 mb-3">Filters</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Title</label>
                    <input type="text" wire:model.live.debounce.300ms="search" id="search" placeholder="Search by title..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
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
                <div>
                    <label for="filter_requester_id" class="block text-sm font-medium text-gray-700">Requester</label>
                    <select wire:model.live="filter_requester_id" id="filter_requester_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Requesters</option>
                        @foreach($requesters as $requester)
                            <option value="{{ $requester->id }}">{{ $requester->name }} ({{ $requester->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>


        <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">ID</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requester</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Urgency</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created At</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($requests as $request)
                                    <tr wire:key="srequest-row-{{ $request->id }}">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $request->id }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ Str::limit($request->title, 30) }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->requester->name ?? 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->category->name ?? 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($request->status)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('assigned') bg-blue-100 text-blue-800 @break
                                                    @case('in_progress') bg-purple-100 text-purple-800 @break
                                                    @case('completed') bg-green-100 text-green-800 @break
                                                    @case('cancelled') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800 @break
                                                @endswitch">
                                                {{ Str::title(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ Str::title($request->urgency) }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <a href="{{ route('service-requests.show', $request) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View<span class="sr-only">, {{ $request->title }}</span></a>
                                            {{-- Further actions like cancel/edit by admin could go here --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">No service requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
