<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/user-group.png') !!}"> Permissions</h1>

        <div class="buttons">
            <a class="button" href="{!! Admin::url('user/permission/add') !!}"><span>Add Permission</span></a>
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

        {!! Form::open(array('id' => 'permission_delete_form')) !!}
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center">
                            <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </th>
                        <th>
                            <a rel="name" class="sortable{!! $orderBy->getElementClass('name') !!}" href="javascript:void(0);">Name</a>
                        </th>
                        <th>Key Name</th>
                        <th>Roles</th>
                        <th class="right"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($permissions) > 0)
                        @foreach ($permissions as $permission)
                            <tr class="row_link" data-href="{!! Admin::url("user/permission/{$permission->id}/edit") !!}">
                                <td class="center no_row_link">
                                    <?php if ($permission->editable_flag): ?>
                                    <input type="checkbox" value="{!! $permission->id !!}" name="selected[]" />
                                    <?php endif; ?>
                                </td>
                                <td>{!! $permission->name !!}</td>
                                <td>{!! $permission->key_name !!}</td>
                                <td>{!! implode(', ', $permission->roles->pluck('name')->all()) !!}</td>
                                <td class="right">
                                    <ul class="actions_btn">
                                        <li>
                                            <a class="actions_link no_row_link" href="javascript:void(0);">
                                                <span class="actions_arrow">Actions</span>
                                            </a>
                                            <ul class="actions_dropdown no_row_link" style="text-align: left;">
                                                <li class="edit_icon"><a href="{!! Admin::url("user/permission/{$permission->id}/edit") !!}">Edit</a></li>
                                                <?php if ($permission->editable_flag): ?>
                                                    <li><a href="javascript:void(0);" data-id="{!! $permission->id !!}" data-href="{!! Admin::url('user/permission/delete') !!}" class="delete_item">Delete</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </li>
                                    </ul>
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

        @include('theme::partials.pagination', ['paginator' => $permissions])
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#permission_delete_form').attr('action', '{!! Admin::url('user/permission/delete') !!}').submit()
            } else {
                return false;
            }
        });
    });
</script>
