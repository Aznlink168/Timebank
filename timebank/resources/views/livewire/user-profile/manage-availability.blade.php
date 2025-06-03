<div>
    <form wire:submit.prevent="saveAvailability">
        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
            <h3 class="text-lg font-medium text-gray-900">Manage Regular Availability</h3>
            <p class="mt-1 text-sm text-gray-600">
                Define your general availability for services each week.
            </p>

            @if (session()->has('message'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded-md">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mt-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mt-6 space-y-4">
                @foreach ($slots as $index => $slot)
                    <div wire:key="slot-{{ $index }}" class="flex items-center space-x-3 p-3 border rounded-md">
                        <div class="flex-1">
                            <label for="slots.{{ $index }}.day_of_week" class="block text-sm font-medium text-gray-700">Day</label>
                            <select wire:model="slots.{{ $index }}.day_of_week" id="slots.{{ $index }}.day_of_week" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($daysOfWeek as $dayNum => $dayName)
                                    <option value="{{ $dayNum }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                            @error("slots.{$index}.day_of_week") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label for="slots.{{ $index }}.start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" wire:model="slots.{{ $index }}.start_time" id="slots.{{ $index }}.start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error("slots.{$index}.start_time") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label for="slots.{{ $index }}.end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" wire:model="slots.{{ $index }}.end_time" id="slots.{{ $index }}.end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error("slots.{$index}.end_time") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="pt-5">
                            <button type="button" wire:click="removeSlot({{ $index }})" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="button" wire:click="addSlot" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Another Slot
                </button>
            </div>

        </div>

        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                Save Availability
            </button>
        </div>
    </form>
</div>
