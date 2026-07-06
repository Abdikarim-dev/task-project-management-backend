<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task && $this->user()?->can('update', $task);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'project_id' => ['sometimes', 'required', 'integer', 'exists:projects,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'priority' => ['sometimes', 'required', Rule::enum(TaskPriority::class)],
            'status' => ['sometimes', 'required', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $assigneeId = $this->input('assigned_to');

            if (! $assigneeId) {
                return;
            }

            $projectId = (int) ($this->input('project_id') ?? $this->route('task')?->project_id);

            if (! $projectId) {
                return;
            }

            if (! $this->isProjectTeamMember($projectId, (int) $assigneeId)) {
                $validator->errors()->add(
                    'assigned_to',
                    'The assignee must be a team member of the selected project.'
                );
            }
        });
    }

    protected function isProjectTeamMember(int $projectId, int $userId): bool
    {
        return DB::table('project_user')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->exists();
    }
}
