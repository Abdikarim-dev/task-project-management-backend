<?php

namespace App\Repositories;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getStatisticsForUser(User $user): array
    {
        if ($user->isAdmin()) {
            return $this->getAdminStatistics();
        }

        return $this->getStaffStatistics($user);
    }

    private function getAdminStatistics(): array
    {
        $projectStats = Project::query()
            ->selectRaw('COUNT(*) as total_projects')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active_projects', [ProjectStatus::Active->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_projects', [ProjectStatus::Completed->value])
            ->first();

        $taskStats = Task::query()
            ->selectRaw('COUNT(*) as total_tasks')
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as active_tasks', [
                TaskStatus::ToDo->value,
                TaskStatus::InProgress->value,
            ])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_tasks', [TaskStatus::Completed->value])
            ->selectRaw('SUM(CASE WHEN due_date < CURDATE() AND status != ? THEN 1 ELSE 0 END) as overdue_tasks', [
                TaskStatus::Completed->value,
            ])
            ->first();

        $tasksByStatus = Task::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        return [
            'total_projects' => (int) $projectStats->total_projects,
            'active_projects' => (int) $projectStats->active_projects,
            'completed_projects' => (int) $projectStats->completed_projects,
            'total_tasks' => (int) $taskStats->total_tasks,
            'active_tasks' => (int) $taskStats->active_tasks,
            'completed_tasks' => (int) $taskStats->completed_tasks,
            'overdue_tasks' => (int) $taskStats->overdue_tasks,
            'tasks_by_status' => $this->normalizeStatusCounts($tasksByStatus),
        ];
    }

    private function getStaffStatistics(User $user): array
    {
        $taskStats = Task::query()
            ->assignedTo($user->id)
            ->selectRaw('COUNT(*) as total_tasks')
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as active_tasks', [
                TaskStatus::ToDo->value,
                TaskStatus::InProgress->value,
            ])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_tasks', [TaskStatus::Completed->value])
            ->selectRaw('SUM(CASE WHEN due_date < CURDATE() AND status != ? THEN 1 ELSE 0 END) as overdue_tasks', [
                TaskStatus::Completed->value,
            ])
            ->first();

        $tasksByStatus = Task::query()
            ->assignedTo($user->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        return [
            'total_projects' => $user->projects()->count(),
            'active_projects' => $user->projects()
                ->where('status', ProjectStatus::Active->value)
                ->count(),
            'completed_projects' => $user->projects()
                ->where('status', ProjectStatus::Completed->value)
                ->count(),
            'total_tasks' => (int) $taskStats->total_tasks,
            'active_tasks' => (int) $taskStats->active_tasks,
            'completed_tasks' => (int) $taskStats->completed_tasks,
            'overdue_tasks' => (int) $taskStats->overdue_tasks,
            'tasks_by_status' => $this->normalizeStatusCounts($tasksByStatus),
        ];
    }

  /**
     * @param  array<string, int>  $counts
     * @return array<string, int>
     */
    private function normalizeStatusCounts(array $counts): array
    {
        $normalized = [];

        foreach (TaskStatus::cases() as $status) {
            $normalized[$status->value] = (int) ($counts[$status->value] ?? 0);
        }

        return $normalized;
    }
}
