<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/user.png') }}"> {{ ( ! empty($role)) ? 'Edit' : 'Add' }} Role</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#role_edit_form').submit()"><span>Save</span></a>
            <a class="button" href="{{ Admin::url('user/role') }}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        @if ( ! empty($role))
            {{ Form::model($role, array('id' => 'role_edit_form')) }}
        @else
            {{ Form::open(array('id' => 'role_edit_form')) }}
        @endif

            <div id="edit-role-tab">
                <div class="form">
                    <div>
                        {{ HTML::decode(Form::label('name', '<span class="required">*</span> Role Name:')) }}
                        {{ Form::text('name') }}
                    </div>

                    <div>
                        {{ Form::label('permissions', 'Permissions:', ['class' => 'valign_top']) }}
                        <div class="fields_wrapper">
                            <div class="scrollbox tall">
                            <?php $i = 0; ?>
                            @foreach ($permissions as $permission)
                                <div class="{{ ($i % 2 == 0) ? 'even' : 'odd' }}">
                                    <label>
                                    {{ 
                                        Form::checkbox(
                                            'role_permissions[]', 
                                            $permission->id, 
                                            (($role != null && $role->hasPermission($permission->key_name)) ? true : false)
                                        ) 
                                    }} 
                                    {{ $permission->name }}
                                    </label>
                                </div>
                                <?php $i++; ?>
                            @endforeach
                            </div>
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', true);">Select All</a> 
                            / 
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', false);">Unselect All</a>
                        </div>
                    </div>
                </div>
            </div>

        <div class="clear"></div>

        {{ Form::close() }}
    </div>
</div>