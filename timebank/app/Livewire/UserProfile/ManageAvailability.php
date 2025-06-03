<?php

namespace App\Livewire\UserProfile;

use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ManageAvailability extends Component
{
    /** @var array Array of availability slots. Each slot is an array itself. */
    public $slots = [];

    /** @var array Mapping of day numbers to names for display. */
    public $daysOfWeek = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    /**
     * Component's mount lifecycle hook.
     * Loads existing availability for the user.
     */
    public function mount()
    {
        $this->loadAvailability();
    }

    /**
     * Loads the authenticated user's availability slots from the database
     * and formats them for the $slots array.
     * If no slots exist, initializes with one empty slot.
     */
    protected function loadAvailability()
    {
        $this->slots = Auth::user()->availability() // Uses the 'availability' relationship on User model
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->map(function ($avail) { // Maps each Availability model to an array structure
                return [
                    'id' => $avail->id, // Preserve ID for potential future specific updates (not used in current save)
                    'day_of_week' => $avail->day_of_week,
                    'start_time' => Carbon::parse($avail->start_time)->format('H:i'), // Format time for time input
                    'end_time' => Carbon::parse($avail->end_time)->format('H:i'),
                ];
            })->toArray();

        // If the user has no availability slots, add a default empty one to the form.
        if (empty($this->slots)) {
            $this->addSlot();
        }
    }

    /**
     * Adds a new, empty availability slot to the $slots array for the form.
     * Defaults to Monday.
     */
    public function addSlot()
    {
        $this->slots[] = ['day_of_week' => 1, 'start_time' => '', 'end_time' => '']; // Default to Monday
    }

    /**
     * Removes an availability slot from the $slots array at the given index.
     * Re-indexes the array afterwards.
     * If all slots are removed, adds a new empty one.
     * @param int $index The index of the slot to remove.
     */
    public function removeSlot($index)
    {
        unset($this->slots[$index]);
        $this->slots = array_values($this->slots);
        if (empty($this->slots)) {
            $this->addSlot();
        }
    }

    /**
     * Validates and saves the current set of availability slots.
     * Performs basic overlap validation.
     * Deletes all existing availability for the user and creates new records.
     */
    public function saveAvailability()
    {
        $this->validate([
            'slots.*.day_of_week' => 'required|integer|between:0,6', // Day of week must be valid
            'slots.*.start_time' => 'required|date_format:H:i',      // Time format validation
            'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time', // End time must be after start time
        ]);

        // Basic overlap validation: checks if any two slots for the same day overlap.
        // More sophisticated overlap (e.g. across midnight if allowed) would need more complex logic.
        $processedSlots = new Collection($this->slots);
        foreach ($processedSlots as $currentIndex => $currentSlot) {
            foreach ($processedSlots as $checkIndex => $checkSlot) {
                if ($currentIndex === $checkIndex) continue;

                if ($currentSlot['day_of_week'] == $checkSlot['day_of_week']) {
                    $currentStart = Carbon::parse($currentSlot['start_time']);
                    $currentEnd = Carbon::parse($currentSlot['end_time']);
                    $checkStart = Carbon::parse($checkSlot['start_time']);
                    $checkEnd = Carbon::parse($checkSlot['end_time']);

                    if ($currentStart->lt($checkEnd) && $currentEnd->gt($checkStart)) {
                         session()->flash('error', 'Availability slots overlap for ' . $this->daysOfWeek[$currentSlot['day_of_week']] . '.');
                         return;
                    }
                }
            }
        }

        $user = Auth::user();
        $user->availability()->delete(); // Clear existing availability

        foreach ($this->slots as $slot) {
            if (!empty($slot['start_time']) && !empty($slot['end_time'])) { // Only save if times are set
                Availability::create([
                    'user_id' => $user->id,
                    'day_of_week' => $slot['day_of_week'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]);
            }
        }

        session()->flash('message', 'Regular availability updated successfully.');
        $this->loadAvailability(); // Reload to reflect changes and persisted IDs
    }

    public function render()
    {
        return view('livewire.user-profile.manage-availability');
    }
}
