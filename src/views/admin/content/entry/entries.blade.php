<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/review.png') }}"> Entries</h1>

        <div class="buttons">
            <ul id="add_entry_btn">
                <li id="add_entry_li">
                    <a class="button" rel="#entry_content_types" id="add_entry" href="javascript:void(0);"><span>Add Entry</span><span class="button_down_arrow"></span></a>
                    <ul id="content_types_dropdown">
                        <li><div class="dropdown_heading">Content Types <span id="add_entry_close" class="dropdown_close"></span></div></li>
                        @if ( ! empty($contentTypes))
                            @foreach ($contentTypes as $contentType)
                                <li><a href="{{ Admin::url('content/type/'.$contentType->id.'/entry/add/') }}">{{ $contentType->title }}</a></li>
                            @endforeach
                        @else
                            <li><div id="no_content_types_added">No content types available</div></li>
                        @endif
                    </ul>
                </li>
                <li>
                    <a class="button delete" href="javascript:void(0);"><span>Delete</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="content">
        {{ Form::model($filter) }}
        <div class="filter">
            <div class="left">
                <div><label>Search:</label></div> 
                {{ Form::text('filter[search]') }}
            </div>

            <div class="left">
                <div><label>Content Type:</label></div> 
                {{ Form::select('filter[content_type_id]', ['' => ''] + $viewableContentTypes->lists('title', 'id')) }}
            </div>

            <div class="left">
                <div><label>Status:</label></div> 
                {{ Form::select('filter[entry_status_id]', ['' => ''] + $entryStatuses->lists('name', 'id')) }}
            </div>
            
            <div class="left filter_buttons">
                <button value="1" class="button" type="submit"><span>Filter</span></button>
                <button name="clear_filter" value="1" class="button" type="submit"><span>Clear</span></button>
            </div>
            <div class="clear"></div>
        </div>
        {{ Form::close() }}

        {{ Form::open(array('id' => 'form')) }}
        <table id="entries_table" class="list">
            <thead>
                <tr>
                    <th width="1" class="center">
                        <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                    </th>
                    <th>
                        <a rel="title" class="sortable{{ $orderBy->getElementClass('title') }}" href="javascript:void(0);">Title</a>
                    </th>
                    <th>
                        <a rel="route" class="sortable{{ $orderBy->getElementClass('route') }}" href="javascript:void(0);">Route</a>
                    </th>
                    <th class="right">
                        <a rel="id" class="sortable{{ $orderBy->getElementClass('id') }}" href="javascript:void(0);">#ID</a>
                    </th>
                    <th>
                        <a rel="content_type_title" class="sortable{{ $orderBy->getElementClass('content_type_title') }}" href="javascript:void(0);">Content Type</a>
                    </th>
                    <th>
                        <a rel="entry_status_name" class="sortable{{ $orderBy->getElementClass('entry_status_name') }}" href="javascript:void(0);">Status</a>
                    </th>
                    <th>
                        <a rel="updated_at" class="sortable{{ $orderBy->getElementClass('updated_at') }}" href="javascript:void(0);">Last Modified</a>
                    </th>
                    <th class="right">Action</th>
                </tr>
            </thead>
            <tbody>
                @if (count($entries) > 0)
                    @foreach ($entries as $entry)
                    <tr>
                        <td class="center"><input type="checkbox" value="<?php echo $entry->id ?>" name="selected[]" /></td>
                        <td>
                            {{ strip_tags($entry->title) }}
                            @if ($entry->isHomePage())
                                <span class="hint">Home Page</span>
                            @endif

                            @if ($entry->isCustom404Page())
                                <span class="hint">Custom 404</span>
                            @endif
                        </td>
                        <td>{{ $entry->getRoute() }}</td>
                        <td class="right">{{ $entry->id }}</td>
                        <td>{{ $entry->content_type_title }}</td>
                        <td>{{ $entry->entry_status_name }}</td>
                        <td>{{ $entry->updated_at->setTimezone(Auth::user()->getTimezoneIdentifier())->format('m/d/Y h:i a') }}</td>
                        <td class="right">
                            @if ($entry->route != '')
                                [ <a target="_blank" href="{{ url($entry->route) }}">View</a> ]
                            @endif
                            [ <a href="{{ Admin::url("content/type/{$entry->content_type_id}/entry/{$entry->id}/edit") }}">Edit</a> ]
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr class="center"><td colspan="8">No results found.</td></tr>
                @endif
            </tbody>
        </table>
        {{ Form::close() }}
        <div class="pagination_footer">
            <div class="links">{{ $entries->links() }}</div>
            <div class="results">Showing {{ $entries->getFrom() }} to {{ $entries->getTo() }} of {{ $entries->getTotal() }} ({{ $entries->getLastPage() }}  Pages)</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        // Delete
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#form').attr('action', '{{ Admin::url('content/entry/delete') }}').submit();
            } else {
                return false;
            }
        });

        $('#add_entry').click( function () {
            if ($('#content_types_dropdown').is(":visible"))
            {
                $('#add_entry_close').trigger("click");
            }
            else
            {
                $('#content_types_dropdown').show();
                $('#add_entry').addClass('selected');
            }
        });

        $('#add_entry_close').click( function () {
            $('#add_entry').removeClass('selected');
            $('#content_types_dropdown').hide();
        });

        $(document).mouseup( function (e) {
            if ($('#content_types_dropdown').is(":visible") && $(e.target).parents('#add_entry_li').length == 0) {
                $('#add_entry').removeClass('selected');
                $('#content_types_dropdown').hide();
            }
        });
    });
</script>
