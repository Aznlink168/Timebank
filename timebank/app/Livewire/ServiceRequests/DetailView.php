<?php

namespace App\Livewire\ServiceRequests;

use App\Models\ServiceRequest;
use Livewire\Component;

class DetailView extends Component
{
    public ServiceRequest $serviceRequest;

use Illuminate\Support\Facades\Auth; // Added for Auth

    public function mount(ServiceRequest $serviceRequest)
    {
        // Eager load relationships for efficiency in the view
        $this->serviceRequest = $serviceRequest->load([
            'requester:id,name',
            'category:id,name',
            'assignments.volunteer:id,name' // Ensure 'assignments' itself is loaded
        ]);
    }

    public function getQrCodeForVolunteer(): ?string
    {
        if (!$this->serviceRequest) {
            return null;
        }

        $loggedInUserId = Auth::id();
        $acceptedAssignment = $this->serviceRequest->assignments()
            ->where('volunteer_id', $loggedInUserId)
            ->where('status', 'accepted')
            ->first();

        if ($acceptedAssignment) {
            // Ensure token exists, then get data URL
            $acceptedAssignment->ensureQrCodeTokenExists();
            return $acceptedAssignment->qr_code_data_url;
        }

        return null;
    }

    public function render()
    {
        return view('livewire.service-requests.detail-view', [
            'qrCodeForVolunteer' => $this->getQrCodeForVolunteer()
        ]);
    }
}
