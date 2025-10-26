@if ($paginator->hasPages())
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" id="editable_info" role="status" aria-live="polite">
                Menampilkan {{ $paginator->firstItem() ?? 0 }} sampai {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data
            </div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="editable_paginate">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="paginate_button page-item previous disabled" id="editable_previous">
                            <a href="#" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                        </li>
                    @else
                        <li class="paginate_button page-item previous" id="editable_previous">
                            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0" rel="prev">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="paginate_button page-item disabled">
                                <a href="#" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0">{{ $element }}</a>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="paginate_button page-item active">
                                        <a href="#" class="page-link" aria-controls="editable" data-dt-idx="{{ $page }}" tabindex="0">{{ $page }}</a>
                                    </li>
                                @else
                                    <li class="paginate_button page-item">
                                        <a href="{{ $url }}" class="page-link" aria-controls="editable" data-dt-idx="{{ $page }}" tabindex="0">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="paginate_button page-item next" id="editable_next">
                            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0" rel="next">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="paginate_button page-item next disabled" id="editable_next">
                            <a href="#" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif
