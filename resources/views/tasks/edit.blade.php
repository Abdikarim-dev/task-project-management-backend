<x-layouts.app :title="'Edit: '.$task->title">
    <x-ui.page-header :title="'Edit: '.$task->title">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Tasks', 'url' => route('tasks.index')],
                ['label' => $task->title, 'url' => route('tasks.show', $task)],
                ['label' => 'Edit'],
            ]" />
        </x-slot:breadcrumb>
    </x-ui.page-header>

    <form method="POST" action="{{ route('tasks.update', $task) }}" class="mx-auto max-w-3xl">
        @csrf
        @method('PUT')
        @include('tasks._form', ['task' => $task, 'projects' => $projects, 'staffMembers' => $staffMembers])
        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button href="{{ route('tasks.show', $task) }}" variant="secondary">Cancel</x-ui.button>
            <x-ui.button type="submit">Save Changes</x-ui.button>
        </div>
    </form>
</x-layouts.app>
