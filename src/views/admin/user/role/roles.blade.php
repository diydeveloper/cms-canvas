<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/user-group.png') }}"> Roles</h1>

        <div class="buttons">
            <a class="button" href="{{ Admin::url('user/role/add') }}"><span>Add Role</span></a>
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

        {{ Form::open(array('id' => 'role_delete_form')) }}
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
                    @if (count($roles) > 0)
                        @foreach ($roles as $role)
                            <tr>
                                <td class="center"><input type="checkbox" value="{{ $role->id }}" name="selected[]" /></td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->users()->count() }}</td>
                                <td class="right">[ <a href="{{ Admin::url("user/role/{$role->id}/edit") }}">Edit</a> ]</td>
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
            <div class="links">{{ $roles->links() }}</div>
            <div class="results">Showing {{ $roles->getFrom() }} to {{ $roles->getTo() }} of {{ $roles->getTotal() }} ({{ $roles->getLastPage() }}  Pages)</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#role_delete_form').attr('action', '{{ Admin::url('user/role/delete') }}').submit()
            } else {
                return false;
            }
        });
    });
</script>
