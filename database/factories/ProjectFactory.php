<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'name' => fake()->catchPhrase().' Project',
            'client_name' => fake()->company(),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'status' => fake()->randomElement(ProjectStatus::cases()),
        ];
    }
}
