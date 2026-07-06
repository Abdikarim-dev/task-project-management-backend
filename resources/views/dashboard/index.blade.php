@php
    use App\Enums\TaskStatus;
    use App\Enums\ProjectStatus;
@endphp

<x-layouts.app :title="$isAdmin ? 'Dashboard' : 'My Dashboard'">
    <x-ui.page-header
        :title="$isAdmin ? 'Dashboard' : 'My Dashboard'"
        :description="$isAdmin ? 'Overview of projects, tasks, and team activity.' : 'Your assigned work at a glance.'"
    >
        <x-slot:actions>
            @if ($isAdmin)
                <x-ui.button href="{{ route('projects.create') }}" variant="secondary">
                    <x-ui.icon name="plus" class="h-4 w-4" /> New Project
                </x-ui.button>
                <x-ui.button href="{{ route('tasks.create') }}">
                    <x-ui.icon name="plus" class="h-4 w-4" /> New Task
                </x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @if ($isAdmin)
            <x-ui.stat-card label="Total Projects" :value="$stats['total_projects']" icon="folder" color="brand" />
        @endif
        <x-ui.stat-card label="Active Tasks" :value="$stats['active_tasks']" icon="clipboard" color="brand" />
        <x-ui.stat-card label="Completed Tasks" :value="$stats['completed_tasks']" icon="check-circle" color="green" />
        <x-ui.stat-card label="Overdue Tasks" :value="$stats['overdue_tasks']" icon="exclamation" color="red" />
    </div>

  @if ($isAdmin)
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <x-ui.card title="Tasks by Status">
                <canvas id="tasksChart" height="220" aria-label="Tasks by status chart"></canvas>
            </x-ui.card>
            <x-ui.card title="Projects by Status">
                <canvas id="projectsChart" height="220" aria-label="Projects by status chart"></canvas>
            </x-ui.card>
        </div>
    @else
        <div class="mt-6">
            <x-ui.card title="My Tasks by Status">
                <canvas id="tasksChart" height="220" aria-label="Tasks by status chart"></canvas>
            </x-ui.card>
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <x-ui.card title="Recent Tasks">
            @if (count($stats['recent_tasks']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="pb-3 pr-4">Task</th>
                                <th class="pb-3 pr-4">Status</th>
                                <th class="pb-3">Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($stats['recent_tasks'] as $task)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 pr-4">
                                        <a href="{{ $isAdmin ? route('tasks.show', $task) : route('my-tasks.show', $task) }}" class="font-medium text-slate-900 hover:text-brand-600">
                                            {{ $task->title }}
                                        </a>
                                        <p class="text-xs text-slate-500">{{ $task->project?->name }}</p>
                                    </td>
                                    <td class="py-3 pr-4"><x-ui.status-badge :status="$task->status" /></td>
                                    <td class="py-3 text-slate-500">{{ $task->due_date?->format('M j, Y') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-ui.empty-state title="No tasks yet" description="Tasks will appear here as they are created." />
            @endif
        </x-ui.card>

        @if ($isAdmin && ! empty($stats['recent_projects']))
            <x-ui.card title="Recent Projects">
                <div class="space-y-3">
                    @foreach ($stats['recent_projects'] as $project)
                        <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-between rounded-lg border border-slate-200 p-4 transition hover:border-brand-200 hover:bg-brand-50/30">
                            <div>
                                <p class="font-medium text-slate-900">{{ $project->name }}</p>
                                <p class="text-xs text-slate-500">{{ $project->client_name }} · {{ $project->tasks_count }} tasks</p>
                            </div>
                            <x-ui.status-badge :status="$project->status" />
                        </a>
                    @endforeach
                </div>
            </x-ui.card>
        @elseif (! $isAdmin)
            <x-ui.card title="Quick Summary">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-slate-500">Assigned Tasks</dt>
                        <dd class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['total_tasks'] }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-slate-500">Pending</dt>
                        <dd class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['active_tasks'] }}</dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <x-ui.button href="{{ route('my-tasks.index') }}" variant="secondary" class="w-full">View all my tasks</x-ui.button>
                </div>
            </x-ui.card>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const taskData = @json($stats['tasks_by_status']);
                const taskLabels = @json(collect(TaskStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]));
                const tasksCtx = document.getElementById('tasksChart');
                if (tasksCtx) {
                    new Chart(tasksCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(taskData).map(k => taskLabels[k] ?? k),
                            datasets: [{
                                data: Object.values(taskData),
                                backgroundColor: ['#94a3b8', '#6366f1', '#10b981'],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } },
                        },
                    });
                }

                @if ($isAdmin)
                const projectData = @json($stats['projects_by_status'] ?? []);
                const projectLabels = @json(collect(ProjectStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]));
                const projectsCtx = document.getElementById('projectsChart');
                if (projectsCtx) {
                    new Chart(projectsCtx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(projectData).map(k => projectLabels[k] ?? k),
                            datasets: [{
                                label: 'Projects',
                                data: Object.values(projectData),
                                backgroundColor: '#6366f1',
                                borderRadius: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                        },
                    });
                }
                @endif
            });
        </script>
    @endpush
</x-layouts.app>
