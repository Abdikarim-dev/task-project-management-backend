<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboardRepository,
        private readonly TaskRepositoryInterface $taskRepository
    ) {}

  /**
     * @return array<string, mixed>
     */
    public function getDashboardData(User $user): array
    {
        $statistics = $this->dashboardRepository->getStatisticsForUser($user);
        $statistics['recent_tasks'] = $this->taskRepository
            ->getRecentForUser($user)
            ->all();

        return $statistics;
    }
}
