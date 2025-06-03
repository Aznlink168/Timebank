<?php

namespace App\Livewire\ServiceRequests;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RequestList extends Component
{
    use WithPagination; // Enables pagination for the component.

    // Filter properties
    public $filter_status = '';        // Filter by status (e.g., 'pending', 'completed')
    public $filter_category_id = ''; // Filter by service category ID
    public bool $show_only_mine = false; // If true, only shows requests created by the authenticated user

    /** @var \Illuminate\Database\Eloquent\Collection Collection of all service categories for filter dropdown. */
    public $serviceCategories;

    /**
     * Defines which public properties are synced with the URL's query string.
     * 'except' => '' means the parameter is removed from URL if it has its default (empty) value.
     */
    protected $queryString = [
        'filter_status' => ['except' => ''],
        'filter_category_id' => ['except' => ''],
        'show_only_mine' => ['except' => false], // 'false' is the default for show_only_mine
    ];

    /**
     * Component's mount lifecycle hook.
     * Initializes $show_only_mine based on parameter passed during component inclusion.
     * Loads all service categories for the filter dropdown.
     * @param bool $showOnlyMine Determines if the list should initially filter by user's own requests.
     */
    public function mount(bool $showOnlyMine = false)
    {
        $this->show_only_mine = $showOnlyMine;
        $this->serviceCategories = ServiceCategory::orderBy('name')->get();
    }

    /**
     * Livewire lifecycle hook that runs when a public property is being updated.
     * Resets pagination to the first page whenever a filter changes.
     */
    public function updatingFilterStatus()
    {
        $this->resetPage(); // From WithPagination trait
    }

    /**
     * Livewire lifecycle hook. Resets pagination when category filter changes.
     */
    public function updatingFilterCategoryId()
    {
        $this->resetPage();
    }

    /**
     * Livewire lifecycle hook. Resets pagination when 'show_only_mine' toggle changes.
     */
    public function updatingShowOnlyMine()
    {
        $this->resetPage();
    }

    /**
     * Renders the component.
     * Fetches service requests based on current filters and pagination.
     * Eager loads 'requester' and 'category' relationships for display.
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = ServiceRequest::with(['requester:id,name', 'category:id,name']) // Eager load relationships
            ->when($this->show_only_mine, function ($q) { // Apply 'my requests' filter if active
                $q->where('requester_id', Auth::id());
            })
            ->when($this->filter_status, function ($q) { // Apply status filter if set
                $q->where('status', $this->filter_status);
            })
            ->when($this->filter_category_id, function ($q) {
                $q->where('service_category_id', $this->filter_category_id);
            });

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.service-requests.request-list', [
            'requests' => $requests,
        ]);
    }
}
