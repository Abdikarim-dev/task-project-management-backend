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
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $staffMembers = collect([
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com'],
            ['name' => 'Michael Chen', 'email' => 'michael@example.com'],
            ['name' => 'Emily Rodriguez', 'email' => 'emily@example.com'],
        ])->map(fn (array $staff): User => User::query()->create([
            'name' => $staff['name'],
            'email' => $staff['email'],
            'password' => Hash::make('password'),
            'role' => UserRole::Staff,
            'email_verified_at' => now(),
        ]));

        $allStaffIds = $staffMembers->pluck('id')->all();

        $projects = collect([
            [
                'name' => 'E-Commerce Platform Redesign',
                'client_name' => 'Nova Retail Group',
                'description' => 'Complete redesign of the customer-facing e-commerce platform with improved checkout flow.',
                'start_date' => now()->subMonths(2)->toDateString(),
                'due_date' => now()->addMonths(4)->toDateString(),
                'status' => ProjectStatus::Active,
            ],
            [
                'name' => 'Mobile Banking App',
                'client_name' => 'Summit Financial',
                'description' => 'Native mobile banking application with biometric authentication and real-time notifications.',
                'start_date' => now()->subMonth()->toDateString(),
                'due_date' => now()->addMonths(6)->toDateString(),
                'status' => ProjectStatus::Planning,
            ],
            [
                'name' => 'Healthcare Portal Integration',
                'client_name' => 'MediCare Solutions',
                'description' => 'Integration of patient records portal with existing EMR systems.',
                'start_date' => now()->subMonths(3)->toDateString(),
                'due_date' => now()->addMonth()->toDateString(),
                'status' => ProjectStatus::Active,
            ],
            [
                'name' => 'Internal HR Dashboard',
                'client_name' => 'GlobalTech Industries',
                'description' => 'Employee self-service dashboard for leave requests, payslips, and performance reviews.',
                'start_date' => now()->subMonths(5)->toDateString(),
                'due_date' => now()->subWeek()->toDateString(),
                'status' => ProjectStatus::Completed,
            ],
            [
                'name' => 'Logistics Tracking System',
                'client_name' => 'FastFreight Logistics',
                'description' => 'Real-time shipment tracking system with GPS integration and automated alerts.',
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
            ['title' => 'Gather requirements from stakeholders', 'priority' => TaskPriority::High, 'status' => TaskStatus::Completed],
            ['title' => 'Create wireframes for main screens', 'priority' => TaskPriority::High, 'status' => TaskStatus::InProgress],
            ['title' => 'Set up development environment', 'priority' => TaskPriority::Medium, 'status' => TaskStatus::Completed],
            ['title' => 'Implement user authentication module', 'priority' => TaskPriority::High, 'status' => TaskStatus::InProgress],
            ['title' => 'Design database schema', 'priority' => TaskPriority::High, 'status' => TaskStatus::Completed],
            ['title' => 'Write API documentation', 'priority' => TaskPriority::Low, 'status' => TaskStatus::ToDo],
            ['title' => 'Conduct code review session', 'priority' => TaskPriority::Medium, 'status' => TaskStatus::ToDo],
            ['title' => 'Fix reported UI bugs', 'priority' => TaskPriority::Medium, 'status' => TaskStatus::InProgress],
            ['title' => 'Optimize database queries', 'priority' => TaskPriority::Medium, 'status' => TaskStatus::ToDo],
            ['title' => 'Prepare deployment checklist', 'priority' => TaskPriority::Low, 'status' => TaskStatus::ToDo],
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
                    'title' => $template['title'].' #'.($taskCount + 1),
                    'description' => 'Task for '.$project->name.' assigned to '.$assignee->name.'.',
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
