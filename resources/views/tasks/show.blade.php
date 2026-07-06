@php
    $staffView = $staffView ?? false;
    $backRoute = $staffView ? route('my-tasks.index') : route('tasks.index');
    $statusRoute = $staffView ? route('my-tasks.update-status', $task) : route('tasks.update-status', $task);
@endphp

<x-layouts.app :title="$task->title">
    <x-ui.page-header :title="$task->title" :description="$task->project?->name">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => $staffView ? 'My Tasks' : 'Tasks', 'url' => $backRoute],
                ['label' => $task->title],
            ]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            @unless($staffView)
                @can('update', $task)
                    <x-ui.button href="{{ route('tasks.edit', $task) }}" variant="secondary">Edit Task</x-ui.button>
                @endcan
                @can('delete', $task)
                    <x-ui.button type="button" variant="danger" @click="$dispatch('open-modal', 'delete-task-{{ $task->id }}')">Delete</x-ui.button>
                @endcan
            @endunless
        </x-slot:actions>
    </x-ui.page-header>

    @can('delete', $task)
        <x-ui.delete-modal name="delete-task-{{ $task->id }}" :action="route('tasks.destroy', $task)" />
    @endcan

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-ui.card title="Details">
                @if ($task->description)
                    <p class="text-sm leading-relaxed text-slate-600">{{ $task->description }}</p>
                @else
                    <p class="text-sm text-slate-500">No description provided.</p>
                @endif
            </x-ui.card>

            @can('updateStatus', $task)
                <x-ui.card title="Update Status">
                    <form method="POST" action="{{ $statusRoute }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                        @csrf
                        @method('PATCH')
                        <div class="flex-1">
                            <x-ui.select label="Status" name="status" required>
                                @foreach (\App\Enums\TaskStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($task->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </x-ui.select>
                        </div>
                        <x-ui.button type="submit">Update Status</x-ui.button>
                    </form>
                </x-ui.card>
            @endcan
        </div>

        <x-ui.card title="Overview">
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-slate-500">Project</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $task->project?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Assigned To</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $task->assignee?->name ?? 'Unassigned' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Priority</dt>
                    <dd class="mt-1"><x-ui.priority-badge :priority="$task->priority" /></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Status</dt>
                    <dd class="mt-1"><x-ui.status-badge :status="$task->status" /></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Due Date</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $task->due_date?->format('M j, Y') ?? '—' }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>
</x-layouts.app>
