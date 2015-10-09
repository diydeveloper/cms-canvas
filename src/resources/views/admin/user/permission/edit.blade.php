<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/user.png') !!}"> {!! ( ! empty($permission)) ? 'Edit' : 'Add' !!} Permission</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#permission_edit_form').submit()"><span>Save</span></a>
            <a class="button" href="{!! Admin::url('user/permission') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        @if ( ! empty($permission))
            {!! Form::model($permission, array('id' => 'permission_edit_form')) !!}
        @else
            {!! Form::open(array('id' => 'permission_edit_form')) !!}
        @endif

            <div id="edit-permission-tab">
                <div class="form">
                    <div>
                        {!! HTML::decode(Form::label('name', '<span class="required">*</span> Name:')) !!}
                        <?php if (! empty($permission) && $permission->editable_flag): ?>
                            {!! Form::text('name', null, ['id' => 'name']) !!}
                        <?php else: ?>
                            {!! $permission->name !!}
                        <?php endif; ?>
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('name', '<span class="required">*</span> Key Name:')) !!}
                        <?php if (! empty($permission) && $permission->editable_flag): ?>
                            {!! Form::text('key_name', null, ['id' => 'key_name']) !!}
                        <?php else: ?>
                            {!! $permission->key_name !!}
                        <?php endif; ?>
                    </div>

                    <div>
                        {!! Form::label('roles', 'Roles:') !!}
                        <div class="fields_wrapper">
                            <div class="scrollbox">
                            <?php $i = 0; ?>
                            @foreach ($roles as $role)
                                <div class="{!! ($i % 2 == 0) ? 'even' : 'odd' !!}">
                                    <label>
                                    {!! 
                                        Form::checkbox(
                                            'role_permissions[]', 
                                            $role->id, 
                                            (($permission != null && $permission->hasRole($role->name)) ? true : false)
                                        ) 
                                    !!} 
                                    {!! $role->name !!}
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

        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        @if (empty($permission))
        $('#name').keyup( function(e) {
            $('#key_name').val($(this).val().toUpperCase().replace(/\s+/g, '_').replace(/[^a-zA-Z0-9\-_]/g, ''))
        });
        @endif
    });
</script>