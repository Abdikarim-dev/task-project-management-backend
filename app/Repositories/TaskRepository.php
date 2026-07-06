<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = Task::query()
            ->select([
                'id',
                'project_id',
                'assigned_to',
                'title',
                'description',
                'priority',
                'status',
                'due_date',
                'created_at',
                'updated_at',
            ])
            ->with([
                'project:id,name,client_name,status',
                'assignee:id,name,email,role',
            ])
            ->latest();

        if ($user->isStaff()) {
            $query->assignedTo($user->id);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Task
    {
        return Task::query()
            ->with([
                'project:id,name,client_name,status',
                'assignee:id,name,email,role',
            ])
            ->find($id);
    }

    public function create(array $data): Task
    {
        return Task::query()->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh(['project:id,name,client_name,status', 'assignee:id,name,email,role']);
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    public function getRecentForUser(User $user, int $limit = 5): Collection
    {
        $query = Task::query()
            ->select(['id', 'title', 'status', 'priority', 'due_date', 'project_id', 'assigned_to', 'updated_at'])
            ->with(['project:id,name', 'assignee:id,name'])
            ->latest('updated_at');

        if ($user->isStaff()) {
            $query->assignedTo($user->id);
        }

        return $query->limit($limit)->get();
    }
}
