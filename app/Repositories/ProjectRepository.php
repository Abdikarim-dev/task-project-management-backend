<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Project::query()
            ->select([
                'id',
                'name',
                'client_name',
                'description',
                'start_date',
                'due_date',
                'status',
                'created_at',
                'updated_at',
            ])
            ->withCount('tasks')
            ->with(['users:id,name,email,role']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Project
    {
        return Project::query()
            ->with(['users:id,name,email,role', 'tasks.assignee:id,name,email'])
            ->find($id);
    }

    public function create(array $data): Project
    {
        return Project::query()->create($data);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project->fresh(['users:id,name,email,role']);
    }

    public function delete(Project $project): bool
    {
        return (bool) $project->delete();
    }

    public function syncTeamMembers(Project $project, array $userIds): void
    {
        $project->users()->sync($userIds);
    }

    public function getRecent(int $limit = 5): \Illuminate\Support\Collection
    {
        return Project::query()
            ->select(['id', 'name', 'client_name', 'status', 'due_date', 'updated_at'])
            ->withCount('tasks')
            ->latest('updated_at')
            ->limit($limit)
            ->get();
    }
}
