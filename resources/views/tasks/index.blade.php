@php
    use App\Enums\TaskPriority;
    use App\Enums\TaskStatus;
@endphp

<x-layouts.app title="Tasks">
    <x-ui.page-header title="Tasks" description="Track and manage all project tasks.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Tasks']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            @can('create', \App\Models\Task::class)
                <x-ui.button href="{{ route('tasks.create') }}">
                    <x-ui.icon name="plus" class="h-4 w-4" /> New Task
                </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <form method="GET" class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center">
        <x-ui.search-box class="flex-1" />
        <x-ui.select name="status" placeholder="All statuses" class="w-full lg:w-40">
            @foreach (TaskStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select name="priority" placeholder="All priorities" class="w-full lg:w-40">
            @foreach (TaskPriority::cases() as $priority)
                <option value="{{ $priority->value }}" @selected(request('priority') === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select name="sort" class="w-full lg:w-40">
            <option value="latest" @selected(request('sort', 'latest') === 'latest')>Latest</option>
            <option value="title" @selected(request('sort') === 'title')>Title</option>
            <option value="due_date" @selected(request('sort') === 'due_date')>Due Date</option>
            <option value="priority" @selected(request('sort') === 'priority')>Priority</option>
            <option value="status" @selected(request('sort') === 'status')>Status</option>
        </x-ui.select>
        <x-ui.button type="submit" variant="secondary">Apply</x-ui.button>
    </form>

    @if ($tasks->count())
        <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white lg:block">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Assigned</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($tasks as $task)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $task->title }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $task->project?->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $task->assignee?->name ?? '—' }}</td>
                            <td class="px-6 py-4"><x-ui.priority-badge :priority="$task->priority" /></td>
                            <td class="px-6 py-4"><x-ui.status-badge :status="$task->status" /></td>
                            <td class="px-6 py-4 text-slate-600">{{ $task->due_date?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-ui.dropdown>
                                    <x-ui.dropdown-item href="{{ route('tasks.show', $task) }}">View</x-ui.dropdown-item>
                                    @can('update', $task)
                                        <x-ui.dropdown-item href="{{ route('tasks.edit', $task) }}">Edit</x-ui.dropdown-item>
                                    @endcan
                                    @can('delete', $task)
                                        <button type="button" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50" @click="$dispatch('open-modal', 'delete-task-{{ $task->id }}')">Delete</button>
                                    @endcan
                                </x-ui.dropdown>
                                @can('delete', $task)
                                    <x-ui.delete-modal name="delete-task-{{ $task->id }}" :action="route('tasks.destroy', $task)" />
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 lg:hidden">
            @foreach ($tasks as $task)
                <div class="card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                            <p class="text-sm text-slate-500">{{ $task->project?->name }}</p>
                        </div>
                        <x-ui.status-badge :status="$task->status" />
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <x-ui.priority-badge :priority="$task->priority" />
                        <span class="text-xs text-slate-500">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                    </div>
                    <x-ui.button href="{{ route('tasks.show', $task) }}" variant="secondary" size="sm" class="mt-4 w-full">View Details</x-ui.button>
                </div>
            @endforeach
        </div>

        <div class="mt-6"><x-ui.pagination :paginator="$tasks" /></div>
    @else
        <x-ui.empty-state title="No tasks found" />
    @endif
</x-layouts.app>
