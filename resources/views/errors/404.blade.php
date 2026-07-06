<x-layouts.guest>
    <div class="card p-8 text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-600">
            <x-ui.icon name="search" class="h-7 w-7" />
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">404 — Page Not Found</h1>
        <p class="mt-2 text-sm text-slate-500">The page you are looking for does not exist or has been moved.</p>
        <x-ui.button href="{{ route('dashboard') }}" class="mt-6">Back to Dashboard</x-ui.button>
    </div>
</x-layouts.guest>
