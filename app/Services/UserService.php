<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage, $filters);
    }

    public function find(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function getDetail(int $id): ?array
    {
        $user = $this->userRepository->findWithRelations($id);

        if (! $user) {
            return null;
        }

        $tasksByStatus = [];
        foreach (TaskStatus::cases() as $status) {
            $tasksByStatus[$status->value] = $user->tasks
                ->where('status', $status)
                ->count();
        }

        $projectsByStatus = [];
        foreach (ProjectStatus::cases() as $status) {
            $projectsByStatus[$status->value] = $user->projects
                ->where('status', $status)
                ->count();
        }

        return [
            'user' => $user,
            'projects' => $user->projects,
            'tasks_by_status' => $tasksByStatus,
            'projects_by_status' => $projectsByStatus,
            'tasks_count' => $user->tasks->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepository->update($user, $data);
    }

    public function suspend(User $user, bool $suspended = true): User
    {
        return $this->userRepository->update($user, ['is_suspended' => $suspended]);
    }
}
