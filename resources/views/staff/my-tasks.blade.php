@php use App\Enums\TaskStatus; @endphp

<x-layouts.app title="My Tasks">
    <x-ui.page-header title="My Tasks" description="View and update your assigned tasks.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'My Tasks']]" />
        </x-slot:breadcrumb>
    </x-ui.page-header>

    <form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        <x-ui.search-box class="flex-1" placeholder="Search my tasks..." />
        <x-ui.select name="status" placeholder="All statuses" class="w-full sm:w-44">
            @foreach (TaskStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
    </form>

    @if ($tasks->count())
        <div class="space-y-4">
            @foreach ($tasks as $task)
                <div class="card p-5 transition hover:shadow-md">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <a href="{{ route('my-tasks.show', $task) }}" class="text-base font-semibold text-slate-900 hover:text-brand-600">{{ $task->title }}</a>
                            <p class="mt-1 text-sm text-slate-500">{{ $task->project?->name }} · Due {{ $task->due_date?->format('M j, Y') ?? 'No date' }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.priority-badge :priority="$task->priority" />
                            <x-ui.status-badge :status="$task->status" />
                        </div>
                    </div>
                    @can('updateStatus', $task)
                        <form method="POST" action="{{ route('my-tasks.update-status', $task) }}" class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-end">
                            @csrf
                            @method('PATCH')
                            <div class="flex-1">
                                <x-ui.select name="status" label="Quick status update">
                                    @foreach (TaskStatus::cases() as $status)
                                        <option value="{{ $status->value }}" @selected($task->status === $status)>{{ $status->label() }}</option>
                                    @endforeach
                                </x-ui.select>
                            </div>
                            <x-ui.button type="submit" size="sm">Update</x-ui.button>
                        </form>
                    @endcan
                </div>
            @endforeach
        </div>
        <div class="mt-6"><x-ui.pagination :paginator="$tasks" /></div>
    @else
        <x-ui.empty-state title="No assigned tasks" description="You don't have any tasks assigned to you yet." />
    @endif
</x-layouts.app>
