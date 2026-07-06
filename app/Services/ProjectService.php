<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository
    ) {}

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->paginate($perPage, $filters);
    }

    public function find(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

  /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $teamMemberIds
     */
    public function create(array $data, array $teamMemberIds = []): Project
    {
        return DB::transaction(function () use ($data, $teamMemberIds): Project {
            $project = $this->projectRepository->create($data);

            if ($teamMemberIds !== []) {
                $this->projectRepository->syncTeamMembers($project, $teamMemberIds);
            }

            return $this->projectRepository->findById($project->id);
        });
    }

  /**
     * @param  array<string, mixed>  $data
     * @param  list<int>|null  $teamMemberIds
     */
    public function update(Project $project, array $data, ?array $teamMemberIds = null): Project
    {
        return DB::transaction(function () use ($project, $data, $teamMemberIds): Project {
            $project = $this->projectRepository->update($project, $data);

            if ($teamMemberIds !== null) {
                $this->projectRepository->syncTeamMembers($project, $teamMemberIds);
                $project = $this->projectRepository->findById($project->id);
            }

            return $project;
        });
    }

    public function delete(Project $project): void
    {
        DB::transaction(fn (): bool => $this->projectRepository->delete($project));
    }
}
