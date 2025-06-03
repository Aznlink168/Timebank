<?php

namespace App\Livewire\ServiceRequests;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class CreateForm extends Component
{
    // Form fields
    public $title;
    public $description;
    public $service_category_id;
    public $location;
    public $required_skills_input; // Stores comma-separated skills from text input
    public $urgency;

    /** @var \Illuminate\Database\Eloquent\Collection Collection of all service categories for the dropdown. */
    public $serviceCategories;

    /**
     * Validation rules for the form fields.
     * @return array
     */
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'service_category_id' => 'required|exists:service_categories,id',
            'location' => 'nullable|string|max:255',
            'required_skills_input' => 'nullable|string',
            'urgency' => 'required|in:low,medium,high',
        ];
    }

    /**
     * Component's mount lifecycle hook.
     * Fetches all service categories for the dropdown.
     * Initializes default values for category and urgency.
     */
    public function mount()
    {
        $this->serviceCategories = ServiceCategory::orderBy('name')->get();
        // Set default category if none is selected and categories exist
        if ($this->serviceCategories->isNotEmpty() && !$this->service_category_id) {
            $this->service_category_id = $this->serviceCategories->first()->id;
        }
        // Set default urgency if not already set
        if (!$this->urgency) {
            $this->urgency = 'medium';
        }
    }

    /**
     * Handles the form submission to create a new service request.
     * Validates the input, creates the ServiceRequest record,
     * flashes a success message, and resets the form fields.
     */
    public function createServiceRequest()
    {
        $this->validate(); // Validate form fields based on rules()

        $requiredSkillsArray = [];
        if (!empty($this->required_skills_input)) {
            // Convert comma-separated string of skills into an array
            $requiredSkillsArray = array_map('trim', explode(',', $this->required_skills_input));
            // Note: Further validation or mapping of skill names to skill IDs might be needed here
            // depending on how `required_skills` is intended to be stored and used.
            // The current ServiceRequest model casts 'required_skills' to JSON, so an array is appropriate.
        }

        ServiceRequest::create([ // Create and persist the new ServiceRequest
            'requester_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'service_category_id' => $this->service_category_id,
            'location' => $this->location,
            'required_skills' => $requiredSkillsArray, // Stored as JSON
            'urgency' => $this->urgency,
            'status' => 'pending', // Set a default status for new requests
        ]);

        session()->flash('message', 'Service request created successfully.'); // Flash success message
        $this->resetFormFields(); // Clear the form fields after successful creation
    }

    /**
     * Resets all form fields to their initial/default states.
     */
    public function resetFormFields()
    {
        $this->title = '';
        $this->description = '';
        // Reset category and urgency to defaults
        if ($this->serviceCategories->isNotEmpty()) {
            $this->service_category_id = $this->serviceCategories->first()->id;
        } else {
            $this->service_category_id = null; // Or handle case where no categories exist
        }
        $this->urgency = 'medium';
        $this->location = '';
        $this->required_skills_input = '';
    }

    /**
     * Renders the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.service-requests.create-form');
    }
}
