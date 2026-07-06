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
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'Hassan Abdi',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'job_title' => 'Platform Administrator',
            'email_verified_at' => now(),
        ]);

        $staffMembers = collect([
            ['name' => 'Amina Mohamed', 'email' => 'amina@example.com', 'job_title' => 'Software Engineer'],
            ['name' => 'Ibrahim Hashi', 'email' => 'ibrahim@example.com', 'job_title' => 'Project Coordinator'],
            ['name' => 'Khadija Osman', 'email' => 'khadija@example.com', 'job_title' => 'Business Analyst'],
        ])->map(fn (array $staff): User => User::query()->create([
            'name' => $staff['name'],
            'email' => $staff['email'],
            'password' => Hash::make('password'),
            'role' => UserRole::Staff,
            'job_title' => $staff['job_title'],
            'email_verified_at' => now(),
        ]));

        $allStaffIds = $staffMembers->pluck('id')->all();

        $projects = collect([
            [
                'name' => 'Mogadishu Port Digital Platform',
                'client_name' => 'Mogadishu Port Authority',
                'description' => 'Digitize cargo clearance, berth scheduling, and customs handoffs for the Port of Mogadishu.',
                'start_date' => now()->subMonths(2)->toDateString(),
                'due_date' => now()->addMonths(4)->toDateString(),
                'status' => ProjectStatus::Active,
            ],
            [
                'name' => 'Somali Mobile Money Gateway',
                'client_name' => 'Salaam Somali Bank',
                'description' => 'Unified API for EVC Plus, Zaad, and bank wallet transfers across Somalia and Somaliland.',
                'start_date' => now()->subMonth()->toDateString(),
                'due_date' => now()->addMonths(6)->toDateString(),
                'status' => ProjectStatus::Planning,
            ],
            [
                'name' => 'Diaspora Remittance Portal',
                'client_name' => 'Dahabshiil',
                'description' => 'Self-service portal for diaspora senders to track remittances to Hargeisa, Mogadishu, and Garowe.',
                'start_date' => now()->subMonths(3)->toDateString(),
                'due_date' => now()->addMonth()->toDateString(),
                'status' => ProjectStatus::Active,
            ],
            [
                'name' => 'Berbera Corridor Logistics Hub',
                'client_name' => 'Somaliland Trade Commission',
                'description' => 'End-to-end shipment tracking for goods moving through the Berbera port and Ethiopia corridor.',
                'start_date' => now()->subMonths(5)->toDateString(),
                'due_date' => now()->subWeek()->toDateString(),
                'status' => ProjectStatus::Completed,
            ],
            [
                'name' => 'National Livestock Export System',
                'client_name' => 'Ministry of Livestock — Federal Republic of Somalia',
                'description' => 'Certification and export documentation workflow for camel and cattle shipments to Gulf markets.',
                'start_date' => now()->subWeeks(2)->toDateString(),
                'due_date' => now()->addMonths(3)->toDateString(),
                'status' => ProjectStatus::OnHold,
            ],
        ])->map(function (array $projectData) use ($allStaffIds): Project {
            $project = Project::query()->create($projectData);
            $assignedStaff = collect($allStaffIds)->random(rand(1, 3))->all();
            $project->users()->attach($assignedStaff);

            return $project;
        });

        $taskTemplates = [
            [
                'title' => 'Gather requirements from Mogadishu port stakeholders',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Completed,
            ],
            [
                'title' => 'Integrate Hormuud EVC Plus payment callback',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::InProgress,
            ],
            [
                'title' => 'Configure Somali Shilling (SOS) currency formatting',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Completed,
            ],
            [
                'title' => 'Implement bilingual Somali and English UI labels',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::InProgress,
            ],
            [
                'title' => 'Design Berbera corridor route mapping schema',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Completed,
            ],
            [
                'title' => 'Document Salaam Somali Bank sandbox API',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::ToDo,
            ],
            [
                'title' => 'Conduct code review with Garowe engineering team',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::ToDo,
            ],
            [
                'title' => 'Fix remittance status sync for Hargeisa branches',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::InProgress,
            ],
            [
                'title' => 'Optimize livestock export certificate queries',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::ToDo,
            ],
            [
                'title' => 'Prepare Central Bank of Somalia compliance checklist',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::ToDo,
            ],
        ];

        $taskCount = 0;

        while ($taskCount < 30) {
            foreach ($projects as $project) {
                if ($taskCount >= 30) {
                    break;
                }

                $template = $taskTemplates[$taskCount % count($taskTemplates)];
                $assignee = $staffMembers->random();

                Task::query()->create([
                    'project_id' => $project->id,
                    'assigned_to' => $assignee->id,
                    'title' => $template['title'],
                    'description' => sprintf(
                        'Deliverable for %s (%s). Assigned to %s for coordination with the client team.',
                        $project->name,
                        $project->client_name,
                        $assignee->name
                    ),
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

        $admin->projects()->attach($projects->pluck('id')->take(2)->all());
    }
}
