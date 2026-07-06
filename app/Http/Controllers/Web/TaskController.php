<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->isStaff()) {
            return redirect()->route('my-tasks.index');
        }

        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->listForUser(
            user: $request->user(),
            perPage: (int) $request->integer('per_page', 15),
            filters: $request->only(['search', 'status', 'priority', 'sort'])
        );

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'projects' => Project::query()->orderBy('name')->get(['id', 'name']),
            'staffMembers' => $this->userRepository->getStaffMembers(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $this->taskService->create($request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task = $this->taskService->find($task->id);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        $task = $this->taskService->find($task->id);

        return view('tasks.edit', [
            'task' => $task,
            'projects' => Project::query()->orderBy('name')->get(['id', 'name']),
            'staffMembers' => $this->userRepository->getStaffMembers(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->taskService->update($task, $request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        $this->taskService->updateStatus($task, $request->validated('status'));

        return back()->with('success', 'Task status updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
