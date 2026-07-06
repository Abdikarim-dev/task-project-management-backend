@php $t = $task ?? null; @endphp

<x-ui.card>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <x-ui.input label="Title" name="title" :value="old('title', $t?->title)" required />
        </div>
        <x-ui.select label="Project" name="project_id" required>
            <option value="">Select project</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(old('project_id', $t?->project_id) == $project->id)>{{ $project->name }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select label="Assigned User" name="assigned_to">
            <option value="">Unassigned</option>
            @foreach ($staffMembers as $member)
                <option value="{{ $member->id }}" @selected(old('assigned_to', $t?->assigned_to) == $member->id)>{{ $member->name }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select label="Priority" name="priority" required>
            @foreach (\App\Enums\TaskPriority::cases() as $priority)
                <option value="{{ $priority->value }}" @selected(old('priority', $t?->priority?->value) === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select label="Status" name="status" required>
            @foreach (\App\Enums\TaskStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $t?->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.input label="Due Date" name="due_date" type="date" :value="old('due_date', $t?->due_date?->format('Y-m-d'))" />
    </div>
    <div class="mt-6">
        <x-ui.textarea label="Description" name="description" rows="5">{{ old('description', $t?->description) }}</x-ui.textarea>
    </div>
</x-ui.card>
