<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
// App\Models\User is already imported (implicitly or explicitly by an earlier use statement)
use App\Mail\NewAssignmentNotification; // Added
use Illuminate\Support\Str; // Added for Str::limit
// NotificationService is already imported via App\Services\NotificationService below

class MatchingService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Finds potential volunteer matches for a given service request in phases.
     *
     * @param ServiceRequest $serviceRequest The service request to find matches for.
     * @return array An array containing collections of User models, keyed by phase number.
     *               Example: [ 1 => Collection<User>, 2 => Collection<User>, ... ]
     */
    public function findMatches(ServiceRequest $serviceRequest): array
    {
        $requiredSkillIds = $serviceRequest->required_skills ?? []; // Assumes array of skill IDs

        // Base query: Users who are not the requester and not already assigned to this specific request.
        $query = User::query()
            ->where('id', '!=', $serviceRequest->requester_id) // Exclude requester
            ->whereDoesntHave('assignedServices', function (Builder $q) use ($serviceRequest) {
                $q->where('service_request_id', $serviceRequest->id); // Exclude already assigned
            });

        // Filter by skills - ensures users have ALL specified skills.
        if (!empty($requiredSkillIds)) {
            $query = $this->filterBySkills($query, $requiredSkillIds);
        }

        // Filter by availability - based on simplified logic (day of week and no major unavailability).
        $query = $this->filterByAvailability($query, $serviceRequest);

        // Eager load skills and availability to prevent N+1 queries if these are accessed later for ranking (not currently used for ranking).
        // $allPotentialVolunteers = $query->with(['skills', 'availability', 'availabilityExceptions'])->get();
        $allPotentialVolunteers = $query->get();

        // Phased selection logic:
        // Phase 1: Top N users.
        // Phase 2: Next M users.
        // Phase 3: Subsequent K users.
        // This ensures distinct users across phases.
        $phase1Limit = 3;
        $phase2Limit = 5;
        $phase3Limit = 20;

        $phase1Volunteers = $allPotentialVolunteers->slice(0, $phase1Limit);
        $remainingAfterPhase1 = $allPotentialVolunteers->slice($phase1Limit);

        $phase2Volunteers = $remainingAfterPhase1->slice(0, $phase2Limit);
        $remainingAfterPhase2 = $remainingAfterPhase1->slice($phase2Limit);

        $phase3Volunteers = $remainingAfterPhase2->slice(0, $phase3Limit);

        // Notify Phase 1 volunteers (example of triggering notification)
        foreach ($phase1Volunteers as $volunteer) {
            $mailable = new NewAssignmentNotification($volunteer, $serviceRequest);
            // Using Str::limit for SMS message conciseness.
            $smsMessage = "You've been matched with a new service request on TimeBank: " . Str::limit($serviceRequest->title, 50);

            $this->notificationService->notifyUser(
                $volunteer,
                'new_assignment',
                'New Service Request Assignment: ' . $serviceRequest->title,
                $mailable,
                $smsMessage,
                ['service_request_id' => $serviceRequest->id]
            );
        }

        return [
            1 => $phase1Volunteers,
            2 => $phase2Volunteers,
            3 => $phase3Volunteers,
        ];
    }

    private function filterBySkills(Builder $query, array $requiredSkillIds): Builder
    {
        // User must have all required skills
        foreach ($requiredSkillIds as $skillId) {
            $query->whereHas('skills', function (Builder $q) use ($skillId) {
                $q->where('skills.id', $skillId);
            });
        }
        // Alternative for "has all": check count if relationship is clean
        // $query->whereHas('skills', function (Builder $q) use ($requiredSkillIds) {
        //     $q->whereIn('skills.id', $requiredSkillIds);
        // }, '=', count($requiredSkillIds));
        return $query;
    }

    /**
     * Filters users based on their availability for a given service request.
     * This is a simplified version. A more advanced version would handle specific time overlaps,
     * recurring availabilities, and exceptions that make a user specifically available.
     *
     * @param Builder $query The Eloquent query builder for Users.
     * @param ServiceRequest $serviceRequest The service request with details like potential date/time.
     * @return Builder The modified query builder.
     */
    private function filterByAvailability(Builder $query, ServiceRequest $serviceRequest): Builder
    {
        // Simplified v1:
        // Uses service request's creation date's day of the week as a proxy.
        // A more robust solution would use a dedicated 'request_date' or 'preferred_time_window' on ServiceRequest.
        $requestDate = $serviceRequest->created_at;
        // Example if a specific request date field existed:
        // $requestDate = $serviceRequest->service_date ? Carbon::parse($serviceRequest->service_date) : $serviceRequest->created_at;

        $dayOfWeek = $requestDate->dayOfWeek; // Carbon: 0 for Sunday, 6 for Saturday

        $query->where(function (Builder $availabilityQuery) use ($dayOfWeek, $requestDate) {
            // Check for regular availability on that day of the week
            $availabilityQuery->whereHas('availability', function (Builder $q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek);
                // TODO: Add time overlap checks if request has specific start/end times.
            });
            // TODO: OR, check if an AvailabilityException makes them specifically available on this date/time.
            // This part is complex as it requires checking for `is_unavailable = false` and time overlaps.
        })
        // And ensure they are not specifically unavailable for the entire day
        ->whereDoesntHave('availabilityExceptions', function (Builder $q) use ($requestDate) {
            $q->where('date', $requestDate->toDateString())
              ->where('is_unavailable', true)
              // TODO: Add checks for partial day unavailability if request has specific times.
              // For now, if is_unavailable is true for the date, they are excluded.
              ->where(function($subQ) { // Covers full day unavailability or specific unavailability period
                  $subQ->whereNull('start_time')
                       ->orWhereNotNull('start_time'); // This logic needs refinement for partial day
              });
        });

        return $query;
    }
}
