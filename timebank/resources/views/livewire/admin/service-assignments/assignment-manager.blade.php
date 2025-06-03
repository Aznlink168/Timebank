<div>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold text-gray-900">Service Assignments Management</h1>
                <p class="mt-2 text-sm text-gray-700">View and manage all service assignments on the platform.</p>
            </div>
        </div>

        <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow">
            <h3 class="font-semibold text-lg text-gray-700 mb-3">Filters</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search_request_title" class="block text-sm font-medium text-gray-700">Search Request Title</label>
                    <input type="text" wire:model.live.debounce.300ms="search_request_title" id="search_request_title" placeholder="Search by request title..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="filter_status_assignment" class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model.live="filter_status" id="filter_status_assignment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Statuses</option>
                        <option value="pending_acceptance">Pending Acceptance</option>
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="filter_volunteer_id" class="block text-sm font-medium text-gray-700">Volunteer</label>
                    <select wire:model.live="filter_volunteer_id" id="filter_volunteer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Volunteers</option>
                        @foreach($volunteers as $volunteer)
                            <option value="{{ $volunteer->id }}">{{ $volunteer->name }} ({{ $volunteer->email }})</option>
                        @endforeach
                    </select>
                </div>
                 <div>
                    <label for="filter_request_id" class="block text-sm font-medium text-gray-700">Service Request ID</label>
                    <input type="number" wire:model.live.debounce.300ms="filter_request_id" id="filter_request_id" placeholder="Enter Service Request ID"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Service Request (ID)</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Volunteer</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requester</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Assigned At</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">QR Code Set</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($assignments as $assignment)
                                    <tr wire:key="assignment-row-{{ $assignment->id }}">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $assignment->id }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <a href="{{ route('service-requests.show', $assignment->serviceRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ Str::limit($assignment->serviceRequest->title, 30) }} (ID: {{ $assignment->service_request_id }})
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $assignment->volunteer->name ?? 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $assignment->serviceRequest->requester->name ?? 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($assignment->status)
                                                    @case('pending_acceptance') bg-gray-100 text-gray-800 @break
                                                    @case('accepted') bg-blue-100 text-blue-800 @break
                                                    @case('declined') bg-orange-100 text-orange-800 @break
                                                    @case('in_progress') bg-purple-100 text-purple-800 @break
                                                    @case('completed') bg-green-100 text-green-800 @break
                                                    @case('cancelled') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800 @break
                                                @endswitch">
                                                {{ Str::title(str_replace('_', ' ', $assignment->status)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $assignment->qr_code ? 'Yes' : 'No' }}</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            {{-- View details or manage assignment actions --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">No service assignments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
