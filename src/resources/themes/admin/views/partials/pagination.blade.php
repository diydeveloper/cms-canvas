        <div class="pagination_footer">
            <div class="links">{!! $paginator->render() !!}</div>
            <div class="results">
                Showing {!! $paginator->total() ? ($paginator->currentPage() - 1) * $paginator->perPage() + 1 : 0 !!} 
                to {!! min($paginator->total(), $paginator->currentPage() * $paginator->perPage()) !!} 
                of {!! $paginator->total() !!} ({!! $paginator->lastPage() !!}  Pages)
            </div>
        </div>