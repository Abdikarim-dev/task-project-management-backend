<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $projects = $this->projectService->list(
            (int) $request->integer('per_page', 15)
        );

        return ApiResponse::success([
            'items' => ProjectResource::collection($projects->items())->resolve(),
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'last_page' => $projects->lastPage(),
            ],
        ], 'Projects retrieved successfully.');
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $teamMemberIds = $validated['team_member_ids'] ?? [];
        unset($validated['team_member_ids']);

        $project = $this->projectService->create($validated, $teamMemberIds);

        return ApiResponse::success(
            new ProjectResource($project),
            'Project created successfully.',
            201
        );
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $project = $this->projectService->find($project->id);

        if (! $project) {
            return ApiResponse::error('Project not found.', 404);
        }

        return ApiResponse::success(
            new ProjectResource($project),
            'Project retrieved successfully.'
        );
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $validated = $request->validated();
        $teamMemberIds = array_key_exists('team_member_ids', $validated)
            ? ($validated['team_member_ids'] ?? [])
            : null;
        unset($validated['team_member_ids']);

        $project = $this->projectService->update($project, $validated, $teamMemberIds);

        return ApiResponse::success(
            new ProjectResource($project),
            'Project updated successfully.'
        );
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return ApiResponse::success(message: 'Project deleted successfully.');
    }
}
