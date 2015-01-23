<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/user.png') }}"> {{ ( ! empty($permission)) ? 'Edit' : 'Add' }} Permission</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#permission_edit_form').submit()"><span>Save</span></a>
            <a class="button" href="{{ Admin::url('user/permission') }}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        @if ( ! empty($permission))
            {{ Form::model($permission, array('id' => 'permission_edit_form')) }}
        @else
            {{ Form::open(array('id' => 'permission_edit_form')) }}
        @endif

            <div id="edit-permission-tab">
                <div class="form">
                    <div>
                        {{ HTML::decode(Form::label('name', '<span class="required">*</span> Name:')) }}
                        {{ Form::text('name') }}
                    </div>
                    <div>
                        {{ HTML::decode(Form::label('name', '<span class="required">*</span> Key Name:')) }}
                        {{ Form::text('key_name') }}
                    </div>
                </div>
            </div>

        <div class="clear"></div>

        {{ Form::close() }}
    </div>
</div>