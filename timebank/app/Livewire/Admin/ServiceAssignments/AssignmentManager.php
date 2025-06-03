<?php

namespace App\Livewire\Admin\ServiceAssignments;

use App\Models\ServiceAssignment;
use App\Models\User;
use App\Models\ServiceRequest;
use Livewire\Component;
use Livewire\WithPagination;

class AssignmentManager extends Component
{
    use WithPagination;

    public $filter_status = '';
    public $filter_volunteer_id = '';
    public $filter_request_id = ''; // For filtering by specific service request
    public $search_request_title = '';


    public $volunteers;
    // public $serviceRequests; // Can be too many, consider a different selection method if needed

    protected $queryString = [
        'filter_status' => ['except' => ''],
        'filter_volunteer_id' => ['except' => ''],
        'filter_request_id' => ['except' => ''],
        'search_request_title' => ['except' => ''],
    ];

    public function mount()
    {
        $this->volunteers = User::orderBy('name')->whereHas('assignedServices')->get(); // Users who have been volunteers
        // $this->serviceRequests = ServiceRequest::orderBy('title')->get(); // If needed for a dropdown
    }

    public function updating($property)
    {
        if (in_array($property, ['filter_status', 'filter_volunteer_id', 'filter_request_id', 'search_request_title'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = ServiceAssignment::with([
            'volunteer:id,name',
            'serviceRequest:id,title,requester_id',
            'serviceRequest.requester:id,name'
            ])
            ->when($this->filter_status, function ($q) {
                $q->where('status', $this->filter_status);
            })
            ->when($this->filter_volunteer_id, function ($q) {
                $q->where('volunteer_id', $this->filter_volunteer_id);
            })
            ->when($this->filter_request_id, function ($q) { // Allow filtering by specific request ID
                $q->where('service_request_id', $this->filter_request_id);
            })
            ->when($this->search_request_title, function($q) { // Search by service request title
                $q->whereHas('serviceRequest', function($subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->search_request_title . '%');
                });
            });


        $assignments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.service-assignments.assignment-manager', [
            'assignments' => $assignments,
        ])->layout('layouts.app');
    }
}
