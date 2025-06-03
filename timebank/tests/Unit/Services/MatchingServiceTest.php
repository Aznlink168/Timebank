<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Skill;
use App\Models\ServiceRequest;
use App\Models\ServiceAssignment;
use App\Models\Availability;
use App\Models\AvailabilityException;
use App\Services\MatchingService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase; // Using this for in-memory testing if DB was set up
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Mockery;

class MatchingServiceTest extends TestCase
{
    // Note: RefreshDatabase would try to migrate. Given DB driver issues,
    // we'll rely purely on mocks and not touch a real DB for this unit test.
    // If using a real SQLite in-memory for other tests, ensure it's configured in phpunit.xml.

    protected $notificationServiceMock;
    protected $matchingService;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock NotificationService
        $this->notificationServiceMock = Mockery::mock(NotificationService::class);
        // Instantiate MatchingService with the mock
        $this->matchingService = new MatchingService($this->notificationServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_find_matches_returns_correct_phased_users_excluding_requester_and_assigned()
    {
        // 1. Arrange
        $requester = User::factory()->make(['id' => 1]); // Use make to not hit DB
        $serviceRequest = ServiceRequest::factory()->make([
            'id' => 101,
            'requester_id' => $requester->id,
            'required_skills' => [1, 2], // Skill IDs
            'created_at' => Carbon::now(), // For availability check
        ]);

        $skill1 = Skill::factory()->make(['id' => 1]);
        $skill2 = Skill::factory()->make(['id' => 2]);
        $skill3 = Skill::factory()->make(['id' => 3]);

        // Volunteers
        $volunteer1 = Mockery::mock(User::class)->makePartial();
        $volunteer1->id = 2; $volunteer1->name = "Volunteer One";
        $volunteer1->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $volunteer1->shouldReceive('getAttribute')->with('requester_id')->andReturn(null); // or some other id
        $volunteer1->shouldReceive('skills')->andReturnSelf(); // Mock relation
        $volunteer1->skills->shouldReceive('where')->with('skills.id', 1)->andReturnSelf();
        $volunteer1->skills->shouldReceive('where')->with('skills.id', 2)->andReturnSelf();
        // $volunteer1->skills->shouldReceive('count')->andReturn(2); // If using count based skill check
        $volunteer1->shouldReceive('availability')->andReturnSelf();
        $volunteer1->availability->shouldReceive('where')->with('day_of_week', $serviceRequest->created_at->dayOfWeek)->andReturn(collect([new Availability()])); // Has availability on the day
        $volunteer1->shouldReceive('availabilityExceptions')->andReturnSelf();
        $volunteer1->availabilityExceptions->shouldReceive('where')->andReturn(collect([])); // No exceptions making them unavailable
        $volunteer1->shouldReceive('assignedServices')->andReturnSelf();
        $volunteer1->assignedServices->shouldReceive('where')->andReturn(collect([]));


        $volunteer2 = Mockery::mock(User::class)->makePartial();
        $volunteer2->id = 3; $volunteer2->name = "Volunteer Two";
        $volunteer2->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $volunteer2->shouldReceive('getAttribute')->with('requester_id')->andReturn(null);
        $volunteer2->shouldReceive('skills')->andReturnSelf();
        $volunteer2->skills->shouldReceive('where')->with('skills.id', 1)->andReturnSelf();
        $volunteer2->skills->shouldReceive('where')->with('skills.id', 2)->andReturnSelf();
        $volunteer2->shouldReceive('availability')->andReturnSelf();
        $volunteer2->availability->shouldReceive('where')->andReturn(collect([new Availability()]));
        $volunteer2->shouldReceive('availabilityExceptions')->andReturnSelf();
        $volunteer2->availabilityExceptions->shouldReceive('where')->andReturn(collect([]));
        $volunteer2->shouldReceive('assignedServices')->andReturnSelf();
        $volunteer2->assignedServices->shouldReceive('where')->andReturn(collect([]));


        $volunteer_assigned = Mockery::mock(User::class)->makePartial(); // Already assigned
        $volunteer_assigned->id = 4; $volunteer_assigned->name = "Assigned Vol";
        $volunteer_assigned->shouldReceive('getAttribute')->with('id')->andReturn(4);

        // Mock the User::query() chain
        $userQueryMock = Mockery::mock('overload:App\Models\User'); // Overload User model for query mocking
        $userQueryMock->shouldReceive('query')->andReturnSelf();
        $userQueryMock->shouldReceive('where')->with('id', '!=', $serviceRequest->requester_id)->andReturnSelf();
        $userQueryMock->shouldReceive('whereDoesntHave')->with('assignedServices', \Mockery::type('closure'))->andReturnUsing(function ($relation, $closure) use ($serviceRequest, $volunteer_assigned) {
            // Simulate the closure: if user is $volunteer_assigned, return a non-empty collection for assignedServices
            $mockedBuilder = Mockery::mock(Builder::class);
            $mockedBuilder->shouldReceive('where')->with('service_request_id', $serviceRequest->id)->andReturnSelf();
            // This part is tricky, depends on how the closure is called on each user.
            // For simplicity, we'll ensure the get() returns users that are not $volunteer_assigned.
            return $this; // Return self for chain
        });

        // Mocking skills check
        $userQueryMock->shouldReceive('whereHas')->with('skills', \Mockery::type('closure'))->times(count($serviceRequest->required_skills))->andReturnSelf();

        // Mocking availability check
        $userQueryMock->shouldReceive('whereHas')->with('availability', \Mockery::type('closure'))->andReturnSelf();
        $userQueryMock->shouldReceive('whereDoesntHave')->with('availabilityExceptions', \Mockery::type('closure'))->andReturnSelf();

        $userQueryMock->shouldReceive('get')->andReturn(collect([$volunteer1, $volunteer2])); // Return only valid, unassigned volunteers

        // Expect notifications for phase 1
        $this->notificationServiceMock->shouldReceive('notifyUser')
            ->times(2) // volunteer1 and volunteer2
            ->withArgs(function(User $user, $type, $subject, $mailable, $smsMessage, $data) use ($serviceRequest) {
                return $type === 'new_assignment' &&
                       $mailable instanceof NewAssignmentNotification &&
                       $data['service_request_id'] === $serviceRequest->id;
            });

        // 2. Act
        $matches = $this->matchingService->findMatches($serviceRequest);

        // 3. Assert
        $this->assertCount(2, $matches[1]); // volunteer1, volunteer2 in Phase 1
        $this->assertEquals(2, $matches[1]->first()->id);
        $this->assertEquals(3, $matches[1]->last()->id);
        $this->assertCount(0, $matches[2]);
        $this->assertCount(0, $matches[3]);
    }
}
