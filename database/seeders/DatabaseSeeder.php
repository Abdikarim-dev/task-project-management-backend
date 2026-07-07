<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'Hassan Abdi',
            'email' => 'admin@aleelo.org',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'job_title' => 'Platform Administrator',
            'phone' => '+252 61 500 0001',
            'bio' => 'Oversees delivery of digital platforms across Somalia and the Horn of Africa.',
            'email_verified_at' => now(),
        ]);

        $staffMembers = collect([
            [
                'name' => 'Amina Mohamed',
                'email' => 'staff@aleelo.org',
                'job_title' => 'Software Engineer',
                'phone' => '+252 61 500 0002',
                'bio' => 'Builds APIs and dashboards for Mogadishu and Hargeisa clients.',
            ],
            [
                'name' => 'Ibrahim Hashi',
                'email' => 'ibrahim@example.com',
                'job_title' => 'Project Coordinator',
                'phone' => '+252 61 500 0003',
                'bio' => 'Coordinates delivery with port authorities and mobile-money partners.',
            ],
            [
                'name' => 'Khadija Osman',
                'email' => 'khadija@example.com',
                'job_title' => 'Business Analyst',
                'phone' => '+252 61 500 0004',
                'bio' => 'Documents requirements for remittance and logistics programmes.',
            ],
        ])->map(fn (array $staff): User => User::query()->create([
            'name' => $staff['name'],
            'email' => $staff['email'],
            'password' => Hash::make('password'),
            'role' => UserRole::Staff,
            'job_title' => $staff['job_title'],
            'phone' => $staff['phone'],
            'bio' => $staff['bio'],
            'email_verified_at' => now(),
        ]));

        $staffByEmail = $staffMembers->keyBy('email');

        $projectDefinitions = [
            [
                'name' => 'Mogadishu Port Digital Platform',
                'client_name' => 'Mogadishu Port Authority',
                'description' => 'Digitize cargo clearance, berth scheduling, and customs handoffs for the Port of Mogadishu.',
                'start_date' => now()->subMonths(2)->toDateString(),
                'due_date' => now()->addMonths(4)->toDateString(),
                'status' => ProjectStatus::Active,
                'team' => ['staff@aleelo.org', 'ibrahim@example.com', 'khadija@example.com'],
            ],
            [
                'name' => 'Somali Mobile Money Gateway',
                'client_name' => 'Salaam Somali Bank',
                'description' => 'Unified API for EVC Plus, Zaad, and bank wallet transfers across Somalia and Somaliland.',
                'start_date' => now()->subMonth()->toDateString(),
                'due_date' => now()->addMonths(6)->toDateString(),
                'status' => ProjectStatus::Planning,
                'team' => ['staff@aleelo.org', 'khadija@example.com'],
            ],
            [
                'name' => 'Diaspora Remittance Portal',
                'client_name' => 'Dahabshiil',
                'description' => 'Self-service portal for diaspora senders to track remittances to Hargeisa, Mogadishu, and Garowe.',
                'start_date' => now()->subMonths(3)->toDateString(),
                'due_date' => now()->addMonth()->toDateString(),
                'status' => ProjectStatus::Active,
                'team' => ['staff@aleelo.org', 'ibrahim@example.com'],
            ],
            [
                'name' => 'Berbera Corridor Logistics Hub',
                'client_name' => 'Somaliland Trade Commission',
                'description' => 'End-to-end shipment tracking for goods moving through the Berbera port and Ethiopia corridor.',
                'start_date' => now()->subMonths(5)->toDateString(),
                'due_date' => now()->subWeek()->toDateString(),
                'status' => ProjectStatus::Completed,
                'team' => ['ibrahim@example.com', 'khadija@example.com'],
            ],
            [
                'name' => 'National Livestock Export System',
                'client_name' => 'Ministry of Livestock — Federal Republic of Somalia',
                'description' => 'Certification and export documentation workflow for camel and cattle shipments to Gulf markets.',
                'start_date' => now()->subWeeks(2)->toDateString(),
                'due_date' => now()->addMonths(3)->toDateString(),
                'status' => ProjectStatus::OnHold,
                'team' => ['ibrahim@example.com'],
            ],
            [
                'name' => 'Zoobe Shop',
                'client_name' => 'Al Huda',
                'description' => 'E-commerce storefront for Al Huda retail in the Zoobe district of Mogadishu, with mobile-money checkout.',
                'start_date' => now()->addWeeks(2)->toDateString(),
                'due_date' => now()->addMonths(2)->toDateString(),
                'status' => ProjectStatus::Planning,
                'team' => ['staff@aleelo.org', 'khadija@example.com'],
                'seed_tasks' => false,
            ],
        ];

        $projects = collect($projectDefinitions)->map(function (array $definition) use ($staffByEmail): Project {
            $teamEmails = $definition['team'];
            unset($definition['team'], $definition['seed_tasks']);

            $project = Project::query()->create($definition);
            $teamIds = collect($teamEmails)
                ->map(fn (string $email): int => $staffByEmail[$email]->id)
                ->all();
            $project->users()->attach($teamIds);
            $project->setRelation('users', $staffByEmail->only($teamEmails)->values());

            return $project;
        });

        $taskTemplates = [
            [
                'title' => 'Gather requirements from Mogadishu port stakeholders',
                'description' => 'Interview customs officers, stevedores, and shipping agents at the Port of Mogadishu to capture clearance workflows.',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Completed,
            ],
            [
                'title' => 'Integrate Hormuud EVC Plus payment callback',
                'description' => 'Wire up EVC Plus webhook handlers so mobile-money payments confirm in real time for Somali Shilling transactions.',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::InProgress,
            ],
            [
                'title' => 'Configure Somali Shilling (SOS) currency formatting',
                'description' => 'Apply SOS locale rules, thousand separators, and display conventions across invoices and dashboards.',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Completed,
            ],
            [
                'title' => 'Implement bilingual Somali and English UI labels',
                'description' => 'Add Somali (Soomaali) translations for navigation, forms, and status badges alongside English defaults.',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::InProgress,
            ],
            [
                'title' => 'Design Berbera corridor route mapping schema',
                'description' => 'Model waypoints from Berbera port through Wajaale to Ethiopian destinations for logistics tracking.',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Completed,
            ],
            [
                'title' => 'Document Salaam Somali Bank sandbox API',
                'description' => 'Publish integration notes for Salaam Somali Bank test credentials and wallet transfer endpoints.',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::ToDo,
            ],
            [
                'title' => 'Conduct code review with Garowe engineering team',
                'description' => 'Review Puntland-region deployment changes with the Garowe engineering team before production release.',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::ToDo,
            ],
            [
                'title' => 'Fix remittance status sync for Hargeisa branches',
                'description' => 'Resolve delayed status updates for Dahabshiil payout locations in Hargeisa and surrounding districts.',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::InProgress,
            ],
            [
                'title' => 'Optimize livestock export certificate queries',
                'description' => 'Improve database performance for Ministry of Livestock certificate lookups during peak export season.',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::ToDo,
            ],
            [
                'title' => 'Prepare Central Bank of Somalia compliance checklist',
                'description' => 'Compile CBS regulatory requirements for mobile-money aggregation and cross-border remittance reporting.',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::ToDo,
            ],
        ];

        $projectsForTasks = $projects->filter(
            fn (Project $project, int $index): bool => ($projectDefinitions[$index]['seed_tasks'] ?? true) === true
        );

        $taskCount = 0;

        while ($taskCount < 30) {
            foreach ($projectsForTasks as $project) {
                if ($taskCount >= 30) {
                    break;
                }

                $template = $taskTemplates[$taskCount % count($taskTemplates)];
                /** @var Collection<int, User> $team */
                $team = $project->users;
                $assignee = $team->random();

                Task::query()->create([
                    'project_id' => $project->id,
                    'assigned_to' => $assignee->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'priority' => $template['priority'],
                    'status' => $template['status'],
                    'due_date' => match ($template['status']) {
                        TaskStatus::Completed => now()->subDays(rand(1, 14))->toDateString(),
                        TaskStatus::InProgress => now()->addDays(rand(3, 14))->toDateString(),
                        default => rand(0, 1) === 0
                            ? now()->subDays(rand(1, 7))->toDateString()
                            : now()->addDays(rand(7, 30))->toDateString(),
                    },
                ]);

                $taskCount++;
            }
        }

        $admin->projects()->attach($projects->take(2)->pluck('id')->all());
    }
}
