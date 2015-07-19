@foreach ($items as $item)
    <li id="list_{!! $item->id !!}">
        <div>
            <span class="sortableTree_handle"></span>
            {!! $item->getTitle() !!}
            <span style="float: right;">
                [ <a href="{!! Admin::url("content/navigation/{$navigation->id}/item/{$item->id}/edit") !!}">Edit</a> ] 
                [ <a class="delete" href="{!! Admin::url("content/navigation/{$navigation->id}/item/{$item->id}/delete") !!}">Delete</a> ]
            </span>
        </div>
        @if (count($item->children) > 0)
            <ol>
                @include('cmscanvas::admin.content.navigation.treeList', ['items' => $item->children])
            </ol>
        @endif
    </li>
@endforeach