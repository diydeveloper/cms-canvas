<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/layout.png') !!}"> Content Types</h1>

        <div class="buttons">
            <a class="button" href="{!! Admin::url('content/type/add') !!}"><span>Add Content Type</span></a>
            <a class="button delete" href="javascript:void(0);"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <div class="filter">
            {!! Form::model($filter, array('id' => 'filter_form')) !!}
                <div class="left">
                    <div><label>Search:</label></div>
                    {!! Form::text('filter[search]') !!}
                </div>

                <div class="left filter_buttons">
                    <button type="submit" class="button"><span>Filter</span></button>
                    <button type="submit" class="button" name="clear_filter" value="1"><span>Clear</span></button>
                </div>
            {!! Form::close() !!}
            <div class="clear"></div>
        </div>

        {!! Form::open(array('id' => 'content_type_form')) !!}
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center">
                            <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </th>
                        <th>
                            <a rel="title" class="sortable{!! $orderBy->getElementClass('title') !!}" href="javascript:void(0);">Title</a>
                        </th>
                        <th>
                            <a rel="short_name" class="sortable{!! $orderBy->getElementClass('short_name') !!}" href="javascript:void(0);">Short Name</a>
                        </th>
                        <th>
                            Number of Entries / Limit
                        </th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($contentTypes) > 0)
                        @foreach ($contentTypes as $contentType)
                            <tr>
                                <td class="center"><input type="checkbox" value="{!! $contentType->id !!}" name="selected[]" /></td>
                                <td>{!! $contentType->title !!}</td>
                                <td>{!! $contentType->short_name !!}</td>
                                <td>{!! $contentType->entries->count() !!} / {!! $contentType->entries_allowed or 'Unlimited' !!}</td>
                                <td class="right">
                                    [ <a href="{!! Admin::url("content/type/{$contentType->id}/field") !!}">Fields</a> ] 
                                    [ <a href="{!! Admin::url("content/type/{$contentType->id}/edit") !!}">Edit</a> ]
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="center">No results found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        {!! Form::close() !!}

        @include('theme::partials.pagination', ['paginator' => $contentTypes])
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#content_type_form').attr('action', '{!! Admin::url('content/type/delete') !!}').submit()
            } else {
                return false;
            }
        });
    });
</script>
