<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Task::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $assigneeId = $this->input('assigned_to');
            $projectId = $this->input('project_id');

            if (! $assigneeId || ! $projectId) {
                return;
            }

            if (! $this->isProjectTeamMember((int) $projectId, (int) $assigneeId)) {
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
