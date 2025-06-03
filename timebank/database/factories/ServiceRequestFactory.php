<?php

namespace Database\Factories;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure related factories exist or handle their creation.
        // For simplicity, if UserFactory and ServiceCategoryFactory are not guaranteed,
        // we might need to create them or use existing records if DB is persistent.
        // Given RefreshDatabase, new ones will be created if factories are defined.

        return [
            'requester_id' => User::factory(), // Assumes UserFactory exists
            'service_category_id' => ServiceCategory::factory(), // Assumes ServiceCategoryFactory exists
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['pending', 'assigned', 'in_progress', 'completed', 'cancelled']),
            'location' => $this->faker->optional()->address,
            // Storing as JSON array of strings for this example. Adjust if skill IDs are integers.
            'required_skills' => $this->faker->randomElements(['gardening', 'plumbing', 'tutoring', 'driving', 'tech support'], rand(0, 3)),
            'urgency' => $this->faker->randomElement(['low', 'medium', 'high']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
