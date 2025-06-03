<div>
    <form wire:submit.prevent="createServiceRequest" class="space-y-6">
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model.defer="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="service_category_id" class="block text-sm font-medium text-gray-700">Service Category</label>
            <select wire:model.defer="service_category_id" id="service_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @if($serviceCategories)
                    @foreach($serviceCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                @else
                    <option value="">Loading categories...</option>
                @endif
            </select>
            @error('service_category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">Location (Optional)</label>
            <input type="text" wire:model.defer="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="required_skills_input" class="block text-sm font-medium text-gray-700">Required Skills (Comma-separated, optional)</label>
            <input type="text" wire:model.defer="required_skills_input" id="required_skills_input" placeholder="e.g., plumbing, gardening, coding" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('required_skills_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency</label>
            <select wire:model.defer="urgency" id="urgency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
            @error('urgency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Service Request
            </button>
        </div>
    </form>
</div>
