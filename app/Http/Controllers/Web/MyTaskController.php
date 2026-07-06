<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyTaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    public function index(Request $request): View
    {
        $tasks = $this->taskService->listForUser(
            user: $request->user(),
            perPage: (int) $request->integer('per_page', 15),
            filters: $request->only(['search', 'status', 'sort'])
        );

        return view('staff.my-tasks', compact('tasks'));
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task = $this->taskService->find($task->id);

        return view('tasks.show', [
            'task' => $task,
            'staffView' => true,
        ]);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        $this->taskService->updateStatus($task, $request->validated('status'));

        return back()->with('success', 'Task status updated successfully.');
    }
}
