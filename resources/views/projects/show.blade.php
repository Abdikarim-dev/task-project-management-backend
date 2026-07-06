<x-layouts.app :title="$project->name">
    <x-ui.page-header :title="$project->name" :description="$project->client_name">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Projects', 'url' => route('projects.index')],
                ['label' => $project->name],
            ]" />
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-ui.button href="{{ route('projects.edit', $project) }}" variant="secondary">
                <x-ui.icon name="pencil" class="h-4 w-4" /> Edit
            </x-ui.button>
            <x-ui.button type="button" variant="danger" @click="$dispatch('open-modal', 'delete-project-{{ $project->id }}')">
                <x-ui.icon name="trash" class="h-4 w-4" /> Delete
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.delete-modal name="delete-project-{{ $project->id }}" :action="route('projects.destroy', $project)" title="Delete project" :message="'Are you sure you want to delete '.$project->name.'?'" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-ui.card title="Project Details">
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div><dt class="text-slate-500">Status</dt><dd class="mt-1"><x-ui.status-badge :status="$project->status" /></dd></div>
                    <div><dt class="text-slate-500">Timeline</dt><dd class="mt-1 font-medium text-slate-900">{{ $project->start_date->format('M j, Y') }} – {{ $project->due_date->format('M j, Y') }}</dd></div>
                </dl>
                @if ($project->description)
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <p class="text-sm text-slate-600">{{ $project->description }}</p>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card title="Tasks ({{ $project->tasks->count() }})">
                @if ($project->tasks->count())
                    <div class="divide-y divide-slate-100">
                        @foreach ($project->tasks as $task)
                            <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                                <div>
                                    <a href="{{ route('tasks.show', $task) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $task->title }}</a>
                                    <p class="text-xs text-slate-500">{{ $task->assignee?->name ?? 'Unassigned' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ui.priority-badge :priority="$task->priority" />
                                    <x-ui.status-badge :status="$task->status" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state title="No tasks" description="No tasks have been added to this project yet." />
                @endif
            </x-ui.card>
        </div>

        <x-ui.card title="Team Members">
            @if ($project->users->count())
                <ul class="space-y-3">
                    @foreach ($project->users as $user)
                        <li class="flex items-center gap-3">
                            <x-ui.avatar>{{ $user->name }}</x-ui.avatar>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No team members assigned.</p>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
