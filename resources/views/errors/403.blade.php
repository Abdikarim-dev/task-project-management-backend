@if (auth()->check())
    <x-layouts.app title="Forbidden">
        <div class="mx-auto max-w-lg py-16 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600">
                <x-ui.icon name="exclamation" class="h-7 w-7" />
            </div>
            <h1 class="text-2xl font-semibold text-slate-900">403 — Forbidden</h1>
            <p class="mt-2 text-sm text-slate-500">{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}</p>
            <x-ui.button href="{{ route('dashboard') }}" class="mt-6">Back to Dashboard</x-ui.button>
        </div>
    </x-layouts.app>
@else
    <x-layouts.guest>
        <div class="card p-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600">
                <x-ui.icon name="exclamation" class="h-7 w-7" />
            </div>
            <h1 class="text-2xl font-semibold text-slate-900">403 — Forbidden</h1>
            <p class="mt-2 text-sm text-slate-500">{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}</p>
            <x-ui.button href="{{ route('login') }}" class="mt-6">Sign In</x-ui.button>
        </div>
    </x-layouts.guest>
@endif
