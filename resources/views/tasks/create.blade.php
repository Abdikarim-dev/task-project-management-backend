<x-layouts.app title="Create Task">
    <x-ui.page-header title="Create Task" description="Add a new task and assign it to a team member.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Tasks', 'url' => route('tasks.index')],
                ['label' => 'Create'],
            ]" />
        </x-slot:breadcrumb>
    </x-ui.page-header>

    <form method="POST" action="{{ route('tasks.store') }}" class="mx-auto max-w-3xl">
        @csrf
        @include('tasks._form', ['projects' => $projects, 'staffMembers' => $staffMembers])
        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button href="{{ route('tasks.index') }}" variant="secondary">Cancel</x-ui.button>
            <x-ui.button type="submit">Create Task</x-ui.button>
        </div>
    </form>
</x-layouts.app>
