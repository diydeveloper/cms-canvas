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
                </div>
            </div>

        <div class="clear"></div>

        {{ Form::close() }}
    </div>
</div>