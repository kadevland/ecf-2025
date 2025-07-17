@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content/50 bg-base-100 border border-base-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content bg-base-100 border border-base-300 leading-5 rounded-md hover:bg-base-200 focus:outline-none focus:ring ring-primary/30 focus:border-primary active:bg-base-200 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-base-content bg-base-100 border border-base-300 leading-5 rounded-md hover:bg-base-200 focus:outline-none focus:ring ring-primary/30 focus:border-primary active:bg-base-200 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-base-content/50 bg-base-100 border border-base-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-base-content/70 leading-5">
                    {!! __('Affichage de') !!}
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    {!! __('à') !!}
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    {!! __('résultats') !!}
                </p>
            </div>

            <div class="flex items-center space-x-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content/50 bg-base-100 border border-base-300 cursor-default rounded-md leading-5" aria-disabled="true">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {!! __('Précédent') !!}
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content bg-base-100 border border-base-300 rounded-md leading-5 hover:bg-base-200 focus:outline-none focus:ring ring-primary/30 focus:border-primary active:bg-base-200 transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {!! __('Précédent') !!}
                    </a>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content bg-base-100 border border-base-300 rounded-md leading-5 hover:bg-base-200 focus:outline-none focus:ring ring-primary/30 focus:border-primary active:bg-base-200 transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                        {!! __('Suivant') !!}
                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-content/50 bg-base-100 border border-base-300 cursor-default rounded-md leading-5" aria-disabled="true">
                        {!! __('Suivant') !!}
                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif