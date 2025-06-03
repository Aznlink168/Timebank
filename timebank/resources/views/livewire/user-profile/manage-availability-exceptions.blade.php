<div>
    <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
        <h3 class="text-lg font-medium text-gray-900">Manage Availability Exceptions</h3>
        <p class="mt-1 text-sm text-gray-600">
            Define specific dates or times when your regular availability changes (e.g., holidays, appointments).
        </p>

        @if (session()->has('message_exceptions'))
            <div class="mt-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded-md">
                {{ session('message_exceptions') }}
            </div>
        @endif
        @if (session()->has('error_exceptions'))
            <div class="mt-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-md">
                {{ session('error_exceptions') }}
            </div>
        @endif

        <form wire:submit.prevent="saveException" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="new_date" class="block text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" wire:model.defer="new_date" id="new_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('new_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="new_is_unavailable" class="flex items-center">
                        <input type="checkbox" wire:model="new_is_unavailable" id="new_is_unavailable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Mark as Unavailable for the whole day</span>
                    </label>
                    @error('new_is_unavailable') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 {{ $new_is_unavailable ? 'hidden' : '' }}">
                 <div>
                    <label for="new_start_time" class="block text-sm font-medium text-gray-700">Start Time (if specifically available)</label>
                    <input type="time" wire:model.defer="new_start_time" id="new_start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('new_start_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_end_time" class="block text-sm font-medium text-gray-700">End Time (if specifically available)</label>
                    <input type="time" wire:model.defer="new_end_time" id="new_end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('new_end_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label for="new_description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                <input type="text" wire:model.defer="new_description" id="new_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g., Doctor's appointment">
                @error('new_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    Add Exception
                </button>
            </div>
        </form>

        <div class="mt-8">
            <h4 class="text-md font-medium text-gray-800">Existing Exceptions</h4>
            @if($exceptions->isEmpty())
                <p class="text-sm text-gray-500 mt-2">You have no availability exceptions scheduled.</p>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach($exceptions as $exception)
                        <li wire:key="exception-{{ $exception->id }}" class="p-3 bg-gray-50 rounded-md shadow-sm flex justify-between items-center">
                            <div>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($exception->date)->format('M d, Y') }}</span>:
                                @if($exception->is_unavailable)
                                    <span class="text-red-600">Unavailable</span>
                                    @if($exception->start_time && $exception->end_time)
                                        (from {{ \Carbon\Carbon::parse($exception->start_time)->format('g:i A') }} to {{ \Carbon\Carbon::parse($exception->end_time)->format('g:i A') }})
                                    @elseif($exception->start_time)
                                        (from {{ \Carbon\Carbon::parse($exception->start_time)->format('g:i A') }})
                                    @else
                                        (all day)
                                    @endif
                                @else
                                    <span class="text-green-600">Available</span>
                                     @if($exception->start_time && $exception->end_time)
                                        from {{ \Carbon\Carbon::parse($exception->start_time)->format('g:i A') }} to {{ \Carbon\Carbon::parse($exception->end_time)->format('g:i A') }}
                                    @else
                                        (specific time not set, check description)
                                    @endif
                                @endif
                                @if($exception->description)
                                    <p class="text-xs text-gray-500 italic">"{{ $exception->description }}"</p>
                                @endif
                            </div>
                            <button type="button" wire:click="deleteException({{ $exception->id }})" wire:confirm="Are you sure you want to delete this exception?" class="text-red-500 hover:text-red-700 ml-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
