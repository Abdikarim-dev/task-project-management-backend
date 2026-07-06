@props(['action', 'title' => 'Delete item', 'message' => 'This action cannot be undone.'])

<x-ui.modal :name="$attributes->get('name')" :title="$title">
    <p>{{ $message }}</p>
    <x-slot:footer>
        <x-ui.button type="button" variant="secondary" @click="$dispatch('close-modal', '{{ $attributes->get('name') }}')">Cancel</x-ui.button>
        <form method="POST" action="{{ $action }}">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger">Delete</x-ui.button>
        </form>
    </x-slot:footer>
</x-ui.modal>
