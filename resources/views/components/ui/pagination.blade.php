@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <p class="text-sm text-slate-500">
            Showing <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span>
            to <span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span>
            of <span class="font-medium text-slate-700">{{ $paginator->total() }}</span>
        </p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg px-3 py-2 text-sm text-slate-400">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100">Previous</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100">Next</a>
            @else
                <span class="rounded-lg px-3 py-2 text-sm text-slate-400">Next</span>
            @endif
        </div>
    </nav>
@endif
