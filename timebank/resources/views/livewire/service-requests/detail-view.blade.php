<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Service Request Details
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Information about the service request.
        </p>
    </div>
    <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Title</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $serviceRequest->title }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Requester</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $serviceRequest->requester->name ?? 'N/A' }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Category</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $serviceRequest->category->name ?? 'N/A' }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $serviceRequest->description }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @switch($serviceRequest->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('assigned') bg-blue-100 text-blue-800 @break
                            @case('in_progress') bg-purple-100 text-purple-800 @break
                            @case('completed') bg-green-100 text-green-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                            @default bg-gray-100 text-gray-800 @break
                        @endswitch">
                        {{ Str::title(str_replace('_', ' ', $serviceRequest->status)) }}
                    </span>
                </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $serviceRequest->location ?: 'Not specified' }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Required Skills</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if(!empty($serviceRequest->required_skills))
                        @foreach($serviceRequest->required_skills as $skill)
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">{{ $skill }}</span>
                        @endforeach
                    @else
                        Not specified
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Urgency</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ Str::title($serviceRequest->urgency) }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Requested Date</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $serviceRequest->created_at->format('M d, Y H:i A') }}</dd>
            </div>
             <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $serviceRequest->updated_at->format('M d, Y H:i A') }}</dd>
            </div>

            {{-- Placeholder for future actions like volunteer assignment, status changes etc. --}}
            {{-- Example: A section for volunteers to offer help, or for requester to cancel/edit if status allows --}}

            @if($qrCodeForVolunteer)
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Assignment QR Code (For Volunteer)</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <p class="mb-2 text-xs text-gray-500">Show this QR code to the service requester to mark the task as 'In Progress' or 'Completed'.</p>
                    <img src="{{ $qrCodeForVolunteer }}" alt="Service Assignment QR Code">
                </dd>
            </div>
            @endif
        </dl>
    </div>
</div>
