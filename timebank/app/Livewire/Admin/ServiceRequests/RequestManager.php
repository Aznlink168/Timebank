<?php

namespace App\Livewire\Admin\ServiceRequests;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class RequestManager extends Component
{
    use WithPagination;

    public $filter_status = '';
    public $filter_category_id = '';
    public $filter_requester_id = '';
    public $search = ''; // For title search

    public $serviceCategories;
    public $requesters; // For requester filter dropdown

    protected $queryString = [
        'filter_status' => ['except' => ''],
        'filter_category_id' => ['except' => ''],
        'filter_requester_id' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->serviceCategories = ServiceCategory::orderBy('name')->get();
        $this->requesters = User::orderBy('name')->whereHas('serviceRequests')->get(); // Only users who made requests
    }

    public function updating($property)
    {
        if (in_array($property, ['filter_status', 'filter_category_id', 'filter_requester_id', 'search'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = ServiceRequest::with(['requester:id,name', 'category:id,name', 'assignments'])
            ->when($this->filter_status, function ($q) {
                $q->where('status', $this->filter_status);
            })
            ->when($this->filter_category_id, function ($q) {
                $q->where('service_category_id', $this->filter_category_id);
            })
            ->when($this->filter_requester_id, function ($q) {
                $q->where('requester_id', $this->filter_requester_id);
            })
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.service-requests.request-manager', [
            'requests' => $requests,
        ])->layout('layouts.app');
    }
}
