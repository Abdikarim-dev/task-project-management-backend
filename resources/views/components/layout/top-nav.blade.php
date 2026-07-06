<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-md sm:px-6">
    <div class="flex items-center gap-3">
        <button
            type="button"
            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 lg:hidden"
            @click="sidebarOpen = !sidebarOpen"
            aria-label="Toggle navigation"
        >
            <x-ui.icon name="menu" class="h-5 w-5" />
        </button>
        @isset($title)
            <h2 class="text-sm font-medium text-slate-500 lg:hidden">{{ $title }}</h2>
        @endisset
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('profile.edit') }}" class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 sm:flex">
            <x-ui.icon name="user" class="h-4 w-4" />
            Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="ghost" size="sm">
                <x-ui.icon name="logout" class="h-4 w-4" />
                <span class="hidden sm:inline">Sign out</span>
            </x-ui.button>
        </form>
    </div>
</header>
