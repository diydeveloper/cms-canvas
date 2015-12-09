@foreach ($items as $item)
    <li id="list_{!! $item->getNavigationItem()->id !!}" class="navigation_item">
        <div>
            <span class="sortableTree_handle"></span>
            {!! $item->getTitle() !!}
            <span style="float: right;">
                <ul class="actions_btn">
                    <li>
                        <a class="actions_link no_row_link" href="javascript:void(0);">
                            <span class="actions_arrow">Actions</span>
                        </a>
                        <ul class="actions_dropdown no_row_link" style="text-align: left;">
                            <li class="edit_icon"><a href="{!! Admin::url("content/navigation/{$navigation->id}/item/{$item->getNavigationItem()->id}/edit") !!}">Edit</a></li>
                            <li><a href="{!! Admin::url("content/navigation/{$navigation->id}/item/{$item->getNavigationItem()->id}/delete") !!}" class="delete">Delete</a></li>
                        </ul>
                    </li>
                </ul>
            </span>
        </div>
        @if (count($item->getChildren()) > 0)
            <ol>
                @include('cmscanvas::admin.content.navigation.treeList', ['items' => $item->getChildren()])
            </ol>
        @endif
    </li>
@endforeach