<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface DashboardRepositoryInterface
{
    public function getStatisticsForUser(User $user): array;
}
