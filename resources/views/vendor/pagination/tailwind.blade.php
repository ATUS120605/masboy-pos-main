@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="mt-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                @if ($paginator->firstItem())
                    Menampilkan <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
                    - <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
                    dari <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
            </p>

            <div class="flex items-center gap-1.5">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm text-slate-600 bg-white border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-900 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm text-slate-400 bg-white border border-slate-200">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-bold text-white bg-emerald-900 border border-emerald-900">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm text-slate-600 bg-white border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-900 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm text-slate-600 bg-white border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-900 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
