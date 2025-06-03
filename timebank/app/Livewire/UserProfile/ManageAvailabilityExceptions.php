<?php

namespace App\Livewire\UserProfile;

use App\Models\AvailabilityException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class ManageAvailabilityExceptions extends Component
{
    public $exceptions;

    // Form properties for adding a new exception
    public $new_date;
    public $new_start_time;
    public $new_end_time;
    public $new_is_unavailable = true;
    public $new_description;

    protected function rules()
    {
        return [
            'new_date' => 'required|date_format:Y-m-d',
            'new_start_time' => 'nullable|required_if:new_is_unavailable,false|date_format:H:i',
            'new_end_time' => 'nullable|required_if:new_is_unavailable,false|date_format:H:i|after_or_equal:new_start_time',
            'new_is_unavailable' => 'required|boolean',
            'new_description' => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        $this->loadExceptions();
    }

    public function loadExceptions()
    {
        $this->exceptions = Auth::user()->availabilityExceptions()->orderBy('date')->orderBy('start_time')->get();
    }

    public function resetForm()
    {
        $this->new_date = null;
        $this->new_start_time = null;
        $this->new_end_time = null;
        $this->new_is_unavailable = true;
        $this->new_description = null;
    }

    public function saveException()
    {
        $this->validate();

        Auth::user()->availabilityExceptions()->create([
            'date' => $this->new_date,
            'start_time' => !empty($this->new_start_time) ? $this->new_start_time : null,
            'end_time' => !empty($this->new_end_time) ? $this->new_end_time : null,
            'is_unavailable' => $this->new_is_unavailable,
            'description' => $this->new_description,
        ]);

        session()->flash('message_exceptions', 'Availability exception saved successfully.');
        $this->resetForm();
        $this->loadExceptions();
    }

    public function deleteException($exceptionId)
    {
        $exception = Auth::user()->availabilityExceptions()->find($exceptionId);
        if ($exception) {
            $exception->delete();
            session()->flash('message_exceptions', 'Availability exception deleted successfully.');
        } else {
            session()->flash('error_exceptions', 'Could not find the exception to delete.');
        }
        $this->loadExceptions();
    }

    public function render()
    {
        return view('livewire.user-profile.manage-availability-exceptions');
    }
}
