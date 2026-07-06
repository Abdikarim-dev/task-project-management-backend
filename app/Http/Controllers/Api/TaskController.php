<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->listForUser(
            $request->user(),
            (int) $request->integer('per_page', 15)
        );

        return ApiResponse::success([
            'items' => TaskResource::collection($tasks->items())->resolve(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'last_page' => $tasks->lastPage(),
            ],
        ], 'Tasks retrieved successfully.');
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated());
        $task = $this->taskService->find($task->id);

        return ApiResponse::success(
            new TaskResource($task),
            'Task created successfully.',
            201
        );
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task = $this->taskService->find($task->id);

        if (! $task) {
            return ApiResponse::error('Task not found.', 404);
        }

        return ApiResponse::success(
            new TaskResource($task),
            'Task retrieved successfully.'
        );
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());

        return ApiResponse::success(
            new TaskResource($task),
            'Task updated successfully.'
        );
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateStatus(
            $task,
            $request->validated('status')
        );

        return ApiResponse::success(
            new TaskResource($task),
            'Task status updated successfully.'
        );
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return ApiResponse::success(message: 'Task deleted successfully.');
    }
}
