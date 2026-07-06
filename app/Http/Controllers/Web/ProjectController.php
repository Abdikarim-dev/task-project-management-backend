<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $projects = $this->projectService->list(
            perPage: (int) $request->integer('per_page', 12),
            filters: $request->only(['search', 'status'])
        );

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create', [
            'staffMembers' => $this->userRepository->getStaffMembers(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $teamMemberIds = $validated['team_member_ids'] ?? [];
        unset($validated['team_member_ids']);

        $this->projectService->create($validated, $teamMemberIds);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project = $this->projectService->find($project->id);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        $project = $this->projectService->find($project->id);

        return view('projects.edit', [
            'project' => $project,
            'staffMembers' => $this->userRepository->getStaffMembers(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();
        $teamMemberIds = array_key_exists('team_member_ids', $validated)
            ? ($validated['team_member_ids'] ?? [])
            : null;
        unset($validated['team_member_ids']);

        $this->projectService->update($project, $validated, $teamMemberIds);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
