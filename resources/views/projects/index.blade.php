@php use App\Enums\ProjectStatus; @endphp

<x-layouts.app title="Projects">
    <x-ui.page-header title="Projects" description="Manage client projects and team assignments.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Projects']]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-ui.button href="{{ route('projects.create') }}">
                <x-ui.icon name="plus" class="h-4 w-4" /> New Project
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            <x-ui.search-box class="flex-1" />
            <x-ui.select name="status" placeholder="All statuses" class="w-full sm:w-44">
                @foreach (ProjectStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
        </form>
    </div>

    @if ($projects->count())
        <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white lg:block">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3">Tasks</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($projects as $project)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $project) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $project->name }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->client_name }}</td>
                            <td class="px-6 py-4"><x-ui.status-badge :status="$project->status" /></td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->due_date->format('M j, Y') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->tasks_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-ui.dropdown>
                                    <x-ui.dropdown-item href="{{ route('projects.show', $project) }}"><x-ui.icon name="eye" class="h-4 w-4" /> View</x-ui.dropdown-item>
                                    <x-ui.dropdown-item href="{{ route('projects.edit', $project) }}"><x-ui.icon name="pencil" class="h-4 w-4" /> Edit</x-ui.dropdown-item>
                                    <button type="button" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50" @click="$dispatch('open-modal', 'delete-project-{{ $project->id }}')">
                                        <x-ui.icon name="trash" class="h-4 w-4" /> Delete
                                    </button>
                                </x-ui.dropdown>
                                <x-ui.delete-modal name="delete-project-{{ $project->id }}" :action="route('projects.destroy', $project)" title="Delete project" message="Are you sure you want to delete {{ $project->name }}? All related tasks will also be removed." />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 lg:hidden">
            @foreach ($projects as $project)
                <div class="card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900">{{ $project->name }}</a>
                            <p class="mt-1 text-sm text-slate-500">{{ $project->client_name }}</p>
                        </div>
                        <x-ui.status-badge :status="$project->status" />
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
                        <span>Due {{ $project->due_date->format('M j, Y') }}</span>
                        <span>{{ $project->tasks_count }} tasks</span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <x-ui.button href="{{ route('projects.show', $project) }}" variant="secondary" size="sm" class="flex-1">View</x-ui.button>
                        <x-ui.button href="{{ route('projects.edit', $project) }}" size="sm" class="flex-1">Edit</x-ui.button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <x-ui.pagination :paginator="$projects" />
        </div>
    @else
        <x-ui.empty-state title="No projects found" description="Create your first project to get started.">
            <x-slot:action>
                <x-ui.button href="{{ route('projects.create') }}">Create Project</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @endif
</x-layouts.app>
