<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}"> Navigation Tree - {!! $navigation->title !!} ({!! $navigation->short_name !!})</h1>

        <div class="buttons">
            <a class="button" href="{!! Admin::url("content/navigation/$navigation->id/item/add") !!}" ><span>Add Item</span></a>
        </div>
    </div>

    <div id="tree_box" class="content" style="clear: both;">
        @if ( ! empty($navigationTree))
            <ol class="sortableTree">
                @include('cmscanvas::admin.content.navigation.treeList', ['items' => $navigationTree])
            </ol>
        @else
            <br/>
            <br/>
            <div class="align_center">No navigation items have been added.</div>
            <br/>
            <br/>
        @endif
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('ol.sortableTree').nestedSortable({
            disableNesting: 'no-nest',
            forcePlaceholderSize: true,
            handle: 'div span.sortableTree_handle',
            helper: 'clone',
            items: 'li.navigation_item',
            maxLevels: 50,
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            update: function(event, ui) { 
                show_status('Saving...', false, true);
                dataString = $('ol.sortableTree').nestedSortable('serialize');
                $.ajax({  
                    type: "POST",  
                    url: "{!! Admin::url('content/navigation/'.$navigation->id.'/tree') !!}",  
                    data: dataString,  
                    success: function(html) {  
                        show_status('Saved', true, false);
                    },
                    error:function (xhr, ajaxOptions, thrownError){
                        hide_status();
                        alert('Status: ' + xhr.responseText);
                    }   
                });   
            }
        });

        // Delete listner
        $('.delete').click(function() {
            return confirm('Are you sure you want to delete this item and any children items it may have?');
        });
    });
</script>
