<?php

namespace App\Livewire\UserProfile;

use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Collection;

class ManageSkills extends Component
{
    public Collection $allSkills; // Collection of all available Skill models
    public array $userSkills = []; // Array of skill IDs currently selected by the user

    /**
     * Mounts the component.
     * Loads all available skills from the database.
     * Loads the authenticated user's currently associated skills and populates $userSkills.
     */
    public function mount()
    {
        $this->allSkills = Skill::orderBy('name')->get();
        // Ensure user's skills are pre-loaded as an array of strings for checkbox binding
        $this->userSkills = Auth::user()->skills->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    /**
     * Saves the user's selected skills.
     * Validates that $userSkills is an array and each ID exists in the 'skills' table.
     * Syncs the user's skills using the Eloquent belongsToMany sync method.
     * Flashes a success message to the session.
     */
    public function saveSkills()
    {
        $this->validate([
            'userSkills' => 'array', // Ensures $userSkills is an array
            'userSkills.*' => 'exists:skills,id', // Ensures each selected skill ID is valid
        ]);

        Auth::user()->skills()->sync($this->userSkills);

        session()->flash('message', 'Skills updated successfully.');
        // Alternative: dispatch a browser event for more complex frontend interactions
        // $this->dispatch('skills-updated');
    }

    /**
     * Renders the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.user-profile.manage-skills');
    }
}
