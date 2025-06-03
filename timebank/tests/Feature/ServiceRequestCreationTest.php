<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCategory;
use App\Livewire\ServiceRequests\CreateForm; // Import the Livewire component
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire; // Import Livewire test utilities
use Tests\TestCase;

class ServiceRequestCreationTest extends TestCase
{
    use RefreshDatabase; // Will attempt to run migrations, likely to fail with driver issue

    public function test_authenticated_user_can_create_service_request()
    {
        // 1. Arrange
        $user = User::factory()->create(); // This will fail if DB isn't working
        $category = ServiceCategory::factory()->create(['name' => 'Gardening']); // This will also fail

        $this->actingAs($user);

        // 2. Act & Assert
        // Test the Livewire component directly
        Livewire::test(CreateForm::class)
            ->set('title', 'Need help with watering plants')
            ->set('description', 'I will be out of town for a week and need someone to water my plants.')
            ->set('service_category_id', $category->id)
            ->set('location', 'My Home Address')
            ->set('required_skills_input', 'plant care, attention to detail')
            ->set('urgency', 'medium')
            ->call('createServiceRequest')
            ->assertHasNoErrors() // Check for validation errors from the component
            ->assertEmitted('saved'); // Or check for session flash message if that's what you used

        // Assert the service request was created in the database
        // This part will fail if the database isn't working or if the component didn't save
        $this->assertDatabaseHas('service_requests', [
            'requester_id' => $user->id,
            'title' => 'Need help with watering plants',
            'service_category_id' => $category->id,
            'status' => 'pending', // Assuming this is the default
            'urgency' => 'medium',
        ]);
    }

    public function test_service_request_requires_title_description_category_urgency()
    {
        $user = User::factory()->create(); // Fails if DB not working
        $this->actingAs($user);

        Livewire::test(CreateForm::class)
            ->call('createServiceRequest')
            ->assertHasErrors(['title', 'description', 'service_category_id', 'urgency']);

        Livewire::test(CreateForm::class)
            ->set('title', 'Valid Title')
            ->set('description', 'This is a valid description that is long enough.')
            ->set('service_category_id', ServiceCategory::factory()->create()->id) // Fails if DB not working
            ->set('urgency', 'high')
            ->call('createServiceRequest')
            ->assertHasNoErrors(['title', 'description', 'service_category_id', 'urgency']);
    }
}
