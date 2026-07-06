<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository
    ) {}

    public function listForUser(User $user, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->paginateForUser($user, $perPage, $filters);
    }

    public function find(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

  /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Task
    {
        return DB::transaction(fn (): Task => $this->taskRepository->create($data));
    }

  /**
     * @param  array<string, mixed>  $data
     */
    public function update(Task $task, array $data): Task
    {
        return DB::transaction(fn (): Task => $this->taskRepository->update($task, $data));
    }

    public function updateStatus(Task $task, TaskStatus|string $status): Task
    {
        $statusValue = $status instanceof TaskStatus ? $status->value : $status;

        return DB::transaction(fn (): Task => $this->taskRepository->update($task, [
            'status' => $statusValue,
        ]));
    }

    public function delete(Task $task): void
    {
        DB::transaction(fn (): bool => $this->taskRepository->delete($task));
    }
}
