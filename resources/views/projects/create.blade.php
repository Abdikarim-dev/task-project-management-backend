@php use App\Enums\ProjectStatus; @endphp

<x-layouts.app title="Create Project">
    <x-ui.page-header title="Create Project" description="Add a new project and assign team members.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Projects', 'url' => route('projects.index')],
                ['label' => 'Create'],
            ]" />
        </x-slot:breadcrumb>
    </x-ui.page-header>

    <form method="POST" action="{{ route('projects.store') }}" class="mx-auto max-w-3xl">
        @csrf
        @include('projects._form', ['staffMembers' => $staffMembers])
        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button href="{{ route('projects.index') }}" variant="secondary">Cancel</x-ui.button>
            <x-ui.button type="submit">Create Project</x-ui.button>
        </div>
    </form>
</x-layouts.app>
