<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/user-group.png') }}"> User Groups</h1>

        <div class="buttons">
            <a class="button" href="{{ Admin::url('user/group/add') }}"><span>Add Group</span></a>
            <a class="button delete" href="javascript:void(0);"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <div class="filter">
            {{ Form::model($filter, array('id' => 'filter_form')) }}
                <div class="left">
                    <div><label>Search:</label></div>
                    {{ Form::text('filter[search]') }}
                </div>

                <div class="left filter_buttons">
                    <button type="submit" class="button"><span>Filter</span></button>
                    <button type="submit" class="button" name="clear_filter" value="1"><span>Clear</span></button>
                </div>
            {{ Form::close() }}
            <div class="clear"></div>
        </div>

        {{ Form::open(array('id' => 'group_delete_form')) }}
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center">
                            <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </th>
                        <th>
                            <a rel="name" class="sortable{{ $orderBy->getElementClass('name') }}" href="javascript:void(0);">Name</a>
                        </th>
                        <th>
                            User Count
                        </th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($groups) > 0)
                        @foreach ($groups as $group)
                            <tr>
                                <td class="center"><input type="checkbox" value="{{ $group->id }}" name="selected[]" /></td>
                                <td>{{ $group->name }}</td>
                                <td>{{ $group->users()->count() }}</td>
                                <td class="right">[ <a href="{{ Admin::url("user/group/{$group->id}/edit") }}">Edit</a> ]</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="center">No results found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        {{ Form::close() }}

        <div class="pagination_footer">
            <div class="links">{{ $groups->links() }}</div>
            <div class="results">Showing {{ $groups->getFrom() }} to {{ $groups->getTo() }} of {{ $groups->getTotal() }} ({{ $groups->getLastPage() }}  Pages)</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#group_delete_form').attr('action', '{{ Admin::url('user/group/delete') }}').submit()
            } else {
                return false;
            }
        });
    });
</script>
