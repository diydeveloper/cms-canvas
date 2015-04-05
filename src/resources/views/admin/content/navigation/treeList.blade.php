@foreach ($items as $item)
    <li id="list_{!! $item->id !!}">
        <div>
            <span class="sortableTree_handle"></span>
            {!! $item->title !!}
            <span style="float: right;">
                [ <a href="">Edit</a> ] 
                [ <a class="delete" href="">Delete</a> ]
            </span>
        </div>
        @if (count($item->children) > 0)
            <ol>
                @include('cmscanvas::admin.content.navigation.treeList', ['items' => $item->children])
            </ol>
        @endif
    </li>
@endforeach