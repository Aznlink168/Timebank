<?php

namespace App\Livewire\Admin\ServiceCategories;

use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    public $name = '';
    public $search = '';
    public $editingCategoryId = null;
    public $editingCategoryName = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:service_categories,name' . ($this->editingCategoryId ? ',' . $this->editingCategoryId : ''),
            'editingCategoryName' => 'required|string|min:3|max:255|unique:service_categories,name,' . $this->editingCategoryId,
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function saveCategory()
    {
        $this->validateOnly('name');

        ServiceCategory::create(['name' => $this->name]);

        $this->reset('name');
        session()->flash('message', 'Service Category added successfully.');
        $this->dispatch('category-added');
    }

    public function editCategory(ServiceCategory $category)
    {
        $this->editingCategoryId = $category->id;
        $this->editingCategoryName = $category->name;
    }

    public function updateCategory()
    {
        $this->validateOnly('editingCategoryName');

        if ($this->editingCategoryId) {
            $category = ServiceCategory::find($this->editingCategoryId);
            if ($category) {
                $category->update(['name' => $this->editingCategoryName]);
                session()->flash('message', 'Service Category updated successfully.');
            }
        }
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->reset(['editingCategoryId', 'editingCategoryName']);
    }

    public function deleteCategory(ServiceCategory $category)
    {
        // Check if category is in use before deleting
        if ($category->serviceRequests()->count() > 0) {
            session()->flash('error', 'Cannot delete category. It is currently assigned to one or more service requests.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Service Category deleted successfully.');
    }

    public function render()
    {
        $categories = ServiceCategory::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.service-categories.category-list', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
