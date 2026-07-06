@php
    $p = $project ?? null;
    $selectedTeam = old('team_member_ids', $p?->users->pluck('id')->all() ?? []);
@endphp

<x-ui.card>
    <div class="grid gap-6 md:grid-cols-2">
        <x-ui.input label="Project Name" name="name" :value="old('name', $p?->name)" required />
        <x-ui.input label="Client Name" name="client_name" :value="old('client_name', $p?->client_name)" required />
        <x-ui.select label="Status" name="status" required>
            @foreach (\App\Enums\ProjectStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $p?->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-ui.select>
        <div></div>
        <x-ui.input label="Start Date" name="start_date" type="date" :value="old('start_date', $p?->start_date?->format('Y-m-d'))" required />
        <x-ui.input label="Due Date" name="due_date" type="date" :value="old('due_date', $p?->due_date?->format('Y-m-d'))" required />
    </div>

    <div class="mt-6">
        <x-ui.textarea label="Description" name="description" rows="5">{{ old('description', $p?->description) }}</x-ui.textarea>
    </div>

    <div class="mt-6">
        <label class="form-label">Assign Team Members</label>
        <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($staffMembers as $member)
                <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 transition hover:bg-slate-50">
                    <input
                        type="checkbox"
                        name="team_member_ids[]"
                        value="{{ $member->id }}"
                        @checked(in_array($member->id, $selectedTeam))
                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                    />
                    <span>
                        <span class="block text-sm font-medium text-slate-900">{{ $member->name }}</span>
                        <span class="block text-xs text-slate-500">{{ $member->email }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('team_member_ids')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</x-ui.card>
