@props(['active' => false])

@php
    $user = auth()->user();
    $links = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'admin' => true, 'staff' => true],
        ['route' => 'projects.index', 'label' => 'Projects', 'icon' => 'folder', 'admin' => true, 'staff' => false],
        ['route' => 'tasks.index', 'label' => 'Tasks', 'icon' => 'clipboard', 'admin' => true, 'staff' => false],
        ['route' => 'my-tasks.index', 'label' => 'My Tasks', 'icon' => 'clipboard', 'admin' => false, 'staff' => true],
    ];
@endphp

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-800/50 bg-sidebar transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
    aria-label="Main navigation"
>
    <div class="flex h-full flex-col">
        <div class="flex h-16 items-center gap-3 border-b border-slate-800/50 px-6">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">TP</div>
            <div>
                <p class="text-sm font-semibold text-white">TaskFlow</p>
                <p class="text-xs text-slate-400">Project Management</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            @foreach ($links as $link)
                @if (($user->isAdmin() && $link['admin']) || ($user->isStaff() && $link['staff']))
                    @php $isActive = request()->routeIs($link['route'].'*') || request()->routeIs($link['route']); @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        @class(['sidebar-link', 'sidebar-link-active' => $isActive])
                        @if($isActive) aria-current="page" @endif
                    >
                        <x-ui.icon :name="$link['icon']" class="h-5 w-5 shrink-0" />
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-slate-800/50 p-4">
            <div class="flex items-center gap-3 rounded-lg bg-slate-800/50 px-3 py-2.5">
                <x-ui.avatar>{{ $user->name }}</x-ui.avatar>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $user->isAdmin() ? 'Administrator' : 'Staff Member' }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>
