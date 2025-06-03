<div>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold text-gray-900">Service Categories Management</h1>
                <p class="mt-2 text-sm text-gray-700">Manage service categories available on the platform.</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mt-4 p-3 rounded-md {{ session('error') ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' }}">
                {{ session('message') }}
                 @if (session('error')) {{ session('error') }} @endif
            </div>
        @endif
         @if (session()->has('error'))
            <div class="mt-4 p-3 rounded-md bg-red-100 text-red-700 border border-red-300">
                {{ session('error') }}
            </div>
        @endif


        <form wire:submit.prevent="saveCategory" class="mt-6 sm:flex sm:items-end space-y-2 sm:space-y-0 sm:space-x-3">
            <div class="flex-grow">
                <label for="name" class="block text-sm font-medium text-gray-700">New Category Name</label>
                <input type="text" wire:model.defer="name" id="name" placeholder="Enter category name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Category
                </button>
            </div>
        </form>

        <div class="mt-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..."
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">ID</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($categories as $category)
                                    <tr wire:key="category-row-{{ $category->id }}">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $category->id }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if($editingCategoryId === $category->id)
                                                <input type="text" wire:model.defer="editingCategoryName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                @error('editingCategoryName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            @else
                                                {{ $category->name }}
                                            @endif
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            @if($editingCategoryId === $category->id)
                                                <button wire:click="updateCategory" class="text-indigo-600 hover:text-indigo-900 mr-2">Save</button>
                                                <button wire:click="cancelEdit" class="text-gray-600 hover:text-gray-900">Cancel</button>
                                            @else
                                                <button wire:click="editCategory({{ $category->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                                <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Are you sure you want to delete this category?" class="text-red-600 hover:text-red-900">Delete</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">No service categories found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
