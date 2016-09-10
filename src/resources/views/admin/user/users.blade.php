<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/user.png') !!}"> Users</h1>

        <div class="buttons">
            <a class="button" href="{!! Admin::url('user/add') !!}"><span>Add User</span></a>
            <a class="button delete" href="javascript:void(0);"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <div class="filter">
            {!! Form::model($filter) !!}
                <div class="left">
                    <div><label>Search:</label></div>
                    {!! Form::text('filter[search]') !!}
                </div>

                <div class="left">
                    <div><label>Role:</label></div> 
                    {!! Form::select('filter[role_id]', ['' => ''] + $roles->pluck('name', 'id')->all()) !!}
                </div>

                <div class="left filter_buttons">
                    <button type="submit" class="button"><span>Filter</span></button>
                    <button type="submit" class="button" name="clear_filter" value="1"><span>Clear</span></button>
                </div>
            {!! Form::close() !!}
            <div class="clear"></div>
        </div>

        {!! Form::open(array('id' => 'user_delete_form')) !!}
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center">
                            <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </th>
                        <th>
                            <a rel="first_name" class="sortable{!! $orderBy->getElementClass('first_name') !!}" href="javascript:void(0);">First Name</a>
                        </th>
                        <th>
                            <a rel="last_name" class="sortable{!! $orderBy->getElementClass('last_name') !!}" href="javascript:void(0);">Last Name</a>
                        </th>
                        <th>
                            <a rel="email" class="sortable{!! $orderBy->getElementClass('email') !!}" href="javascript:void(0);">Email</a>
                        </th>
                        <th>
                            Roles
                        </th>
                        <th>
                            <a rel="last_login" class="sortable{!! $orderBy->getElementClass('last_login') !!}" href="javascript:void(0);">Last Login</a>
                        </th>
                        <th class="right"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr class="row_link" data-href="{!! Admin::url("user/{$user->id}/edit") !!}">
                                <td class="center no_row_link"><input type="checkbox" value="{!! $user->id !!}" name="selected[]" /></td>
                                <td>{!! $user->first_name !!}</td>
                                <td>{!! $user->last_name !!}</td>
                                <td>{!! $user->email !!}</td>
                                <td>
                                    {!! implode(', ', $user->roles->pluck('name')->all()) !!}
                                </td>
                                <td>{!! (empty($user->last_login)) ? '' : Content::userDate($user->last_login) !!}</td>
                                <td class="right">
                                    <ul class="actions_btn">
                                        <li>
                                            <a class="actions_link no_row_link" href="javascript:void(0);">
                                                <span class="actions_arrow">Actions</span>
                                            </a>
                                            <ul class="actions_dropdown no_row_link" style="text-align: left;">
                                                <li class="edit_icon"><a href="{!! Admin::url("user/{$user->id}/edit") !!}">Edit</a></li>
                                                <li class="edit_icon"><a href="{!! Admin::url("user/{$user->id}/avatar") !!}">Update Avatar</a></li>
                                                <li><a href="javascript:void(0);" data-id="{!! $user->id !!}" data-href="{!! Admin::url('user/delete') !!}" class="delete_item">Delete</a></li>
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

        @include('theme::partials.pagination', ['paginator' => $users])
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#user_delete_form').attr('action', '{!! Admin::url('user/delete') !!}').submit()
            } else {
                return false;
            }
        });
    });
</script>
