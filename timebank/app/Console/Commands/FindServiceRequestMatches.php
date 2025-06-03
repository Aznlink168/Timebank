<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServiceRequest;
use App\Services\MatchingService;

class FindServiceRequestMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timebank:find-matches {service_request_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds and displays potential volunteer matches for a given service request ID, grouped by phase.';

    /**
     * Execute the console command.
     */
    public function handle(MatchingService $matchingService)
    {
        $serviceRequestId = $this->argument('service_request_id');
        $serviceRequest = ServiceRequest::find($serviceRequestId);

        if (!$serviceRequest) {
            $this->error("ServiceRequest with ID {$serviceRequestId} not found.");
            return 1;
        }

        $this->info("Finding matches for Service Request: {$serviceRequest->title} (ID: {$serviceRequest->id})");

        // Assuming required_skills is an array of skill IDs.
        // If it's skill names, you'd need to convert names to IDs first.
        // For this command, we'll trust the MatchingService handles the structure of required_skills.
        if (is_string($serviceRequest->required_skills)) {
            // Attempt to decode if it's a JSON string (e.g. from older data or manual input)
            $skillsArray = json_decode($serviceRequest->required_skills, true);
            if (json_last_error() !== JSON_ERROR_NONE && !is_array($skillsArray)) {
                 $this->warn("Warning: required_skills field is a string but not valid JSON. Treating as empty. Content: " . $serviceRequest->required_skills);
                 $serviceRequest->required_skills = []; // Reset to empty array if not valid JSON
            } else {
                $serviceRequest->required_skills = $skillsArray;
            }
        } elseif (!is_array($serviceRequest->required_skills) && !is_null($serviceRequest->required_skills)) {
            $this->warn("Warning: required_skills field is neither an array nor null. Treating as empty.");
            $serviceRequest->required_skills = [];
        }


        $matchesByPhase = $matchingService->findMatches($serviceRequest);

        if (empty($matchesByPhase[1]) && empty($matchesByPhase[2]) && empty($matchesByPhase[3])) {
            $this->info("No potential volunteers found for this service request.");
            return 0;
        }

        foreach ($matchesByPhase as $phase => $volunteers) {
            $this->line("\n--- Phase {$phase} ---");
            if ($volunteers->isEmpty()) {
                $this->info("No volunteers found for this phase.");
                continue;
            }
            $this->info("Found " . $volunteers->count() . " volunteer(s):");
            foreach ($volunteers as $volunteer) {
                $this->line("- User ID: {$volunteer->id}, Name: {$volunteer->name}, Email: {$volunteer->email}");
                // You could add more details like skills or availability summary if needed for debugging
            }
        }

        return 0;
    }
}
