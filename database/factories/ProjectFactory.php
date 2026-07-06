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

    /** @var list<array{name: string, client_name: string}> */
    private static array $somaliProjects = [
        ['name' => 'Mogadishu Port Digital Platform', 'client_name' => 'Mogadishu Port Authority'],
        ['name' => 'Somali Mobile Money Gateway', 'client_name' => 'Salaam Somali Bank'],
        ['name' => 'Diaspora Remittance Portal', 'client_name' => 'Dahabshiil'],
        ['name' => 'Berbera Corridor Logistics Hub', 'client_name' => 'Somaliland Trade Commission'],
        ['name' => 'Hargeisa Smart City Dashboard', 'client_name' => 'Hargeisa Municipality'],
        ['name' => 'National Livestock Export System', 'client_name' => 'Ministry of Livestock — FRS'],
        ['name' => 'Garowe Education Management System', 'client_name' => 'Puntland Ministry of Education'],
        ['name' => 'Kismayo Fisheries Tracking Platform', 'client_name' => 'Jubaland Fisheries Authority'],
    ];

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = fake()->dateTimeBetween($startDate, '+6 months');
        $project = fake()->randomElement(self::$somaliProjects);

        return [
            'name' => $project['name'],
            'client_name' => $project['client_name'],
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'status' => fake()->randomElement(ProjectStatus::cases()),
        ];
    }
}
