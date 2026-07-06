@php use App\Enums\ProjectStatus; @endphp

<x-layouts.app title="Edit Project">
    <x-ui.page-header :title="'Edit: '.$project->name" description="Update project details and team assignments.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Projects', 'url' => route('projects.index')],
                ['label' => $project->name, 'url' => route('projects.show', $project)],
                ['label' => 'Edit'],
            ]" />
        </x-slot:breadcrumb>
    </x-ui.page-header>

    <form method="POST" action="{{ route('projects.update', $project) }}" class="mx-auto max-w-3xl">
        @csrf
        @method('PUT')
        @include('projects._form', ['project' => $project, 'staffMembers' => $staffMembers])
        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button href="{{ route('projects.show', $project) }}" variant="secondary">Cancel</x-ui.button>
            <x-ui.button type="submit">Save Changes</x-ui.button>
        </div>
    </form>
</x-layouts.app>
