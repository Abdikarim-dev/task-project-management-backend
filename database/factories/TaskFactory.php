<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /** @var list<string> */
    private static array $somaliTaskTitles = [
        'Integrate Hormuud EVC Plus payment callback',
        'Configure Somali Shilling (SOS) currency formatting',
        'Map Berbera corridor trade routes in the system',
        'Onboard Salaam Somali Bank API sandbox credentials',
        'Translate dashboard labels to Somali language',
        'Review Central Bank of Somalia compliance requirements',
        'Sync remittance status for Hargeisa branch network',
        'Prepare livestock export certificate workflow',
        'Conduct user testing with Mogadishu port staff',
        'Deploy staging environment in Nairobi DR region',
    ];

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'assigned_to' => User::factory(),
            'title' => fake()->randomElement(self::$somaliTaskTitles),
            'description' => fake()->randomElement([
                'Coordinate with the client team in Mogadishu on delivery milestones.',
                'Validate integration against Somali mobile-money sandbox credentials.',
                'Ensure compliance with Central Bank of Somalia reporting requirements.',
                'Support bilingual Somali and English content across the platform.',
            ]),
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'due_date' => fake()->dateTimeBetween('now', '+3 months'),
        ];
    }
}
