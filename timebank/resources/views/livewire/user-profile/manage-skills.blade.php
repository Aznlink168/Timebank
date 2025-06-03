<div>
    <form wire:submit.prevent="saveSkills">
        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
            <h3 class="text-lg font-medium text-gray-900">Manage Skills</h3>
            <p class="mt-1 text-sm text-gray-600">
                Select the skills you possess or can offer.
            </p>

            @if (session()->has('message'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($allSkills as $skill)
                    <label for="skill-{{ $skill->id }}" class="flex items-center">
                        <input type="checkbox" id="skill-{{ $skill->id }}" value="{{ $skill->id }}" wire:model="userSkills"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">{{ $skill->name }}</span>
                    </label>
                @endforeach
            </div>

             @error('userSkills') <span class="text-red-500 text-sm mt-2">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                Save Skills
            </button>
        </div>
    </form>
</div>
