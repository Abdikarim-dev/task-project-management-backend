<x-layouts.guest>
    <div class="card overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-8 py-10 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-xl font-bold text-white backdrop-blur">TP</div>
            <h1 class="text-2xl font-semibold text-white">Welcome back</h1>
            <p class="mt-2 text-sm text-brand-100">Sign in to your TaskFlow workspace</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5 p-8" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <x-ui.input
                label="Email address"
                name="email"
                type="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />

            <x-ui.input
                label="Password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
            />

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" />
                    Remember me
                </label>
            </div>

            <x-ui.button type="submit" class="w-full" ::disabled="loading">
                <span x-show="!loading">Sign in</span>
                <span x-show="loading" x-cloak>Signing in...</span>
            </x-ui.button>
        </form>
    </div>

    <p class="mt-6 text-center text-xs text-slate-300">
        Demo: admin@example.com / password
    </p>
</x-layouts.guest>
