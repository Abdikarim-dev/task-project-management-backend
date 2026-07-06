<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Project::query()
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
            ->with(['users:id,name,email,role'])
            ->latest()
            ->paginate($perPage);
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
}
